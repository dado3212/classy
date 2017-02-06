<?php
  include_once("./secret.php");
  include_once("./util.php");

  error_reporting(-1);
  ini_set('display_errors', 'On');

  $PDO = createConnection();

  $term = "17S";

  function canTake($class, $allClasses) {
    if ($allClasses == null) return true; // handle if session token not passed

    foreach ($allClasses as $testClass) {
      if ($testClass["department"] == $class["department"] && $testClass["number"] == $class["number"]) {
        return false;
      }
    }
    return true;
  }

  function getMatches($criteria, $classes) {
    global $PDO;
    global $term;

    // Get all the criteria properly sorted (indiscriminate of weight)
    $allDepts = [];
    $allDistribs = [];
    $allPeriods = [];
    foreach ($criteria as $criterion) {
      if ($criterion["type"] == "departments") {
        foreach ($criterion["value"] as $dept) {
          if (!in_array($dept, $allDepts)) {
            $allDepts[] = $dept;
          }
        }
      } else if ($criterion["type"] == "distributives") {
        foreach ($criterion["value"] as $distrib) {
          if (!in_array($distrib, $allDistribs)) {
            $allDistribs[] = $distrib;
          }
        }
      } else if ($criterion["type"] == "periods") {
        foreach ($criterion["value"] as $period) {
          if (!in_array($period, $allPeriods)) {
            $allPeriods[] = $period;
          }
        }
      }
    }

    // Get all the current classes in the term that meet the base criteria
    $stmt = $PDO->prepare("SELECT * FROM timetable WHERE `term`='$term' AND `number` < 100");
    $stmt->execute();

    $rawClasses = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $masterList = [];
    $weighting = [];

    foreach ($rawClasses as $rawClass) {
      // If it meets at least some of the criteria
      if (
        (in_array($rawClass["department"], $allDepts) || 
        count(array_intersect($allDistribs, json_decode($rawClass["distrib"]))) > 0 || 
        in_array($rawClass["period"], $allPeriods)) &&
        canTake($rawClass, $classes)
      ) {
        // Add the class to the list
        $masterList[$rawClass["id"]] = $rawClass;
        $weighting[$rawClass["id"]] = 0;

        // Figure out its worth
        foreach ($criteria as $criterion) {
          if ($criterion["type"] == "departments") {
            foreach ($criterion["value"] as $dept) {
              if ($dept == $rawClass["department"]) {
                $weighting[$rawClass["id"]] += $criterion["weight"];
              }
            }
          } else if ($criterion["type"] == "distributives") {
            $weighting[$rawClass["id"]] += $criterion["weight"] * count(array_intersect($criterion["value"], json_decode($rawClass["distrib"])));
          } else if ($criterion["type"] == "periods") {
            foreach ($criterion["value"] as $period) {
              if ($period == $rawClass["period"]) {
                $weighting[$rawClass["id"]] += $criterion["weight"];
              }
            }
          }
        }
      }
    }

    // Sort by highest weight
    arsort($weighting);

    return [
      "list" => $masterList,
      "weights" => $weighting,
    ];
  }

  /**
   *  Given your ordered departments, distribs, periods, and overall weighting, returns your main classes
   *
   */
  function mainClasses($info) {
    global $PDO;
    global $departments;

    $masterList = $info["list"];
    $weighting = $info["weights"];

    $count = 0;

    $results = [];

    foreach ($weighting as $key => $value) {
      if ($count < 25) {
        $class = $masterList[$key];

        $stmt = $PDO->prepare("SELECT * FROM orc WHERE department=:dept AND number=:number");
        $stmt->bindParam(":dept", $class['department'], PDO::PARAM_STR);
        $stmt->bindParam(":number", $class['number'], PDO::PARAM_STR);
        $stmt->execute();
        $orc_data = $stmt->fetch();

        // Get the median
        $medianSTMT = $PDO->prepare("SELECT median FROM medians WHERE department=:dept AND number=:number");
        $medianSTMT->bindParam(":dept", $class['department'], PDO::PARAM_STR);
        $medianSTMT->bindParam(":number", $class['number'], PDO::PARAM_STR);
        $medianSTMT->execute();

        $median = $medianSTMT->fetch(PDO::FETCH_ASSOC);
        $median = (isset($median["median"]) ? $median["median"] : "N/A");
        $prereqs = preg_replace('/The Timetable of Class Meetings contains.*/', '', $orc_data["prereqs"]);

        $results[] = [
          "department" => $departments[$class["department"]],
          "class" => $class["number"],
          "title" => $class["title"],
          "period" => $class["period"],
          "culture" => $class["culture"],
          "distribs" => json_decode($class["distrib"]),
          "prereqs" => $prereqs,
          "median" => $median,
          "teacher" => $class["teacher"],
          "description" => $class["description"],
        ];

        $count++;
      }
    }

    return $results;
  }

  if (isset($_POST["criteria"])) {
    $sessid = $_POST["sessid"];

    if ($sessid) {
      $classes = getClasses($_POST["sessid"]);
      if ($classes == null) {
        echo "Sign in again, token no longer valid.";
        die();
      }
    } else {
      $classes = null;
    }

    $matchesInformation = getMatches($_POST["criteria"], $classes);
    echo json_encode(mainClasses($matchesInformation));
  }
?>
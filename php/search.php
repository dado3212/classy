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
    $allMedians = [];
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
      } else if ($criterion["type"] == "medians") {
        foreach ($criterion["value"] as $median) {
          if (!in_array($median, $allMedians)) {
            $allMedians[] = $median;
          }
        }
      }
    }

    // Get all the current classes
    $stmt = $PDO->prepare("
      SELECT timetable.*, medians.median, orc.prereqs FROM timetable
      LEFT JOIN medians
      ON timetable.department = medians.department AND timetable.`number` = medians.`number`
      LEFT JOIN orc
      ON timetable.department = orc.department AND timetable.`number` = orc.`number`
      WHERE timetable.term = '$term' AND timetable.`number` < 100");
    $stmt->execute();

    $rawClasses = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $results = [];

    foreach ($rawClasses as $rawClass) {
      // If it meets at least some of the criteria
      if (
        (in_array($rawClass["department"], $allDepts) || 
        count(array_intersect($allDistribs, json_decode($rawClass["distrib"]))) > 0 || 
        in_array($rawClass["culture"], $allDistribs) || 
        in_array($rawClass["period"], $allPeriods) || 
        in_array($rawClass["median"], $allMedians)) &&
        canTake($rawClass, $classes)
      ) {
        // Add the class to the list
        $results[$rawClass["id"]] = [
          "info" => $rawClass,
          "weight" => 0,
        ];

        // Figure out its worth
        foreach ($criteria as $criterion) {
          if ($criterion["type"] == "departments") {
            foreach ($criterion["value"] as $dept) {
              if ($dept == $rawClass["department"]) {
                $results[$rawClass["id"]]["weight"] += $criterion["weight"];
              }
            }
          } else if ($criterion["type"] == "distributives") {
            foreach ($criterion["value"] as $distrib) {
              if (in_array($distrib, json_decode($rawClass["distrib"])) || $distrib == $rawClass["culture"]) {
                $results[$rawClass["id"]]["weight"] += $criterion["weight"];
              }
            }
          } else if ($criterion["type"] == "periods") {
            foreach ($criterion["value"] as $period) {
              if ($period == $rawClass["period"]) {
                $results[$rawClass["id"]]["weight"] += $criterion["weight"];
              }
            }
          } else if ($criterion["type"] == "medians") {
            foreach ($criterion["value"] as $median) {
              if ($median == $rawClass["median"]) {
                $results[$rawClass["id"]]["weight"] += $criterion["weight"];
              }
            }
          }
        }
      }
    }

    // Sort by highest weight, then by median
    usort($results, function ($a, $b) {
      global $medians;

      $a_median = ($a["info"]["median"] ? $medians[$a["info"]["median"]] : -0.5);
      $b_median = ($b["info"]["median"] ? $medians[$b["info"]["median"]] : -0.5);

      if ($b["weight"] == $a["weight"]) {
        if ($b_median == $a_median) {
          return 0;
        } else {
          return ($b_median - $a_median < 0 ? -1 : 1);
        }
      } else {
        return ($b["weight"] - $a["weight"] < 0) ? -1 : 1;
      }
    });

    return array_map(function ($a) { return $a["info"]; }, $results);
  }

  /**
   *  Given your ordered departments, distribs, periods, and overall weighting, returns your main classes
   *
   */
  function mainClasses($classes) {
    global $PDO;
    global $departments;

    $count = 0;

    $results = [];

    foreach ($classes as $class) {
      if ($count < 25) {
        $median = (isset($class["median"]) ? $class["median"] : "N/A");
        $prereqs = preg_replace('/The Timetable of Class Meetings contains.*/', '', $class["prereqs"]);
        $distribs = json_decode($class["distrib"]);
        if ($class["culture"] != "") { $distribs[] = $class["culture"]; }

        $results[] = [
          "department" => $departments[$class["department"]],
          "class" => $class["number"],
          "title" => $class["title"],
          "period" => $class["period"],
          "distribs" => $distribs,
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
    $classes = null;

    if ($sessid) {
      $classes = getClasses($_POST["sessid"]);
    }

    $matchesInformation = getMatches($_POST["criteria"], $classes);
    echo json_encode(mainClasses($matchesInformation));
  }
?>
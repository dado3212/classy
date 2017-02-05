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
  function formatMatches($info) {
    global $PDO;
    global $departments;

    $masterList = $info["list"];
    $weighting = $info["weights"];

    $count = 0;

    foreach ($weighting as $key => $value) {
      if ($count < 25) {
        $class = $masterList[$key];

        $stmt = $PDO->prepare("SELECT * FROM orc WHERE department=:dept AND number=:number");
        $stmt->bindParam(":dept", $class['department'], PDO::PARAM_STR);
        $stmt->bindParam(":number", $class['number'], PDO::PARAM_STR);
        $stmt->execute();
        $data = $stmt->fetch();

        echo "<div class='class'>";
        echo "<h1><strong>{$departments[$class['department']]} {$class['number']}</strong> - {$class['title']}</h1>";
        echo "<span class='right'>";

        echo "<span>Period: {$class['period']}</span>";
        if ($class['culture'] == "")
          echo "<span>Culture: N/A</span>";
        else
          echo "<span>Culture: {$class['culture']}</span>";
        if ($class['distrib'] == [])
          echo "<span>Distribs: N/A</span>";
        else
          echo "<span>Distribs: {$class['distrib']}</span>";
        echo "<span>Prereqs: {$data['prereqs']}</span>";

        echo "</span>";
        echo "<span class='teacher'>{$class['teacher']}</span>";

        $description = $class['description'];
        if ($description != "")
          echo "<p class='description'>{$class['description']}</p>";
        else if ($class['crosslisted'] != "[]"){
          $stmt = $PDO->prepare("SELECT * FROM orc WHERE department=:dept AND number=:number");
          $stmt->bindParam(":dept", explode(" ", json_decode($class['crosslisted'], true)[0])[0], PDO::PARAM_STR);
          $stmt->bindParam(":number", explode(" ", json_decode($class['crosslisted'], true)[0])[1], PDO::PARAM_STR);
          $stmt->execute();
          $data = $stmt->fetch();
          echo "<p class='description'>{$data['description']}</p>";
        } else {
          echo "<p class='description'>No description.</p>";
        }

        echo "</div>";
        $count++;
      }
    }
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
    formatMatches($matchesInformation);
  }
?>
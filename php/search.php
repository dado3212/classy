<?php
  include_once("./secret.php");
  include_once("./util.php");

  $PDO = createConnection();

  $term = "S2017";

  function test($depts, $distribs, $periods, $overall, $sessid) {
    // Get all the current
  }

  /**
   *  Given your ordered departments, distribs, periods, and overall weighting, returns your main classes
   *
   */
  function getMatches($depts, $distribs, $periods, $overall, $prereqs) {
    global $PDO;
    global $departments;

    $masterList = [];
    $weighting = [];

    // Search and aggregate by categories
    getDept($masterList, $weighting, $depts, $overall);
    getDistrib($masterList, $weighting, $distribs, $overall);
    getPeriod($masterList, $weighting, $periods, $overall);

    arsort($weighting);

    //$weighting = array_slice($weighting, 0, 15, true);

    $count = 0;

    foreach ($weighting as $key => $value) {
      if ($count < 25) {
        $class = $masterList[$key];

        $stmt = $PDO->prepare("SELECT * FROM orc WHERE department=:dept AND number=:number");
        $stmt->bindParam(":dept", $class['department'], PDO::PARAM_STR);
        $stmt->bindParam(":number", $class['number'], PDO::PARAM_STR);
        $stmt->execute();
        $data = $stmt->fetch();

        //if ($data['prereqs'] == "") {
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
        //}
      }
    }
  }

  if (isset($_POST["depts"])) {
    echo "<pre>" . var_export($_POST, true) . "</pre>";
    $classes = getClasses($_POST["sessid"]);
    echo "<pre>" . var_export($classes, true) . "</pre>";
  }

  // getMatches();
?>
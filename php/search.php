<?php
  include_once("./secret.php");
  include_once("./util.php");

  $PDO = createConnection();

  $term = "S2017";

  /**
   *  Gets all department matches
   *
   *  @param &$masterList: 
   */
  function getDept(&$masterList, &$weighting, $depts, $overall) {
    global $PDO;
    global $term;

    foreach ($depts as $key => $chunk) {
      if (count($chunk) > 0) {
        $weight = (count($depts) - $key) / count($depts) + ($overall["depts"] - 1);

        $query = "
          SELECT *
          FROM timetable
          WHERE `term`='$term' AND number < 100 AND (
        ";

        // Handle all periods
        foreach ($chunk as $key => $period) {
          $query .= "department = ? OR ";
        }
        $query = substr($query, 0, -4);
        $query .= ")";

        $stmt = $PDO->prepare($query);

        for ($i = 0; $i < count($chunk); $i++) {
          $stmt->bindParam($i+1, $chunk[$i], PDO::PARAM_STR);
        }

        $stmt->execute();

        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
        foreach ($data as $value) {
          $masterList[$value['id']] = $value;
          if (array_key_exists($value['id'], $weighting))
            $weighting[$value['id']] += $weight;
          else
            $weighting[$value['id']] = $weight;
        }
      }
    }
  }

  function getDistrib(&$masterList, &$weighting, $distribs, $overall) {
    global $PDO;
    global $term;

    foreach ($distribs as $key => $chunk) {
      if (count($chunk) > 0) {
        $weight = (count($distribs) - $key) / count($distribs) + ($overall["distribs"] - 1);

        $query = "
          SELECT *
          FROM timetable
          WHERE `term`='$term' AND number < 100 AND (
        ";

        // Handle all periods
        foreach ($chunk as $key => $period) {
          $query .= "culture = :culture{$key} OR json_contains(distrib, :distrib{$key}) OR ";
        }
        $query = substr($query, 0, -4);
        $query .= ")";

        $stmt = $PDO->prepare($query);

        for ($i = 0; $i < count($chunk); $i++) {
          $stmt->bindParam(':culture' . $i, $chunk[$i], PDO::PARAM_STR);
          $d = '"' . $chunk[$i] . '"';
          $stmt->bindParam(':distrib' . $i, $d, PDO::PARAM_STR);
        }

        $stmt->execute();

        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
        foreach ($data as $value) {
          $masterList[$value['id']] = $value;
          if (array_key_exists($value['id'], $weighting))
            $weighting[$value['id']] += $weight;
          else
            $weighting[$value['id']] = $weight;
        }
      }
    }
  }

  function getPeriod(&$masterList, &$weighting, $periods, $overall) {
    global $PDO;
    global $term;

    // For each of the given periods, separate them into their respective chunks
    foreach ($periods as $key => $chunk) {
      if (count($chunk) > 0) {
        // Calculate a weight based on the overall ranking
        $weight = (count($periods) - $key) / count($periods) + ($overall["periods"] - 1);
        // Construct the query
        $query = "
          SELECT *
          FROM timetable
          WHERE `term`='$term' AND number < 100 AND (
        ";

        // Handle all periods
        foreach ($chunk as $key => $period) {
          $query .= "period = ? OR ";
        }
        $query = substr($query, 0, -4);
        $query .= ")";

        $stmt = $PDO->prepare($query);
        
        for ($i = 0; $i < count($chunk); $i++) {
          $stmt->bindParam($i+1, $chunk[$i], PDO::PARAM_STR);
        }

        $stmt->execute();

        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
        foreach ($data as $value) {
          $masterList[$value['id']] = $value;
          if (array_key_exists($value['id'], $weighting))
            $weighting[$value['id']] += $weight;
          else
            $weighting[$value['id']] = $weight;
        }
      }
    }
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

  getMatches();
?>
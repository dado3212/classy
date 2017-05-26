<?php
  include_once("php/util.php");
?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <title>Classy</title>

    <!-- Meta tags -->
    <meta name="robots" content="index, follow, archive">
    <meta name="description" content="Easily search by department, distribs, periods, and medians to find the perfect class.">
    <meta charset="utf-8" />
    <meta http-equiv="Cache-control" content="public">
    <!-- old -->
    <meta name="google-site-verification" content="9U8kYC24rUGX98pnl1EDy0A4tY4EE8DxFAvpPwNNAEs" />
    <!-- new -->
    <meta name="google-site-verification" content="SmeVIjL9gg0LUuOrJ-ozBhozzBg0ZpmSZ_u86do4Y7U" />

    <!-- Semantic Markup -->
    <meta property="og:title" content="Classy">
    <meta property="og:type" content="website">
    <meta property="og:image" content="https://alexbeals.com/projects/classes/images/header_dark.jpg">
    <meta property="og:url" content="https://alexbeals.com/projects/classes">
    <meta property="og:description" content="Easily search by department, distribs, periods, and medians to find the perfect class.">

    <meta name="twitter:card" content="summary">
    <meta name="twitter:creator" content="@alex_beals">

    <!-- Google Analytics -->
    <script>
      (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
      (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
      m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
      })(window,document,'script','//www.google-analytics.com/analytics.js','ga');
      ga('create', 'UA-70745807-1', 'auto');
      ga('send', 'pageview');
    </script>

    <!-- Favicons -->
    <link rel="apple-touch-icon" sizes="180x180" href="images/favicons/apple-touch-icon.png">
    <link rel="icon" type="image/png" href="images/favicons/favicon-32x32.png" sizes="32x32">
    <link rel="icon" type="image/png" href="images/favicons/favicon-16x16.png" sizes="16x16">
    <link rel="manifest" href="images/favicons/manifest.json">
    <link rel="mask-icon" href="images/favicons/safari-pinned-tab.svg" color="#222222">
    <link rel="shortcut icon" href="images/favicons/favicon.ico">
    <meta name="msapplication-config" content="images/favicons/browserconfig.xml">
    <meta name="theme-color" content="#ffffff">

    <?php
      if (preg_match("/(iPhone|iPod|iPad|Android|BlackBerry|Mobile)/i", $_SERVER['HTTP_USER_AGENT'])) {
        ?><meta name="viewport" content="width=570"><?php
      }
    ?>
    <script>
      <?php
        // Pull in criteria from live-updating URL
        $criteria = [];
        // Check that the query parameters are in the right form
        if (
          isset($_GET['t']) && isset($_GET['c']) && isset($_GET['p']) && // exist
          (count($_GET['t']) == count($_GET['c']) && count($_GET['c']) == count($_GET['p'])) // same length
        ) {
          for ($i = 0; $i < count($_GET['t']); $i++) {
            $c = [];
            $c['type'] = $_GET['t'][$i];
            $c['choices'] = explode(",", $_GET['c'][$i]);
            $c['points'] = $_GET['p'][$i];

            // Convert to numeric for choices
            for ($j = 0; $j < count($c['choices']); $j++) {
              if (!is_numeric($c['choices'][$j])) {
                break;
              }
              $c['choices'][$j] = (int)$c['choices'][$j];
            }

            // Conver to numeric for type, points
            if (is_numeric($c['type']) && is_numeric($c['points'])) {
              $c['type'] = (int)$c['type'];
              $c['points'] = (int)$c['points'];
              $criteria[] = $c;
            }
          }
        }

        $departmentOptions = "<optgroup disabled hidden></optgroup>";
        foreach ($departments as $code => $name) {
          $departmentOptions .= "<option value='$code'>$name ($code)</option>";
        }

        $distributiveOptions = "<optgroup disabled hidden></optgroup>";
        foreach ($distribs as $code => $name) {
          $distributiveOptions .= "<option value='$code'>$name</option>";
        }

        $periodOptions = "<optgroup disabled hidden></optgroup>";
        foreach ($periods as $code => $name) {
          $periodOptions .= "<option value='$code'>$name</option>";
        }

        $medianOptions = "<optgroup disabled hidden></optgroup>";
        foreach ($medians as $code => $value) {
          $medianOptions .= "<option value='$code'>$code</option>";
        }
      ?>
      var criteria = <?php echo json_encode($criteria); ?>;

      var departmentOptions = "<?php echo $departmentOptions; ?>;";
      var distributiveOptions = "<?php echo $distributiveOptions; ?>;";
      var periodOptions = "<?php echo $periodOptions; ?>";
      var medianOptions = "<?php echo $medianOptions; ?>";

      var departments = <?php echo json_encode($departments); ?>;
      var distributives = <?php echo json_encode($distribs); ?>;
      var periods = <?php echo json_encode($periods); ?>;
      var medians = <?php echo json_encode($medians); ?>;
    </script>

    <script src="//ajax.googleapis.com/ajax/libs/jquery/3.1.0/jquery.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script src="//cdnjs.cloudflare.com/ajax/libs/chosen/1.6.2/chosen.jquery.min.js"></script>
    <script src="js/main.js"></script>

    <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,700" rel="stylesheet">
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/chosen/1.6.2/chosen.min.css">
    <link rel="stylesheet" type="text/css" href="css/main.css">
  </head>
  <body>
    <div class="container">
      <div id="search">
        <form>
          <img class="header" src="images/header.png" alt="Logo">

          <p class="overview">
            Classy is a way of searching for classes based on departments, distribs, periods, and medians.  It allows you to find the best fits for <i>you</i> based on your priorities in a course.
          </p>

          <h4>Criteria and Points Explanation</h4>
          <div>
            <p>
              You can build a search by using criteria.  You can add a criteria for the <strong>department</strong>, <strong>distrib</strong>, <strong>period</strong>, or <strong>median</strong>.  For each criteria, you can then choose one or more of the choices, and give it a number of points.  Classes that meet elements of each criteria will be given the number of points for that criteria, and the top classes will be returned, sorted by median.  If you're only searching for one criteria, the number of points is irrelevant.
            </p>
            <?php if (count($criteria) == 0) { ?>
            <p>
              A sample search is displayed below, with an ECON class being most important (3 points), the 'LIT' distrib being second most important (2 points), and the time blocks '10' and '11' least important (1 point).
            </p>
            <?php } ?>
          </div>

          <div id="criteria" class="row">
          </div>
          <button type="button" onClick="addCriteria()" class="btn btn-secondary add">Add New Criteria</button>

          <h4>Filtering Past Classes</h4>
          <p>
             You can choose to filter out your past classes by copying and pasting your unofficial transcript.  The only information it will take is the department and class # for each class you've taken, and none of the information will be stored or saved.  <strong>This is optional.</strong>
          </p>
          <a href onClick="return toggleBannerText(this);">Show Scraping Steps</a>
          <div id="scrapingSteps" style="display: none;">
            <ol>
              <li>
                Click the following link, and log into Banner:  <strong><a href="javaScript:void(0);" onClick="openBanner()">Log In</a></strong>
              </li>
              <li>
                Navigate to 'Unofficial Transcript - Web version', and click 'Submit' to pull up your full unofficial transcript.  Select+All to select the entire page, and then copy it.
              </li>
              <li>
                Paste the newly copied text into the 'Raw Transcript' textbox.
              </li>
            </ol>
            <div class="form-group">
              <label for="classText">Raw Transcript (get from above instructions)</label>
              <textarea class="form-control" rows="5" name="classText" spellcheck="false"></textarea>
            </div>
          </div>
          <button type="submit" class="btn btn-primary search">Search</button>
        </form>
      </div>

      <div id="classes"></div>
    </div>

    <footer>
      <p>
        Made by <a href="//alexbeals.com">Alex Beals</a> '18.
        <!-- Source code: https://github.com/dado3212/classy -->
      </p>
    </footer>
  </body>
</html>

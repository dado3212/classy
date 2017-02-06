<?php
  include_once("php/util.php");

  $keycode = getKeycode($_SERVER["HTTP_USER_AGENT"]);
?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <title>DartClasses</title>
    <?php
      if (preg_match("/(iPhone|iPod|iPad|Android|BlackBerry|Mobile)/i", $_SERVER['HTTP_USER_AGENT'])) {
        ?><meta name="viewport" content="width=500"><?php
      }
    ?>
    <script>
      <?php
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
          $medianOptions .= "<option vallue='$code'>$code</option>";
        }
      ?>
      var departmentOptions = "<?php echo $departmentOptions; ?>;";
      var distributiveOptions = "<?php echo $distributiveOptions; ?>;";
      var periodOptions = "<?php echo $periodOptions; ?>";
      var medianOptions = "<?php echo $medianOptions; ?>";
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
          <h1>DartClasses</h1>

          <p class="overview">
            DartClasses is a way of searching for classes based on departments, distribs, periods, and medians.  It allows you to find the best fits for <i>you</i> based on your priorities in a course.
          </p>

          <h4>Criteria and Points Explanation</h4>
          <div>
            <p>
              You can build a search by using criteria.  You can add a criteria for the <strong>department</strong>, <strong>distrib</strong>, <strong>period</strong>, or <strong>median</strong>.  For each criteria, you can then choose one or more of the choices, and give it a number of points.  Classes that meet elements of each criteria will be given the number of points for that criteria, and the top classes will be returned, sorted by median.  If you're only searching for one criteria, the number of points is irrelevant.
            </p>
            <p>
              For example, you have three criteria.  The first, for the ECON department, gives 3 points.  The second, for the distrib 'LIT', gives 2 points.  The third, for the time blocks '10' and '11', gives 1 point.  This means that it is most important that the class be in the ECON department, slightly less important that it be a LIT course, and least important that it be a 10 or 11 (but still better than nothing).
            </p>
            <p>
              An ECON class in the 10A block would have 3 points.  A class in the 11 block with the 'LIT' distrib would also have 3 points.  An ECON class in the 10 block with the 'LIT' distrib would have the maximum of 6 points.
            </p>
          </div>

          <div id="criteria">
          </div>
          <button type="button" onClick="addCriteria()" class="btn btn-secondary">Add New Criteria</button>

          <h4>Getting Past Classes</h4>
          <p>
            You can choose to scrape the classes you've already taken from Dartmouth.  This will prevent it from suggesting classes you've already taken.  The only information it will take is the department, class #, and name of each class you've taken.
          </p>
          <p>
            <strong>This is optional, and slightly technical.  If you don't feel comfortable with computers, feel free to skip this.  Additionally, this is <i>not</i> possible on mobile.</strong>
          </p>
          <a href onClick="return toggleBannerText(this);">Show Scraping Steps</a>
          <div id="scrapingSteps" style="display: none;">
            <ol>
              <li>
                Click the below code, and copy it.<br>
                <code id="js">
                  prompt('Copy to DartClasses', document.cookie.split(';')[3].slice(8)); window.close();
                </code>
              </li>
              <li>
                Click the following link, and log into Banner:  <strong><a href="javaScript:void(0);" onClick="openBanner()">Log In</a></strong>
              </li>
              <li>
                Open the Developer view by pressing <code><?php echo $keycode; ?></code>, paste the code you copied in step 1 into the new tab, and hit 'Enter'.  (It will pop up a window with text selected.  Copy that <i>new</i> text and hit 'Enter').
              </li>
              <li>
                Paste the newly copied text into the Session ID input.
              </li>
            </ol>
            <div class="form-group">
              <label for="sessid">Session ID (get from above instructions)</label>
              <input type="text" class="form-control" placeholder="RS2JLz19CpZzQkXcHr==" name="sessid" spellcheck="false">
            </div>
          </div>
          <button type="submit" class="btn btn-primary">Search</button>
        </form>
      </div>

      <div id="classes"></div>
    </div>

    <footer>
      <p>
        Made by <a href="//alexbeals.com">Alex Beals</a> '18.
      </p>
    </footer>
  </body>
</html>

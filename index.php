<?php
  include_once("php/util.php");
?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <title>Find Classes</title>

    <script>
      <?php
        $departmentOptions = "";
        foreach ($departments as $code => $name) {
          $departmentOptions .= "<option value='$code'>$name ($code)</option>";
        }

        $distributiveOptions = "";
        foreach ($distribs as $code => $name) {
          $distributiveOptions .= "<option value='$code'>$name</option>";
        }

        $periodOptions = "";
        foreach ($periods as $code => $name) {
          $periodOptions .= "<option value='$code'>$name</option>";
        }
      ?>
      var departmentOptions = "<?php echo $departmentOptions; ?>;";
      var distributiveOptions = "<?php echo $distributiveOptions; ?>;";
      var periodOptions = "<?php echo $periodOptions; ?>";
    </script>

    <script src="//ajax.googleapis.com/ajax/libs/jquery/3.1.0/jquery.min.js"></script>
    <script src="//maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>
    <script src="//cdnjs.cloudflare.com/ajax/libs/chosen/1.6.2/chosen.jquery.min.js"></script>
    <script src="js/main.js"></script>

    <link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
    <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/chosen/1.6.2/chosen.min.css">
    <link rel="stylesheet" type="text/css" href="css/main.css">
  </head>
  <body>
    <h1>&nbsp;</h1>
    <div class="container">
      <div class="panel panel-primary">
        <div class="panel-heading">Search</div>
        <div class="panel-body">
          <div class="row">
            <div class="col-lg-12">
              <p>
                You can choose to scrape the classes you've already taken from Dartmouth.  This will prevent it from suggesting classes you've already taken.  The only information it will take is the department, class #, and name of each class you've taken.  <strong>This is optional.</strong>
              </p>
              <ol>
                <li>
                  Click the below code, and copy it.
                  <code id="js">
                    prompt('Copy to Class Search', document.cookie.split(';')[3].slice(8)); window.close();
                  </code>
                </li>
                <li>
                  Click the following link, and log into Banner:  <strong><a href onClick="window.open('//dartmouth.edu/bannerstudent','_blank')">Log In</a></strong>
                </li>
                <li>
                  Open the DevTools, and run the javascript you just copied.  (Ctrl+Shift+I, or F12).  It will pop up a window with text selected.  Just copy it and hit 'Enter'.
                </li>
                <li>
                  Paste into the Session ID input. 
                </li>
              </ol>
            </div>
          </div>
          <form>
            <div class="row">
              <div class="col-lg-12">
                <div class="form-group">
                  <label for="sessid">Session ID (get from above instructions)</label>
                  <input type="text" class="form-control" placeholder="RS2JLz19CpZzQkXcHr==" name="sessid">
                </div>
              </div>
            </div>

            <div id="criteria">
            </div>
            <button type="button" onClick="addCriteria()" class="btn btn-success">Add Criteria</button>

            <button type="submit" class="btn btn-primary">Search</button>
          </form>
        </div>
      </div>
    </div>

    <div id="output"></div>
  </body>
</html>

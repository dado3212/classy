<?php
  include_once("php/util.php");
  include_once("php/search.php"); 
  error_reporting(-1);
  ini_set('display_errors', 'On');

  $depts = [["COSC", "MATH"]];
  $distribs = [["LIT", "NW"]];
  $periods = [["10","11","2"],["12"]];
  $overall = array("depts" => 3, "distribs" => 0, "periods" => 1); // weighting term
  $prereqs = [];
?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.0/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>
    <script src="js/main.js"></script>

    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">

<!-- Optional theme -->
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap-theme.min.css" integrity="sha384-rHyoN1iRsVXV4nD0JutlnGaslCJuC7uwjduW9SVrLvRYooPp2bWYgmgJQIXwl/Sp" crossorigin="anonymous">

    <link rel="stylesheet" type="text/css" href="css/rough.css">
  </head>
  <body>
    <h1>&nbsp;</h1>
    <div class="container">
      <div class="row">
        <div class="col-lg-12">
          <div class="panel panel-primary">
            <div class="panel-heading">How to Search</div>
            <div class="panel-body">
              <div class="text-center">
                <a href="http://dartmouth.edu/bannerstudent" target="_blank" rel="noopener">Get Cookie</a>
              </div>
              <code>
                javascript:document.cookie.split(';')[3].slice(8);
              </code>
              <form>
                <div class="form-group">
                  <label for="sessid">Session ID (get from above code)</label>
                  <input type="text" class="form-control" placeholder="RS2JLz19CpZzQkXcHr==" name="sessid">
                </div>
                <button type="submit" class="btn btn-primary">Submit</button>
              </form>
            </div>
          </div>
        </div>
      </div>
    </div>
   
    <?php echo getMatches($depts, $distribs, $periods, $overall, $prereqs); ?>
  </body>
</html>

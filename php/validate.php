<?php
  include_once("./util.php");

  if (isset($_POST["sessid"])) {
    if (isSessionValid($_POST["sessid"])) {
      echo "valid";
    } else {
      echo "invalid";
    }
  } else {
    echo "invalid";
  }
?>
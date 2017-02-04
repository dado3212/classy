<?php
  include_once("banner.php");
  $sessid = (isset($_POST["sessid"]) ? $_POST["sessid"] : "UTJYUUE3MjYyNjQzMg==");
  $classes = getClasses($sessid);

  echo json_encode($classes, true);
?>
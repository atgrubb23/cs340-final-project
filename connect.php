<?php
include('userInfo.php');
  $myDb = new mysqli($myHost, $myUsername, $myPassword, $myDatabase);
  //Check connection
  if(!$myDb || $myDb->connect_errno) {
    echo "Connection to " . $myHost . " failed. Error #" . $myDb->connect_errno . " with " . $myDb->connect_error . ".";
  }
  else {
    echo "Connected to " . $myHost . " successfully.";
  }

?>
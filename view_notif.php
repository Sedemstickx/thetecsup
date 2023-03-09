<?php
header('Content-Type: application/json');
require_once "includes/initialize.php";

//$_SESSION['user_id'] refers to ids of other user profiles.$_SESSION['m_no'] refers to mobile number.
if (isset($_SESSION['user_id']) && isset($session->user_id) && isset($_SESSION['m_no'])) {
     
  //create view notification.
  $notification = new Notification();
  $notification->create_notif($_SESSION['user_id'],"view",$_SESSION['user_id'],$session->user_id,"");

  //empty selected session data.
  //prevents the same data from being sent multiple times if user clicks the button multiple times.
  $_SESSION["user_id"] = null;
  unset($_SESSION["user_id"]);

$mobile_number = array("number" => $_SESSION['m_no']);//make this an associative array. for some reason $_SESSION doesn't make arrays associative.

echo json_encode($mobile_number);//send mobile number on success.
}

?>
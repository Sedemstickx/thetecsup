<?php require_once "../includes/initialize.php"; ?>
<?php

if($session->is_admin_logged_in()){
 
 $announce_class = new Announcement();

 $result = $announce_class->delete();

  if($result){
  //success

    $session->message("Announcement successfully deleted.");

      redirect_to($_SERVER['HTTP_REFERER']);
    }
}
?>
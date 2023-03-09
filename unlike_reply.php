<?php require_once "includes/initialize.php"; ?>
<?php

if(isset($_POST['unlike_id'])) {

 $like_reply_class = new LikeReply();

 $result = $like_reply_class->delete();

   if($result){
 //success
  $notif_class = new Notification(); 
  //delete like notification.
    $notif_class->delete($_POST['unlike_id']);
    }
}

?>
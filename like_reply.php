<?php require_once "includes/initialize.php"; ?>
<?php

if(isset($_POST['like_id'])) {

 $like_reply_class = new LikeReply();

 $result = $like_reply_class->create();

   if($result){
   //success
   	$reply = Reply::find_by_id($_POST['like_id']);
    
    //create like notification.
    //if user liking the reply is not the originator of the reply create the notification.
    if ($reply->userid != $session->user_id) {  

    $notif_class = new Notification();

    $notif_class->create_notif($reply->userid,"likes",$reply->post_id,$session->user_id,$_POST['like_id']);
   }
    }
}

?>
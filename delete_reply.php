<?php require_once "includes/initialize.php"; ?>
<?php
if (isset($_GET['id'])) {
  $reply_id = htmlentities($_GET['id']);
}

 //find some question details from database.
 $reply = Reply::find_by_id($reply_id);

//redirect user to home page if id doesn't exist yet
if ($reply == null) {
  redirect_to($home);
}

if($session->is_logged_in() && $session->user_id==$reply->userid){

  //Reset all reply_to_ids related to this reply id to 0.
  $reply->reset_reply_to($reply->id);

  $result = $reply->delete($reply->image);

    if($result){
 //success
  $notification = new Notification(); 
  $notification->delete($reply_id);

  //create likereply instance and deleted all likes to this reply.
  $like_reply = new LikeReply();
  $like_reply->delete_all_related_likes($reply_id);

 //use the user class function to query sql and return results from database.
  $user = User::find_by_id($reply->userid);

  //subtract points
  $user->subtract_points($reply->userid,10);

      redirect_to($_SERVER['HTTP_REFERER']);
    }
}
?>
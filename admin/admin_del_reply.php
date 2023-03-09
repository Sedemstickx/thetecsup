<?php require_once "../includes/initialize.php"; ?>
<?php
if (isset($_GET['id'])) {
  $reply_id = htmlentities($_GET['id']);
}

if($session->is_admin_logged_in()){ 

  //find some question details from database.
  $reply = Reply::find_by_id($reply_id);

//redirect user to home page if id doesn't exist yet
if ($reply == null) {
  redirect_to($admin_home);
}

  //Reset all reply_to_ids related to this reply id to 0.
  $reply->reset_reply_to($reply->id);

  $result = $reply->admin_delete($reply->image);

  $notification = new Notification();
  $like_reply = new LikeReply(); 

    if($result){
    //success
    $notification->delete($reply_id);

  //create likereply instance and deleted all likes to this reply.
  $like_reply->delete_all_related_likes($reply_id);
      
 //use the user class function to query sql and return results from database.
  $user = User::find_by_id($reply->userid);

  //subtract points
  $user->subtract_points($reply->userid,10);

  $reported_msg = "";
   if (isset($_GET['report'])) {
     $reported_msg = "<p>Your reply has been reported by a user.</p>";
   }

  $message = "Dear {$user->username},<br>
  {$reported_msg}
  <p>The reply '".htmlentities($reply->text)."' you posted doesn't go in accordance with our terms of use and it has been deleted. 
  </p>
  <p>Please do not reply this email as it's not monitored. If there has been a mistake you can <a href='mailto:{$site_title}@gmail.com'>contact us</a>.</p>";

  //send email notif.
  send_email($user->email,"Deleted reply",$message);
  
  //Record Admin who deleted the reply.
  log_action(htmlentities($cookie_username),"deleted the reply -> ".htmlentities($reply->text)."",$logfile = "../logs/logs.txt");

    $session->message("Reply successfully deleted. Mail notice has been sent to the user.");

      redirect_to($_SERVER['HTTP_REFERER']);
    }
}
?>
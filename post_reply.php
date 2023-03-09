<?php
require_once "includes/initialize.php";

   //Get question id from a post request from forum page.
   $post_id = (int)$_POST['post_id'];

   //return selected question from database.
   $post = Post::find_by_post(htmlentities($post_id)); 

   $reply = new Reply();//by default draft will not be availble so default instance must be created.

   //get draft if available.
   $reply_draft = Reply::find_by_draft($post_id);

//Check if user has started drafting their reply and save their post as a draft.    
if (isset($_POST['draft']) && isset($_POST['text'])){

  //refactor
  $draft = $_POST['draft'];  

   if($session->is_logged_in()){

  //if user hasn't completed drafting continue updating thhe draft.
  if ($draft == 1 && $reply_draft != null) {
      $result = $reply->update_draft($post_id);
  }
  
  //if user completes drafting and clicks to post reply unsave the draft and post in on the page.
  elseif ($draft == 0 && $reply_draft != null){

   $result = $reply->unsave_draft($post_id);

   $activity_id = $reply_draft->id;

   if($result){

    $reply_to_userid = Reply::find_userid($_POST['reply_id']);
   //success
   //create reply notifications.
   $notification = new Notification();
   $notification->reply_notifs($post->userid,$post_id,$reply_to_userid,$activity_id);
    
   //send email notif.
   $reply->send_reply_email($post->title,$post_id,$post->userid,$reply_to_userid);

   //Update points
   $user = new User();
   $user->update_points($session->user_id,10);

    //only needed if jquery ajax is not used.
   //redirect_to($server_url);
     }
   }
   //If there is no saved draft save the drafted reply as a draft.use csrf for protection.
  elseif ($draft == 1 && $reply_draft == null && csrf_protect()){
   $result = $reply->save_draft($post_id);
  }  
 }
}
else {
  echo "There was an error.";
}
?>
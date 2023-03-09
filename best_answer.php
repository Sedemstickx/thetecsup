<?php require_once "includes/initialize.php"; ?>
<?php
if (isset($_GET['rid'])) {
  $reply_id = $_GET['rid'];
}

 //find some question details from database.
 $post = Post::find_by_id($_GET['id']);

if($session->is_logged_in() && $session->user_id==$post->userid){

  $reply_class = new Reply(); 

  $result = $reply_class->update_best_answer($reply_id);

    if($result){
  //success
  $reply = Reply::find_by_id($reply_id);

   //use the user class function to query sql and return results from database.
  $user = User::find_by_id($reply->userid);

  //add points
  $user->update_points($user->id,20);

  $session->message("Your have successfully selected the best answer :)");    

      redirect_to($_SERVER['HTTP_REFERER']);
    }
}
?>
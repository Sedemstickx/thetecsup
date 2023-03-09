<?php require_once "includes/initialize.php"; ?>
<?php
 //find some question details from database.
 $post = Post::find_by_id(htmlentities($_GET['id']));

if($session->is_logged_in() && $session->user_id==$post->userid){

 $result = $post->delete($post->image);

  if($result){
//success
  //use the user class function to query sql and return results from database.
  $user = User::find_by_id($post->userid);

   $subtract = "";
   if ($post->type == "tip") {
   $subtract = 15;
   }
   else{
    $subtract = 5;
   }

  //subtract points
  $user->subtract_points($post->userid,$subtract);

    $session->message("Question successfully deleted.");

      redirect_to($_SERVER['HTTP_REFERER']);
    }
}
?>
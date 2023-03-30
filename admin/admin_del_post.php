<?php require_once "../includes/initialize.php"; ?>
<?php

if($session->is_admin_logged_in()){

 //find some question details from database.
 $post = Post::find_by_id(htmlentities($_GET['id']));

//redirect user to home page if id doesn't exist yet
if ($post == null) {
  redirect_to($admin_home);
}

 $result = $post->admin_delete($post->image);

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

  $reported_msg = "";
   if (isset($_GET['report'])) {
     $reported_msg = "<p>Your post has been reported by a user.</p>";
   }

  $message = "Dear {$user->username},<br>
  {$reported_msg}
  <p>The question '".htmlentities($post->title)."' you posted doesn't go in accordance with our terms of use and it has been deleted. 
  </p>
  <p>Please do not reply this email as it's not monitored. If there has been a mistake you can <a href='mailto:{$site_title}@gmail.com'>contact us</a>.</p>";

  //send email notif.
  send_email($user->email,"Deleted post",$message);
  	
  //Record Admin who deleted the post.
  log_action(htmlentities($session->active_username),"deleted the post -> ".htmlentities($post->title)."",$logfile = "../logs/logs.txt");

    $session->message("Question successfully deleted. Mail notice has been sent to the user.");

      redirect_to($_SERVER['HTTP_REFERER']);
    }
}
?>
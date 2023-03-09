<?php require_once "includes/initialize.php"; ?>

<?php

  if (isset($_GET['qid'])) {
  $post_id = $_GET['qid'];
}

  if (isset($_GET['question'])) {
  $question = $_GET['question'];
}

$server_url = $_SERVER['REQUEST_URI'];

$return = return_to();

   $reply = Reply::find_by_id(htmlentities($_GET['id'])); 

//redirect user to home page if id doesn't exist yet
if ($reply == null) {
  redirect_to($home);
}

$msg = "";
if(isset($_POST['submit']) && csrf_protect()){

   $result = $reply->update_reply($reply->id);

    if($result){

      $post = Post::find_by_id(htmlentities($post_id)); 

       $notif_class = new Notification();

       $reply_to_userid = Reply::find_userid($reply->reply_to_id);

    //create edited reply notification.   
    $notif_class->edit_reply_notifs($post->userid,$reply_to_userid);

    //send edited reply email notif.
    $reply->send_replyEdit_email($post->title,$post->userid,$reply_to_userid);

     //Update points
    $user_class = new User();
    $user_class->update_points($session->user_id,2);
  
    $session->message("Your reply has been successfully updated. <a href=". $return .">Go to post</a>");

    redirect_to($server_url);
    }
   else {
     $msg = "<br><center><span class='error-feedback-messages'>Something went wrong or you haven't changed anything.</span></center>";
    }
}
?>

<?php $page_title = "Edit reply";?>
<?php include("layouts/header.php"); ?>
<?php if(!$session->is_logged_in() && $session->user_id != $reply->userid){redirect_to($home);} ?>

  <div id="left" class="middle-spacing"> 

<h1>Edit your reply</h1>

  <form action="<?php echo htmlentities($server_url); ?>" method="post">
  <?php echo csrf_token(); ?>

    <p><b style="line-height:2.5em;">Question : <?php echo htmlentities($question); ?></b> 
      <br>
    <textarea name="text" placeholder="Write a reply..." title="Not more than 2000 characters" maxlength="2000" required><?php echo htmlentities($reply->text); ?></textarea>
        </p> 
       <br>
        <br>
    <input type="submit" name="submit" value="Save changes">&nbsp;&nbsp; <a href="javascript:void(0)" onclick="goBack()">Cancel</a> 
    <br>

    </form>

    </div>

<?php include("layouts/footer.php"); ?>
<?php require_once "includes/initialize.php"; ?>

<?php 
if (isset($_GET['id'])) {
  $post_id = $_GET['id'];
}

$server_url = $_SERVER['REQUEST_URI'];
  
$return = return_to();

$post = Post::find_by_id(htmlentities($post_id)); 

  $msg="";
  if(isset($_POST['submit']) && csrf_protect()){
   
  $result = $post->update_post(htmlentities($post_id));

     if($result){

      $session->message("Your question has been successfully updated. <a href=". $return ."> Go to post</a>");

      redirect_to($server_url);
    }
   else {
     $msg = "<br><center><span class='error-feedback-messages'>Something went wrong or you haven't changed anything.</span></center>";
    }
 }
?>

<?php $page_title =  isset($session->user_id) && $session->user_id == $post->userid ? "Edit post" : "Edit topics";?>
<?php include("layouts/header.php"); ?>
<?php if(!$session->is_logged_in() && $session->user_id != $post->userid){redirect_to($home);} ?>

  <div id="left" class="middle-spacing"> 

<h1><?php echo $tags = isset($session->user_id) && $session->user_id == $post->userid ? "Edit your post" : "Edit or add topics"; ?></h1>


<?php 
if ($post->type == "tip") {
  $post_type = "Tip";
  $placeholder = "A brief title of the tip";
}
else{
  $post_type = "Question";
  $placeholder = "You can start with How/What/Why";
}

//asssign topics from db to $topic_result
$topic_result = $post->topic;
?>

<?php
if ($post->last_edited != 0) {

    //get user profile details.
  list($username) = User::find_user_profile($post->last_edited);
  
  if ($username != null) {
    echo "<br><span style='color:gray;'>Last edited by: ".htmlentities($username)."</span>";
  }
   
}
?>

  <form action="<?php echo htmlentities($server_url); ?>" method="post">
  <?php echo csrf_token(); ?>
  
<?php 
if(isset($session->user_id) && $session->user_id == $post->userid) {
?>
    <p><b style="line-height:2.5em;"><?php echo $post_type; ?></b> 
      <br>
       <input type="text" name="title" placeholder="<?php echo $placeholder; ?>" value="<?php echo htmlentities($post->title); ?>" maxlength="100" required>
       </p>
    <p><b style="line-height:2.5em;">Details</b> 
      <br>
    <textarea name="details" placeholder="Enter more details for better description" title="Not more than 2000 characters" maxlength="2000"><?php echo htmlentities($post->details); ?></textarea>
        </p> 
<?php
}
?>        
      <br>
      
      <?php include 'refactor/postTopicsForm.php'; ?>      

    <input type="submit" name="submit" value="Save changes">&nbsp;&nbsp; <a href="javascript:void(0)" onclick="goBack()">Cancel</a> 
    <br>

    </form>

    </div>

<?php include("layouts/footer.php"); ?>
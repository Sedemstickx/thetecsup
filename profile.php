<?php require_once "includes/initialize.php"; ?>

<?php
if (isset($_GET['name'])) {
$name = $_GET['name'];
}
 else{
 $_SESSION["message"] = "Page doesn't exist. :(";

 redirect_to($home);
}

if (isset($_GET['profile'])) {
  $profile_type = $_GET['profile'];
}

//return the user's details from the database.
$user = User::find_profile_by_name($name); 

if ($user == NULL) {
 $_SESSION["message"] = "Page doesn't exist. :(";

 redirect_to($home);
}

$_SESSION['user_id'] = $user->id;//assign selected user id to a session for use with ajax.
$_SESSION['m_no'] = $user->m_number;
?>

<?php
 if (isset($_GET['notif_id'])) {//get id of the selected notification.
  $notif_class = new Notification();  
  $notif_class->update_read_status($_GET['notif_id']);//change read status to read
 }
?>

<?php $page_title = htmlentities($name) . " - thetecsup"; ?>
<?php include("layouts/header.php"); ?> 

<div id="left">

<!--profile part-->      
<?php 
   //get user profile pic location.
  $picture = get_pic_location($user->pic);
?>

<?php if($session->is_logged_in()) {?>
<button class="report" value="<?php echo Report::user_report_link($user->id); ?>" onclick="report_modal(this)">Report this user</button>
<?php } ?>

<!--display the uploaded picture.-->
 <center>
  <img src="<?php echo $picture; ?>" alt="image" onclick="enlargeImage(this);" style="cursor: zoom-in;" class="profile-pic">
</center>

<center><?php echo $profile_options = $user->auth_edit_show_freelancer($session->user_id,$user->id,$user->freelancer); ?> <?php echo $admin_access = $user->auth_user_admin_access($session->user_id,$user->id,$user->block,$user->admin); ?></center>
<br>
<center><b style="font-size: 1rem;"><?php echo htmlentities($user->username); ?></b><br>
  <br>
<span class="profile-details"><?php if($user->bio != null){echo nl2br(create_hyperlinks($user->bio)) . "<br><br>";} ?></span>

  <span class="profile-details"><?php if($user->location != null){echo "Location: " .htmlentities($user->location) . "<br><br>";} ?></span>

  <span class="profile-details">Member since: <?php echo date_converter($user->date); ?></span><br>  
<br>  
  <span class="profile-details" title="Earn more points and who knows, we might reward you well in the future ;) .">Reputation Points: <?php echo htmlentities(number_format($user->points)); ?></span><br> 
<br>
<span class="profile-details"><?php if($user->specialties != null){echo "Specialties: " .htmlentities($user->specialties) . "<br><br>";} ?></span>


</center>

 <br>

<!--Image enlarger-->
<?php 
//Image enlarger
image_enlarge_div();
?>

<!--User question and reply records-->
<span><a class="profile-tab-link" style="<?php if(!isset($profile_type)){echo "font-weight:bold";} ?>" href="<?php echo User::profile_link($user->username); ?>">Replies: <?php echo number_format(Reply::count_by_userid($user->id)); ?></a></span>&nbsp; <span><a class="profile-tab-link" style="<?php if(isset($profile_type) == "questions"){echo "font-weight:bold";} ?>" href="<?php echo User::profile_link($user->username); ?>&profile=<?php echo urlencode("post"); ?>">Posts: <?php echo number_format(Post::count_by_userid($user->id)); ?></a></span>
<hr>

<!--reply part-->
<?php 
if(!isset($profile_type)){
 //create likereply instance
$like_reply_class = new LikeReply();

$pagination = new Pagination($per_page=15,$total_count=Reply::count_by_userid($user->id));

$reply_result = Reply::find_by_user_id($user->id,$per_page,$pagination->offset()); 

 //display list of replies by the user.
if($reply_result->num_rows >= 1){
  while ($reply = $reply_result->fetch_object('Reply')) { 

  //get user profile pic location.
  $pic = get_pic_location($reply->pic);

     //process image if one exist.
   $reply_image = $reply->display_image($reply->image);   

  //Find the userid using the reply_to property which matches a given reply id.
  $reply_to_userid = Reply::find_userid($reply->reply_to_id);

  //get the username from the userid that has been replied to.
  list($reply_username) = User::find_user_profile($reply_to_userid);

  $replied_to_username = Reply::replied_username($reply_username);//return replied to username
?>

<div class="replies-list">

<p><a class="posts" href="<?php echo Post::forum_link($reply->replied_id,$reply->title); ?>"><b><?php echo htmlentities($reply->title); ?></b></a></p>

<div class="flex">
<div class="profile-div"><a class="profile-link" href="<?php echo User::profile_link($user->username); ?>  "><img src="<?php echo $pic; ?>" alt="image" class="profile-pic-small"></a></div> 
<div class="name-div"><a class="profile-link" href="<?php echo User::profile_link($user->username); ?>  "><b><?php echo htmlentities($user->username); ?></b></a></div> 
  </div>
<p><?php echo nl2br(create_hyperlinks($replied_to_username.$reply->text)); ?></p>

  <?php echo $reply_image; ?>

<div>
<!--Number of likes-->
<?php 
 echo $like_reply_class->num_of_likes($reply->id);
?>
</div>

<div class="left-right-items no-margin">
<div>
<?php if($session->is_logged_in()) {?>
<button class="reply-report" value="<?php echo $reply_report_link = Report::reply_report_link($reply->id); ?>" onclick="report_modal(this)">Report</button>
<?php } ?>

<!--like/unlike button--> 
<?php echo $like_unlike_feild = $like_reply_class->like_unlike_field($reply->id); ?>
</div>

 <p class="reply-date-time"><?php echo date_converter($reply->date). " at " . time_converter($reply->time); ?></p>
</div>  

</div> 

<?php
  }
 }
 else{
  echo "<br><br><center style='color:gray;'> No replies have been posted. </center><br><br><br>";
  }
  //free results in memory after loop.
 $reply_result->free_result(); 
}

elseif(isset($profile_type) == "post"){
?>
<!--Question part-->
<?php
$pagination = new Pagination($per_page=15,$total_count=Post::count_by_userid($user->id));

$post_result = Post::find_by_user_id($user->id,$per_page,$pagination->offset()); 


 //display list of questions by the user.
if($post_result->num_rows >= 1){
  while ($post = $post_result->fetch_object('Post')) { 

   //get reply results.
  $reply_result_num = Reply::count_by_questionid($post->id);   

  list($tip) = $post->post_type($post->type);

     if ($post->type == "tip") {
     $url_page = Post::tip_link($post->id,$post->title);
   }else{
      $url_page = Post::forum_link($post->id,$post->title);
   }
?>

<div class="post-list">

<?php echo $tip; ?>  

  <p>
<div class="left-right-items no-margin">    
<a class="posts" href="<?php echo $url_page; ?>"><b><?php echo htmlentities($post->title); ?></b></a>
<div class="edit-delete-container"><span><?php echo $q_edit = $post->auth_post_edit($post->id,$user->id); ?>
  <?php echo $q_delete = $post->auth_post_delete($post->id,$user->id); ?>
</span></div> 
</div>
  </p>  
  
<span>In</span> <a class="post-topic" href="<?php echo Topic::link($post->topic); ?>"><?php echo htmlentities(strtok($post->topic, ",")); ?></a>

<div class="left-right-items no-margin">
<span class="bottom-gray-text">
  <?php if ($post->type != "tip") { ?>
  <?php echo number_format($reply_result_num); ?> <?php echo num_grammar($reply_result_num,"reply","replies") ?>&nbsp; 
<?php } ?>
  Views: <?php echo htmlentities(number_format($post->views)); ?>
</span>
<span class="bottom-gray-text"><?php echo date_converter($post->date). " at " . time_converter($post->time); ?></span>
</div>

</div>
<?php
 }
}
 else{
  echo "<br><br><center style='color:gray;'> Nothing posted yet. </center><br><br><br>";
 }
//free results in memory after loop.
 $post_result->free_result(); 
}
?>
<br>
 <br>
<div id="pagination">
 <?php
//Provide page links.
$pagination->page_links();
?>
   </div>
 <br>
  <br>

<?php if($user->freelancer == 1 && $session->is_logged_in() && $user->id != $session->user_id) {?>
<div class="mobile_contact">
<center><button onclick="show_contact_mobile()" id="contact_mobile" class="mobile_contact_button">Contact Freelancer</button></center>
</div>

<div id="modal_contact_bg" class="modal_bg">

<div id="mobile_contact_modal">
<span id="close_mobile" class="close_mobile_modal" title="close">&times;</span> 
<br>
 <br>
<center>
  <b id="bold_mno">Please wait...</b>
<br>
 <br>
<a id="mobile_sms" href=""><i class="fa-solid fa-comment-sms" style="font-size:2rem;"></i></a> &nbsp;&nbsp;&nbsp;&nbsp;  
<a id="mobile_tel" href=""><i class="fa-solid fa-phone" style="font-size:1.8rem;"></i></a>
</center>
<br>
 <br> 
<?php echo safety_tips(); ?> 
</div>
    </div>
<?php } ?>   

     </div>

<!-- Report -->
<?php include('report.php');?>
<?php include("layouts/rightside.php"); ?>
<?php include("layouts/footer.php"); ?>
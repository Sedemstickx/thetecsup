<?php require_once "includes/initialize.php"; ?>

<?php if(!$session->is_logged_in()){redirect_to($home);} ?>
<?php 
 $notification = new Notification();
 $notification->update_viewed_status($session->user_id);
?>

<?php $page_title = "Notifications - ".$site_title.""; $active_notif = 'id="active"';?>
<?php include("layouts/header.php"); ?>

  <div id="left" class="middle-spacing"> 

<h1>Notifications</h1>
<hr>

<?php
$notif_result = Notification::find_by_userid($session->user_id);

if($notif_result->num_rows >= 1){

 //display list of questions posted.
  while ($notif = $notif_result->fetch_object()) {

  //get user profile pic location.
  $pic = get_pic_location($notif->pic); 

//Check if notif is read or not and change backgound colors if notif is read or unread.
list($read,$bgcolor) = $notification->check_read($notif->is_read);

$post = Post::return_title($notif->source_id);//get reply text

//Question replies notifs.  
if ($notif->type == "Question reply") {

   $reply_cnt = Reply::count_by_questionid($notif->id,$notif->userid);

   if ($reply_cnt > 1) {
   //get number of replies of given question
   $reply_result_num = $reply_cnt-1;   
   }
   else{
       $reply_result_num = $reply_cnt;
   }


   $reply_grammar = $notification->reply_num_grammar($reply_result_num);
?>

<a href="<?php echo Post::forum_link($notif->source_id,$post->title); ?>&read=<?php echo urlencode($read); ?>&notif_id=<?php echo urlencode($notif->id); ?>#<?php echo urlencode($notif->activity_id); ?>" class="notif-anchor">
<div class="notif-list" style="<?php echo $bgcolor;?>">
<span><img src="<?php echo $pic; ?>" alt="image" class="profile-pic-small"> <b><?php echo htmlentities($notif->username); ?></b> <?php echo $reply_grammar; ?> posted a reply to your Post: <b><?php echo htmlentities($post->title); ?></b> </span>
<span class="notif_dates"><?php echo date_converter($notif->date). " at " . time_converter($notif->time); ?></span>
</div>
</a>

<?php
}

//Reply to another reply notifs.
if ($notif->type == "Replies") { 
 ?>

<a href="<?php echo Post::forum_link($notif->source_id,$post->title); ?>&read=<?php echo urlencode($read); ?>&notif_id=<?php echo urlencode($notif->id); ?>#<?php echo urlencode($notif->activity_id); ?>" class="notif-anchor">
<div class="notif-list" style="<?php echo $bgcolor;?>">
<span><img src="<?php echo $pic; ?>" alt="image" class="profile-pic-small"> <b><?php echo htmlentities($notif->username); ?></b> replied to your reply to the Post:  <b><?php echo htmlentities($post->title); ?></b> </span>
<span class="notif_dates"><?php echo date_converter($notif->date). " at " . time_converter($notif->time); ?></span>
</div>
</a>

<?php  
}

//Edited reply to another reply or question notifs.
if ($notif->type == "Edited reply") {
?>

<a href="<?php echo Post::forum_link($notif->source_id,$post->title); ?>&read=<?php echo urlencode($read); ?>&notif_id=<?php echo urlencode($notif->id); ?>#<?php echo urlencode($notif->activity_id); ?>" class="notif-anchor">
<div class="notif-list" style="<?php echo $bgcolor;?>">
<span><img src="<?php echo $pic; ?>" alt="image" class="profile-pic-small"> <b><?php echo htmlentities($notif->username); ?></b> edited their reply in the Post: <b><?php echo htmlentities($post->title); ?></b> </span>
<span class="notif_dates"><?php echo date_converter($notif->date). " at " . time_converter($notif->time); ?></span>
</div>
</a>

<?php
}

$reply = Reply::return_text($notif->activity_id);//get reply text

//notifs for likes.
if ($notif->type == "likes" && $notif->activity_id != 0 && $reply != null) {//if activity id(replyid) is not empty use return_text method to get text.
?>

<a href="<?php echo Post::forum_link($notif->source_id,$post->title); ?>&read=<?php echo urlencode($read); ?>&notif_id=<?php echo urlencode($notif->id); ?>#<?php echo urlencode($notif->activity_id); ?>" class="notif-anchor">
<div class="notif-list" style="<?php echo $bgcolor;?>">
<span><img src="<?php echo $pic; ?>" alt="image" class="profile-pic-small"> <b><?php echo htmlentities($notif->username); ?></b> 
liked your reply:  <b>"<?php echo htmlentities(substr($reply->text,0,120)) . "..."; ?>"</b></span> 
<span class="notif_dates"><?php echo date_converter($notif->date). " at " . time_converter($notif->time); ?></span>
</div>
</a>

<?php 
  } 

//notifs if a user has viewed another user contact detail.
if ($notif->type == "view") {
?>

<a href="<?php echo User::profile_link($notif->username); ?>&read=<?php echo urlencode($read); ?>&notif_id=<?php echo urlencode($notif->id); ?>" class="notif-anchor">
<div class="notif-list" style="<?php echo $bgcolor;?>">
<span><img src="<?php echo $pic; ?>" alt="image" class="profile-pic-small"> <b><?php echo htmlentities($notif->username); ?></b> has viewed your contact details. You might be contacted for a gig soon.</span>
<span class="notif_dates"><?php echo date_converter($notif->date). " at " . time_converter($notif->time); ?></span>
</div>
</a>

<?php 
  } 
//closing curly brackets for if and whiles conditions. 
 }
}
 else{
  echo "<br><br><br><center style='color:gray;'> No notifications for you yet. </center><br><br><br>";
 }
//free results in memory after loop.
 $notif_result->free_result();  
?>
    </div>

<?php include("layouts/footer.php"); ?>
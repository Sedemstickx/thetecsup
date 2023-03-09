<?php require_once "includes/initialize.php"; ?>

<?php
$pagination = new Pagination($per_page=15,$total_count=Post::count_all_posted());

$post_result = Post::find_all_posted($per_page,$pagination->offset()); 
?>

<?php $page_title = "".$site_title." - Home"; $active_home = 'id="active"'; ?>
<?php include("layouts/header.php"); ?>

  <div id="left"> 

<!--announcement-->
<?php
 if ($session->is_logged_in()) {

 //find and display the latest announcements to all users of the site.
 $announcement = Announcement::find_latest(); 

 //find if given user has read the announcement already or not. 
 $user = User::read_announcements_status(); 


 //display announcement if user hasn't already read it 
 if ($user->read_announcement == 0 && $announcement != null) {
 ?>
<div id="Announce" class="announcement">

 <p>
 <span onclick="hideAnnounce()" style="float:right;margin-right:5px;margin-top:-6px;cursor:pointer;font-size:1.687rem;">&times;</span>
<center><span style="font-weight:bold;font-size:1.125rem;"><?php echo htmlentities($announcement->title);?></span></center>
</p>
<span style="overflow:hidden;font-size: 0.937rem;"><?php echo htmlentities(substr($announcement->message,0,86)) . "..."; ?>
</span>
<br>
 <br>
<center><a href="read_announcement?view=<?php echo urlencode($user->read_announcement); ?>&id=<?php echo urlencode($announcement->id); ?>">Read more</a></center>
<span class="date-time"><?php echo date_converter($announcement->date);?></span>

</div>
<?php
 }
}
?>

<div class="left-right-items">
  <h1><?php echo $statement = page_conditional_statement("Latest posts", "Posts"); ?></h1> 
  <a class="askquestionbox" href="ask">Ask Question</a>
</div> 
<div class="left-right-items no-margin">
  <div class="left-items"><a href="#user_list">Users</a> <a style="margin-left: 8px;" href="tips">Tips</a></div>  
  <div class="right-items"><a href="topics">View topics</a></div>
</div>
<hr>
<!--Question list-->  
<?php
if($post_result->num_rows >= 1){

  include 'refactor/postList.php';

 //display list of questions posted.
  while ($post = $post_result->fetch_object()) {

  //get user profile pic location.
  $pic = get_pic_location($post->pic);

 //get reply results.
  $reply_result_num = Reply::count_by_questionid($post->id);
?>

<?php echo post_list($pic,$post->username,$post->id,$post->title,$post->details,$post->topic,$post->views,$post->date,$post->time,$reply_result_num,$post->type); ?>
  
<?php
 }
}
 else{
  echo "<br><br><center style='color:gray;'> No post have been written yet. </center><br><br><br>";
 }
//free results in memory after loop.
 $post_result->free_result(); 
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
    </div>

<?php include("layouts/rightside.php"); ?>
<?php include("layouts/footer.php"); ?> 

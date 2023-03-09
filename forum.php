<?php require_once "includes/initialize.php"; ?>

<?php 
 if (isset($_GET['notif_id'])) {//get id of the selected notification.
  $notif_class = new Notification(); 
  $notif_class->update_read_status($_GET['notif_id']);//change read status to read
 }
?>

<?php
//Question title.
 $question_title ="";
 if (isset($_GET['question'])) {$question_title = $_GET['question'];}

  $post_id = $_GET['id'];

  $server_url = $_SERVER['REQUEST_URI'];

   //return selected question from database.
   $post = Post::find_by_post(htmlentities($post_id)); 


  //if question doesn't exist in database redirect user to homepage and tell them that the page doesn't exist.
if($post == null){

$session->message("Page doesn't exist :(");

 redirect_to($home);}

   //if user visits the page add 1 to view table.
   $post->update_views();

   //get draft if available.
   $reply_draft = Reply::find_by_draft($post_id);
?>

<?php $page_title = htmlentities($post->title) . " - thetecsup"; ?>
<?php 
$encode_shared_link = '<script type="text/javascript">
   var link = location.href;
   if (link.indexOf("-") != -1) {
   var shared_link = link.replace("-","#");
   window.location = shared_link;
   }
 </script>';
?>
<?php include("layouts/header.php"); ?>

<div id="left"> 
<!--Question part-->

<?php 
$type = "question";
include 'refactor/postDisplay.php'; 
?> 

<!--Reply part-->
<div  id="reply-list">
<?php
   $pagination = new Pagination($per_page=15,$total_count=Reply::count_by_questionid($post_id));//get limit per page and number of sql query. 

   $reply_result = Reply::find_by_questionid($post_id,$per_page,$pagination->offset()); 
?>
<p>
<b id="reply_num"><?php echo number_format($total_count); ?></b> <b id="reply_num_gram"><?php echo num_grammar($total_count,"reply","replies") ?></b>
</p>


<?php
 //display list of replies posted.

if($reply_result->num_rows >= 1){

  //include refactored reply list
  include 'refactor/replyList.php';  

 //create likereply instance
$like_reply = new LikeReply();

//Get all results from replies with the best answer selected to the given question.
$best_answer_set = Reply::find_best_answer($post_id); 

  while ($reply = $reply_result->fetch_object('Reply')) {

  $reply_id = $reply->id;

  //get user profile pic location.
  $pic = get_pic_location($reply->pic); 

    //process image if one exist.
   $reply_image = $reply->display_image($reply->image); 

   //mark reply as edited if true.
     $edited_reply = "";
  if ($reply->edited == 1) {$edited_reply = "<span class='edited no-margin'>Edited</span><br>";} 

  //Find the userid using the reply_to property which matches a given reply id.
  $reply_to_userid = Reply::find_userid($reply->reply_to_id);
  
  //get the username from the userid that has been replied to.
  list($reply_username) = User::find_user_profile($reply_to_userid);
  
  $replied_to_username = Reply::replied_username($reply_username);//return replied to username

//Get or let the asker select his/her best answer.
$best_answer = $reply->auth_best_answer($best_answer_set,$post->userid,$reply->userid,$reply->id,$reply->b_answer,$post_id);
?>

<?php echo reply_list($reply->id,$reply->username,$pic,$post->title,$post_id,$reply->userid,$replied_to_username,$reply->text,$reply_image,$reply->date,$reply->time,$like_reply,$edited_reply,$best_answer,$reply); ?>

<?php
 }
}
 else{
  echo "<center id='no_reply' style='color:gray;'><br><br> No replies have been posted. Be the first to reply.<br><br><br></center>";
 } 
?>
</div> 

<div id="shared_reply"><?php if (isset($_GET['rid'])) { include_once("shared_reply.php"); } ?></div>

<div id="current_reply"></div>


<div id="pagination">
 
<?php
//Provide page links.
$pagination->page_load_ajax($per_page,$total_count,"more_replies");
?>

   </div>
  


<span id="post_id" style="display:none;"><?php echo htmlentities($post_id); ?></span>

<!--drafting and posting-->
<center><span id="loadstyle" style="color:silver;font-size:0.975rem;display:none;">Posting...</span></center>

<?php if($reply_draft != null){echo "<span id='draft_indicator' style='color:gray;'>Draft last saved on ".date_converter($reply_draft->date)." at ".time_converter($reply_draft->time)."</span>";} ?>
  <span id="draft_indicator" style="color:gray;"></span> 

<!--reply form-->
<?php
//if user is logged in allow the user to reply else show the login option.
if($session->is_logged_in()){
 ?>
<form id="uploadform" action="<?php echo htmlentities("post_reply"); ?>" enctype="multipart/form-data" method="post">
<?php echo csrf_token(); ?>

  <input type="hidden" name="post_id" value="<?php echo htmlentities($post_id); ?>">

  <input id="reply-to-id" type="hidden" name ="reply_id" value="">

  <input id="draft" type="hidden" name ="draft" value="1">

 <p id="reply-username" style=" color:#0066CC;font-size: 0.812rem;"></p>

 <textarea id="text" style="min-height: 150px;" minlength="5" name="text" placeholder="Write your reply..." onfocus="draft_reply();"  title="Not more than 2000 characters" maxlength="2000" required><?php echo $reply_draft != null ? htmlentities($reply_draft->text) : ""; ?></textarea>
   <br>
      <br>
      <?php list($attach_image,$max_size) = max_upload_file_size(); ?> 
      <label for="img"><?php echo $attach_image; ?></label>
      <?php echo $max_size; ?>
    <input type="file" id="img" style="cursor: pointer;" name="image_upload" accept="image/*"> <input id="stop_drafting_on_submit" style="float:right;margin-top:-5px;" type="submit" name="submit" value="Post reply"> 
    <br>
    </form>
<?php 
}
 else{
  echo "<br><br><center><span class='reply_info'><a title='Do not worry, you would be redirect back to this page right after login ;)' href='login?".return_to_link($server_url)."'>Please log in to reply here.</a></span></center><br>";
 }
//free results in memory after loop.
 $reply_result->free_result();  
?>    

<br>
 <br>
 <div id="scroll-target"></div>

<!--Related posts part-->
<hr>
  <p class="relatedpost">Related Questions</p>

<?php
//get questions related to the forum question.//refactor
$related_post_result = Post::find_related_post($post->topic,$post->title); 

 //display list from the result set.
if($related_post_result->num_rows >= 1){
  while ($related_post = $related_post_result->fetch_object()) {

   //get related reply results.
  $related_replies_num = Reply::count_by_questionid($related_post->id); ?>
<div>

</span> 
  <p><a class="posts_related" href="<?php echo Post::forum_link($related_post->id,$related_post->title); ?>"><b><?php echo htmlentities($related_post->title); ?></b></a>
  </p>   
  <p>
<span style="color:gray;font-size:0.812rem;"><?php echo number_format($related_replies_num); ?> <?php echo num_grammar($related_replies_num,"reply","replies") ?>&nbsp; Views: <?php echo htmlentities(number_format($related_post->views)); ?></span>
     </p>

</div>

<?php
 }
}else { echo "<br><br><center style='color:gray;'> No questions are related to this question yet. </center><br><br><br>"; }

//free results in memory after loop.
 $related_post_result->free_result(); 
?>
<br>

    </div>

<!-- Report -->
<?php include('report.php');?>
<?php $add_new_post = Post::add_new_post($post->topic);  ?>
<?php include("layouts/rightside.php"); ?>
<?php include("layouts/footer.php"); ?> 
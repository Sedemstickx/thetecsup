<?php require_once "includes/initialize.php"; ?>

<?php
  $topic_name = $_GET['topic'];

  //return selected question from database.
   $topic = Topic::find_by_topic(htmlentities($_GET['topic']));
?>

<?php $page_title = htmlentities($topic_name) .  " - ".$site_title.""; $active_topic = 'id="active"'; ?>
<?php include("layouts/header.php"); ?>

  <div id="left"> 
<!--topic part-->

<?php
  //prevent getting Trying to get property of non-object error
  $topic_icon = $topic != null ? htmlentities($topic->icon) : "";
  $topic_about = $topic != null ? htmlentities($topic->about) : "";

  //process image if one exist.
  $image_url = display_image($topic_icon);

   $post_count=Post::count_by_topic($topic_name);

   $topic_topic = !empty($topic->topic) ? htmlentities($topic->topic) : "General Discussions";
?>

<div class="topic-list">
 
   
 <div class="topic-icon-div"><img src="<?php echo $image_url; ?>" alt=" " class="topic-icon">
 </div>
  <div class="topic-name-div" style="padding-top:30px;margin-bottom:30px;"><span><b style="font-size: 1.562rem;padding-top:59px;"><?php echo htmlentities($topic_name); ?></b></span>
  </div>
  <p> <?php echo htmlentities($topic_about); ?></p>
<?php 
if ($topic_topic != "Unorganized") {
?>   
   <p> <a href="ask?topic=<?php echo urlencode($topic_topic); ?>">Ask a question</a> | <a href="post_tip?topic=<?php echo urlencode($topic_topic); ?>">Write a tip</a></p> 
<?php
}
?>
</div>
<hr>

<!--Question list part-->
   <span style="color:gray;float:right;font-size:0.875rem;"><?php echo number_format($post_count); ?> <?php echo num_grammar($post_count,"post","posts") ?>
  </span> 
  <b>Post</b>
 <br>
  <br>
<?php
$pagination = new Pagination($per_page=15,$total_count=Post::count_by_topic($topic_name));

$post_result = Post::find_by_topic($topic_name,$per_page,$pagination->offset()); ;

if($topic == null){
  echo "<br><br><center style='color:gray;'> No posts are related to this topic or topic doesn't exist yet. </center><br><br><br>";
 }
elseif($post_result->num_rows >= 1){

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
  echo "<br><br><center style='color:gray;'> No posts are related to this topic yet. </center><br><br><br>";
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
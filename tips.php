<?php require_once "includes/initialize.php"; ?>

<?php
$pagination = new Pagination($per_page=15,$total_count=Post::count_all_posted_tips());

$post_result = Post::find_all_posted_tips($per_page,$pagination->offset()); 
?>

<?php $page_title = "Tips - ".$site_title.""; $active_header = 'id="active"'; ?>
<?php include("layouts/header.php"); ?>

  <div id="left"> 

  <div class="left-right-items">
  <h1><?php echo $statement = page_conditional_statement("Latest tips", "Tips"); ?> </h1> 
   <a class="askquestionbox" href="post_tip">Write a tip</a>
   </div> 

  <hr>
<!--tips list-->  
<?php
if($post_result->num_rows > 0){

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
  echo "<br><br><center style='color:gray;'> No tips have been written yet. </center><br><br><br>";
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

<?php require_once "includes/initialize.php"; ?>

<?php
//tip title.
 $tip_title ="";
 if (isset($_GET['title'])) {$tip_title = $_GET['title'];}

  $post_id = $_GET['id'];

   //return selected question from database.
   $post = Post::find_by_post(htmlentities($post_id)); 

   $post->update_views();
?>

<?php $page_title = htmlentities($post->title) . " - ".$site_title.""; ?>
<?php include("layouts/header.php"); ?>
<?php 
//if post doesn't exist in database redirect user to homepage and tell them that the page doesn't exist.
if($post == null){

$session->message("Page doesn't exist :(");

 redirect_to($home);} 
 ?>

  <div id="left"> 
<!--Question part-->

<?php 
$type = "tip";
include 'refactor/postDisplay.php'; 
?>

<br>
 <br>

<!--Related tips part-->
<hr>
  <p class="relatedpost">Related Tips</p>

<?php
//get questions related to the forum question.
$related_post_result = Post::find_related_tips($post->topic,$post->type,$post->title); 

 //display list from the result set.
if($related_post_result->num_rows >= 1){
  while ($related_post = $related_post_result->fetch_object()) {
 
   //get reply results.
  $related_replies_num = Reply::count_by_questionid($related_post->id); ?>
<div>

</span> 
  <p><a class="posts" href="<?php echo Post::tip_link($related_post->id,$related_post->title); ?>"><b><?php echo htmlentities($related_post->title); ?></b></a>
  </p>   
  <p>
 <span style="color:gray;font-size:0.812rem;">Views: <?php echo htmlentities(number_format($related_post->views)); ?></span>
     </p>

</div>
<?php
 }
}else 
     { echo "<br><br><center style='color:gray;'> No tips are related to this tip yet. </center><br><br><br>"; }

//free results in memory after loop.
 $related_post_result->free_result();      
?>


    </div>

<!-- Report -->
<?php include('report.php');?>
<?php $add_new_post = Post::add_new_post($post->topic,"post_tip","Add a tip"); ?>
<?php include("layouts/rightside.php"); ?>
<?php include("layouts/footer.php"); ?> 
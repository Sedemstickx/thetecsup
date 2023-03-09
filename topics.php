<?php require_once "includes/initialize.php"; ?>

<?php
$pagination = new Pagination($per_page=30,$total_count=Topic::count_all());

$topic_result = Topic::find_all($per_page,$pagination->offset()); 
?>

<?php $page_title = "Topics - ".$site_title.""; $active_topic = 'id="active"'; ?>
<?php include("layouts/header.php"); ?>

  <div id="left"> 
<!--Topics list-->
      
  <h2>Topics</h2>

<?php
if($topic_result->num_rows >= 1){

 //display list of questions posted.
  while ($topic = $topic_result->fetch_object()) {

  //process image if one exist.
   $image_url = display_image($topic->icon);

   $post_count=Post::count_by_topic($topic->topic);
?>

<div class="topic-list">
 
  <p>
  <span style="color:gray;float:right;font-size:0.875rem;"><?php echo number_format($post_count); ?> <?php echo num_grammar($post_count,"post","posts") ?>
  </span>  
 <div class="topic-icon-div"><a href="<?php echo Topic::link($topic->topic); ?>" title="<?php echo htmlentities($topic->about); ?>"><img src="<?php echo $image_url; ?>" alt="image" class="topic-icon-list"></a>
 </div>
  <div class="topic-name-div"><a href="<?php echo Topic::link($topic->topic); ?>" title="<?php echo htmlentities($topic->about); ?>"><b style="font-size: 1.125rem;"><?php echo htmlentities($topic->topic); ?></b></a>
  </div>
     </p>    

</div>
<?php
 }
}
 else{
  echo "<br><br><center style='color:gray;'> No topics are available yet. </center><br><br><br>";
 }
//free results in memory after loop.
 $topic_result->free_result(); 
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
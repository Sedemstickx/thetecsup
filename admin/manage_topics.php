<?php require_once "../includes/initialize.php"; ?>
<?php $page_title = "Manage topics"; ?>

<?php 
$pagination = new Pagination($per_page=30,$total_count=Topic::count_all());

$topic_result = Topic::find_all($per_page,$pagination->offset()); 

$server_url = $_SERVER['REQUEST_URI'];
?>

<?php include("../layouts/admin_header.php"); ?>

<?php if($page_title == "Manage topics"){ $c_bold = "font-weight:bold;background-color:#555;"; }; ?>

<?php include("../layouts/admin_navigation.php"); ?>

  <div id="admin-right">

<?php echo $nav; ?>

<!--Topics list-->
      
  <h2>Manage topics (<?php echo number_format(Topic::count_all()); ?>)</h2>

<br>
<a class="admin-options-link" href="create_topic">+ Add a topic</a>
<br>
 <br>

<?php
if($topic_result->num_rows >= 1){
 //display list of questions posted.
  while ($topic = $topic_result->fetch_object()) {

  //process image if one exist.
   $image_url = display_image($topic->icon);

   $post_count=Post::count_by_topic($topic->topic);
?>

<div class="admin-list">
 
  <p> 
 <span style="float:right;color:silver;margin-top:5px;"><a class='status' href="edit_topic?id=<?php echo urlencode($topic->id);?>&<?php echo return_to_link($server_url);?>">Edit</a></span>  
 <div class="topic-icon-div"><a href="<?php echo Topic::link_admin($topic->topic); ?>" title="<?php echo htmlentities($topic->about); ?>"><img src="<?php echo admin_image_loc($image_url); ?>" alt="image"  class="topic-icon-list" ></a>
 </div>
  <div class="topic-name-div"><a href="<?php echo Topic::link_admin($topic->topic); ?>" title="<?php echo htmlentities($topic->about); ?>"><b style="font-size: 1.125rem;"><?php echo htmlentities($topic->topic); ?></b></a>
  </div>
     </p>    
  <span style="color:gray;font-size:0.875rem;"><?php echo number_format($post_count); ?> <?php echo num_grammar($post_count,"post","posts") ?>
  </span> 

</div>
<?php
 }
} 
else{
  echo "<br><br><center style='color:gray;'> Topics are empty :(. </center><br><br><br>";
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

<?php include("../layouts/admin_footer.php"); ?>
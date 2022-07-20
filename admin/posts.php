<?php require_once "../includes/initialize.php"; ?>
<?php $page_title = "Manage posts"; ?>

<?php
$pagination = new Pagination($per_page=15,$total_count=Post::count_all());

$post_result = Post::find_all($per_page,$pagination->offset()); 
?>

<?php include("../layouts/admin_header.php"); ?>

<?php if($page_title == "Manage posts"){ $q_bold = "font-weight:bold;background-color:#555;"; }; ?>

<?php include("../layouts/admin_navigation.php"); ?>

  <div id="admin-right">

<?php echo $nav; ?>

     <h1>Manage posts (<?php echo number_format(Post::count_all()); ?>)</h1>

<?php echo display_message(); ?>
     
<?php 
if($post_result->num_rows >= 1){
 //display list of posts posted.
  while ($post = $post_result->fetch_object('Post')) {

  //get user profile pic location.
  $pic = get_pic_location($post->pic);

 //get reply results.
  $reply_result_num = Reply::count_by_questionid($post->id);

  if ($post->draft == 1) {
    $draft = "Draft";
  }
   else{$draft = "";}

  list($tip,$asked_or_written) = $post->post_type($post->type);

   if ($post->type == "tip") {
     $url_page = Post::tip_link($post->id,$post->title);
   }else{
      $url_page = Post::forum_link($post->id,$post->title);
   }
?>

<div class="admin-list">

<?php echo $tip; ?>
<span style="float:right;color:silver;margin-top:5px;"><a class='delete' onclick="return confirm('Confirm deletion <?php echo substr(htmlentities($post->title),0,50) . " ..."; ?>')" href="<?php echo Post::admin_del_link($post->id);?>">Delete</a>
</span>  <p><a class="posts" href="../<?php echo $url_page; ?>"><b><?php echo htmlentities($post->title); ?></b></a></p> 
  <p>
    <span>In <a class="post-topic" href="<?php echo Topic::link_admin($post->topic); ?>"><?php echo htmlentities($post->topic); ?></a>
     <?php echo $asked_or_written; ?> <a class="profile-link" href="<?php echo User::profile_link_admin($post->username); ?>  "><?php echo User::profile_pic_admin($pic); ?><b><?php echo htmlentities($post->username); ?></b></a></span>
 
<br>

<span style="color:gray;font-size:0.812rem;">
<?php if ($post->type != "tip") { ?>
  <?php echo number_format($reply_result_num); ?> <?php echo num_grammar($reply_result_num,"reply","replies") ?>&nbsp; 
<?php } ?>
  Views: <?php echo htmlentities(number_format($post->views)); ?></span>

<span class="admin-date-time"><?php echo date_converter($post->date). " at " . time_converter($post->time); ?></span>
<br>
<span style="color:gray;font-size:0.812rem;"><?php echo $draft; ?></span>
     </p>

</div>
<?php
 }
}
 else{
  echo "<br><br><center style='color:gray;'> No questions have been asked. </center><br><br><br>";
 }
//free results in memory after loop.
 $post_result->free_result(); 
?>
 <br>
<div id="pagination">
 <?php
//Provide page links.
$pagination->page_links();
list($page_number,$total_pages) = $pagination->page_number();
?>
   </div>
 <br>
<span style="color:#333;float:right;"><?php echo $page_number; ?> of  <?php echo number_format($total_pages); ?></span> <br> 
 
  </div>

<?php include("../layouts/admin_footer.php"); ?>
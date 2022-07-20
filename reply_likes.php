<?php require_once "includes/initialize.php"; ?>

<?php 
 $like_reply_class = new LikeReply();
 $reply_likes_num = LikeReply::count_by_like_id($_GET['lid']);

$pagination = new Pagination($per_page=15,$total_count=LikeReply::count_by_like_id($_GET['lid']));

$likes_results = LikeReply::find_all_by_like_id($_GET['lid'],$per_page,$pagination->offset()); 
?>

<?php $page_title = $reply_likes_num." ". $like_reply_class->num_grammar($reply_likes_num,"like","likes","likes")." - ".$site_title."";?>
<?php include("layouts/header.php"); ?>

  <div id="left" class="middle-spacing"> 

<h1><?php echo $reply_likes_num; ?> <?php echo $like_reply_class->num_grammar($reply_likes_num,"like","likes","likes") ?></h1>

<?php
if($likes_results->num_rows >= 1){
 //display list of users that like the selected reply.
  while ($like_reply = $likes_results->fetch_object()) {

    //get user profile pic location.
  $pic = get_pic_location($like_reply->pic);  
?>

<div>

<p>
<div class="left-right-items no-margin">
  <div class="flex">
<div class="profile-div"><a class="profile-link" href="<?php echo User::profile_link($like_reply->username); ?>  "><img src="<?php echo $pic; ?>" alt="image" class="profile-pic-small"></a></div> 
<div class="name-div"><a class="profile-link" href="<?php echo User::profile_link($like_reply->username); ?>  "><b><?php echo htmlentities($like_reply->username); ?></b></a></div> 
  </div>
<div style="color:gray;"> Points: <?php echo htmlentities($like_reply->points); ?>
  </div>  
  </div>
</p>

</div>

<?php
 }
}
 else{
  echo "<br><br><br><center style='color:gray;'> No person has liked this reply yet. </center><br><br><br>";
 } 
//free results in memory after loop.
$likes_results->free_result();  
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

<?php include("layouts/footer.php"); ?>
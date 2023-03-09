<?php require_once "../includes/initialize.php"; ?>
<?php $page_title = "Manage replies"; ?>

<?php
$pagination = new Pagination($per_page=15,$total_count=Reply::count_all());

$reply_result = Reply::find_all($per_page,$pagination->offset());
?>

<?php include("../layouts/admin_header.php"); ?>

<?php if($page_title == "Manage replies"){ $r_bold = "font-weight:bold;background-color:#555;"; }; ?>

<?php include("../layouts/admin_navigation.php"); ?>

  <div id="admin-right">

<?php echo $nav; ?>

     <h1>Manage replies (<?php echo number_format(Reply::count_all()); ?>)</h1>

<?php echo display_message(); ?>
    
<!--Image enlarger-->
<?php 
//Image enlarger
image_enlarge_div();
?>
     
<?php  
 //display list of replies by the user.
if($reply_result->num_rows >= 1){

 //create likereply instance
$like_reply_class = new LikeReply();

  //display list of replies.
  while ($reply = $reply_result->fetch_object('Reply')) {

  //get user profile pic location.
  $pic = get_pic_location($reply->pic);  

     //process image if one exist.
   $display_reply_image = $reply->display_image_admin($reply->image);  

   //show best answers.
   $best_answer = "";
   if ($reply->b_answer == 1) {
  $best_answer = "<span class='best-answer'>Best answer &#10004;</span><br><br>";
  }  


  if ($reply->draft == 1) {
    $draft = "Draft";
  }
   else{$draft = "";}
?>

<div class="replies-list">

<?php echo $best_answer; ?>

<?php
 if ($reply->b_answer == 0) {
 ?> 
<span style="float:right;color:silver;margin-top: 5px;"><a class='delete' onclick="return confirm('Confirm deletion: <?php echo substr(htmlentities($reply->text),0,50) . " ..."; ?>')" href="<?php echo Reply::admin_del_link($reply->id);?>">Delete</a>
</span>
<?php
}
?>
<div class="profile-div"><a class="profile-link" href="<?php echo User::profile_link_admin($reply->username); ?>  "><?php echo User::profile_pic_admin($pic); ?></a></div> 
<div class="name-div"><a class="profile-link" href="<?php echo User::profile_link_admin($reply->username); ?>  "><b><?php echo htmlentities($reply->username); ?></b></a></div> 
<p><?php echo nl2br(create_hyperlinks($reply->text)); ?></p>

  <?php echo $display_reply_image; ?>

<!--Number of likes-->
<?php 
 echo $number_of_likes = $like_reply_class->num_of_likes_admin($reply->id);
?>
<br>

<span>Replied in: <a style="color:#333;" href="<?php echo Post::forum_link_admin($reply->replied_id,$reply->title); ?>"><span style="font-size:1.062rem;color:#333;font-weight:bold;"><?php echo htmlentities($reply->title); ?></span></a>
<span class="admin-date-time"><?php echo date_converter($reply->date). " at " . time_converter($reply->time); ?></span>
<br>
<span style="color:gray;font-size:0.812rem;"><?php echo $draft; ?></span>
<br>
<br></span>
  </div> 
<?php
  }
 }
 else{
  echo "<br><br><center style='color:gray;'> No replies have been posted. </center><br><br><br>";
  }
  //free results in memory after loop.
 $reply_result->free_result();
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
<?php require_once "../includes/initialize.php"; ?>
<?php $page_title = "Manage report"; ?>

<?php
$server_url = $_SERVER['REQUEST_URI'];//refactor page url

//return the selected report details from the database.
if (isset($_GET['id'])) {

  $report = Report::find_by_id($_GET['id']); 

  //change report read status if the given report page is opened.
  $report->update_view_status($report->id);
}
?>

<?php include("../layouts/admin_header.php"); ?>

<?php if($page_title == "Manage report"){ $rep_bold = "font-weight:bold;background-color:#555;"; }; ?>

<?php include("../layouts/admin_navigation.php"); ?>
<?php 
//if report doesn't exist in database redirect user to report_list page.
if($report == null){redirect_to("reports_list");} 
?>

  <div id="admin-right">

<?php echo $nav; ?>

     <h1>Manage report</h1>

     <?php echo display_message(); ?>

<b style="color:orange;" >Note: Please Remove report if there are no violations or the report is irrelevant. </b>
<br>
 <br>

<span style="float:right;color:silver;margin-top:18px;"><a class='delete' onclick="return confirm('Confirm deletion')" href="delete_report?id=<?php echo urlencode($report->id);?>">Remove report</a>
</span>

<h3>Report from: <a class="profile-link" href="<?php echo User::profile_link_admin($report->username); ?>  "><b><?php echo htmlentities($report->username); ?></b></a></h3>

<p>Subject: <b><?php echo htmlentities($report->subject); ?></b> </p>

<p> Email: <b><?php echo htmlentities($report->email); ?></b> </p>
<br>

<p> <b style="color:green;">Message:</b> <?php echo htmlentities($report->message); ?> </p>

<?php 
 $feedback = '<b style="color:green;" >Report issue has been solved. You can remove the posted report.</b>';

if($_GET['type'] == "Post") {
  $post = Post::find_by_id(htmlentities($_GET['activity_id']));

   if($post->id != null){
?>
<fieldset>

<span style="float:right;color:silver;margin-top:5px;"><a class='delete' onclick="return confirm('Confirm deletion <?php echo substr(htmlentities($post->title),0,50) . " ..."; ?>')" href="<?php echo Post::admin_del_link($post->id,"report");?>">Delete Post</a>
</span> 

<p>Post: <?php echo htmlentities($post->title); ?></p>

<span style="font-size: 0.937rem;color:gray;">Posted on: <?php echo date_converter($post->date). " at " . time_converter($post->time); ?></span>

</fieldset>
<?php
 }
 else{
  echo $feedback;
 }
}
elseif($_GET['type'] == "Reply") {

  $reply = Reply::find_by_id(htmlentities($_GET['activity_id']));

 if($reply != null){
       //process image if one exist.
   $display_reply_image = $reply->display_image_admin($reply->image);

   //show best answers.
   $best_answer = "";
   if ($reply->b_answer == 1) {
  $best_answer = "<span class='best-answer'>Best answer &#10004;</span>";
  }  
?>
<fieldset>

<?php echo $best_answer; ?>

<?php
 if ($reply->b_answer == 0) {
 ?> 
 <span style="float:right;color:silver;margin-top: 0px;"><a class='delete' onclick="return confirm('Confirm deletion: <?php echo substr(htmlentities($reply->text),0,50) . " ..."; ?>')" href="<?php echo Reply::admin_del_link($reply->id,"report");?>">Delete Reply</a>
</span>
<?php
}
?>

<p>Post: <?php echo htmlentities($reply->text); ?></p>

  <?php echo $display_reply_image; ?>

<span style="font-size: 0.937rem;color:gray;">Posted on: <?php echo date_converter($reply->date). " at " . time_converter($reply->time); ?></span>

</fieldset>
<?php
 }
 else{
  echo $feedback;
 }
}
elseif($_GET['type'] == "user"){

$user = User::find_by_id(htmlentities($_GET['activity_id']));

  //get user profile pic location.
  $pic = get_pic_location($user->pic);  

  $status = $user->block_status($user->block);
  $admin_status = $user->admin_status($user->admin);
?>

<fieldset>
  
<span style="float:right;color:silver;margin-top:5px;"><a class='status' href="block?id=<?php echo urlencode($user->id);?>&<?php echo return_to_link($server_url);?>">status (<?php echo $status; ?>)</a>
</span>
  <p>
<div class="profile-div"><a class="profile-link" href="<?php echo User::profile_link_admin($user->username); ?>"><?php echo User::profile_pic_admin($pic); ?></a></div> 
<div class="name-div"><a class="profile-link" href="<?php echo User::profile_link_admin($user->username); ?>"><b><?php echo htmlentities($user->username); ?></b></a></div> 
<span>Email: <b><?php echo htmlentities($user->email);?></b></span>     <span class="admin-date-time"><?php echo date_converter($user->date). " at " . time_converter($user->time); ?></span>

     </p>

</fieldset>

<?php
}
?>

  </div>

<?php include("../layouts/admin_footer.php"); ?>
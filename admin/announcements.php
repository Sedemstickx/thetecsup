<?php require_once "../includes/initialize.php"; ?>
<?php $page_title = "Announcements"; ?>

<?php 
$pagination = new Pagination($per_page=15,$total_count=Announcement::count_all());

$announcements_result = Announcement::find_all($per_page,$pagination->offset()); 

$msg ="";
?>

<?php include("../layouts/admin_header.php"); ?>

<?php if($page_title == "Announcements"){ $announce_bold = "font-weight:bold;background-color:#555;"; }; ?>

<?php include("../layouts/admin_navigation.php"); ?>

  <div id="admin-right">

<?php echo $nav; ?>

<!--Announcements list-->
      
  <h2>Announcements (<?php echo number_format(Announcement::count_all()); ?>)</h2>

<?php echo display_message($msg); ?>

<br>
<a class="admin-options-link" href="create_announcement">+ Add an announcement</a>
<br>
 <br>

<?php
if($announcements_result->num_rows >= 1){
 //display list of announcements posted.
  while ($announcement = $announcements_result->fetch_object()) {
?>

<div class="admin-list">
 
  <p> 

<span style="float:right;color:silver;margin-top:5px;"><a class='delete' onclick="return confirm('Confirm deletion <?php echo substr(htmlentities($announcement->title),0,50) . " ..."; ?>')" href="delete_announcement?id=<?php echo urlencode($announcement->id);?>">Delete</a></span>

<a style="color:#333;" href="#"><span style="font-size:1.062rem;color:#333;font-weight:bold;"><?php echo htmlentities($announcement->title); ?></span></a>
<br>
 <br>
<span style="color:#333;"><?php echo htmlentities($announcement->message); ?></span>
<br>
<span class="admin-date-time">Created on: <?php echo date_converter($announcement->date); ?></span>
</p>
<br>
</div>
<?php
 }
} 
else{
  echo "<br><br><center style='color:gray;'> Announcements are empty :(. </center><br><br><br>";
 }
//free results in memory after loop.
 $announcements_result->free_result(); 
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
<?php require_once "../includes/initialize.php"; ?>
<?php $page_title = "Reports list"; ?>

<?php
$pagination = new Pagination($per_page=15,$total_count=Report::count_all());

$report_result = Report::find_all($per_page,$pagination->offset());
?>

<?php include("../layouts/admin_header.php"); ?>

<?php if($page_title == "Reports list"){ $rep_bold = "font-weight:bold;background-color:#555;"; }; ?>

<?php include("../layouts/admin_navigation.php"); ?>

  <div id="admin-right">

<?php echo $nav; ?>

     <h1>Manage reports (<?php echo number_format(Report::count_all()); ?>)</h1>

     <a style='color:Green;font-weight:bold;' href="export_report.php">Export reports</a>

<?php echo display_message(); ?>
     
<?php 
if($report_result->num_rows >= 1){

 //display list of questions posted.
  while ($report = $report_result->fetch_object('Report')) {

  list($viewed,$read) = $report->check_view($report->viewed);
?>

<div class="admin-list">

<span style="float:right;color:silver;margin-top:5px;color:orange"><a href="manage_report?id=<?php echo urlencode($report->id); ?>&activity_id=<?php echo urlencode($report->activity_id); ?>&type=<?php echo urlencode($report->type); ?>&view=<?php echo urlencode($viewed); ?>"><?php echo $read; ?></a>
</span>
<p>Subject: <a class="report-subject" href="manage_report?id=<?php echo urlencode($report->id); ?>&activity_id=<?php echo urlencode($report->activity_id); ?>&type=<?php echo urlencode($report->type); ?>&view=<?php echo urlencode($viewed); ?>"><b><?php echo htmlentities($report->subject); ?></b></a></p> 
  <p>
    <span>Username: <a class="profile-link" href="<?php echo User::profile_link_admin($report->username); ?>  "><b><?php echo htmlentities($report->username); ?></b></a></span>
     </p>
     <p>Type: <b style="font-size: 0.875rem;"><?php echo htmlentities($report->type); ?></b>
<span class="admin-date-time">Reported on: <?php echo date_converter($report->date). " at " . time_converter($report->time); ?></span>
     </p> 

</div>
<?php
 }
}
 else{
  echo "<br><br><center style='color:gray;'> No reports have been submitted yet. </center><br><br><br>";
 }
//free results in memory after loop.
 $report_result->free_result(); 
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
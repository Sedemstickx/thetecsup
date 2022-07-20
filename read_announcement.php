<?php require_once "includes/initialize.php"; ?>
<?php
 if (isset($_GET['view'])) {

    $user = new User(); 
    $user->update_read_announcements_status($session->user_id);
 }
?>

<?php $page_title = "Announcement - ".$site_title.""; ?>
<?php include("layouts/header.php"); ?>
<?php if(!$session->is_logged_in()){redirect_to($home);} ?>

  
     <div id="middle">

<?php 
if ($_GET['id'] != null) {

$announcement = Announcement::find_by_id($_GET['id']);
?>

    <h2><?php echo htmlentities($announcement->title);?></h2>

<p><?php echo nl2br(create_hyperlinks($announcement->message));?></p>

<span class="date-time"><?php echo date_converter($announcement->date);?></span>
<?php
}
 else{
 $session->message("Page doesn't exist :(");

 redirect_to($home);
 }
?>     </div>


<?php include("layouts/footer.php"); ?>
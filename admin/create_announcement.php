<?php require_once "../includes/initialize.php"; ?>
<?php $page_title = "Create announcement"; ?>

<?php 
$server_url = $_SERVER['REQUEST_URI'];

$msg = "";
if(isset($_GET['reset']) && $_GET['reset'] == "yes"){
  
  $user_class = new User();
  $user_class->reset_sent();

}
?>

<?php include("../layouts/admin_header.php"); ?>

<?php if($page_title == "Create announcement"){ $announce_bold = "font-weight:bold;background-color:#555;"; }; ?>

<?php include("../layouts/admin_navigation.php"); ?>

  <div id="admin-right">

<?php echo $nav; ?>

     <h1>Create an announcement</h1>

<center>
  <span id="announce_feedback" class="feedback-messages" style="display:none;">
  <span id="announce_status" style="margin: auto;"></span> 
  <span id="close_feedback" style="margin-top:-4px;font-size:2rem !important;display:none;" class="close_button" title="close" onclick="this.parentElement.style.display = 'none';">&times;</span>
   &nbsp; </span>
</center>

<p>Number of sent emails : <b id="sent_count"><?php echo User::count_sent_emails(); ?></b> / <b><?php echo User::count_all_emails(); ?></b></p>

<p>Please reset sent email status if it's number matches total number of emails. <a onclick="return confirm('Confirm reset')" href="create_announcement?reset=yes"> Click here to reset sent email status</a></p>

<b style="font-size: 1.062rem;">Enter a new announcment title and add details to it.</b>

 <p><b>&lt;b&gt;</b> and <b>&lt;a&gt;</b> tags can be used by developers when writing the message.</p>

    <form method="post" onsubmit="send_announcement(event)">
    <?php echo csrf_token(); ?>

    <p><label for="announce_title">Title</label> 
      <br>
       <input id="announce_title" type="text" name="title" placeholder="e.g Welcome to thetecsup" value="<?php if(isset($_POST["title"])){ echo htmlentities($_POST["title"]); } ?>" maxlength="200" required>
       </p>
    <p><label for="announce_msg">Message</label>
      <br>
    <textarea id="announce_msg" name="message" placeholder="Enter details about the announcement here..." title="Not more than 2000 characters" maxlength="2000" required><?php if(isset($_POST["message"])){ echo htmlentities($_POST["message"]); } ?></textarea>
    </p>
   <p>
  <input id="send_emails" name="send_emails" type="checkbox" value="" onclick="$(this).val(1);"> Send this announcement to user emails. A limit of <b>50</b> must be sent per hour, <b>500</b> per day.
    </p>
    <input type="submit" value="Submit">&nbsp;&nbsp; <input type="reset" value="Reset">&nbsp;&nbsp; <a href="announcements">Cancel</a>

    </form>


  </div>

<?php include("../layouts/admin_footer.php"); ?>
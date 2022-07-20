<?php require_once "includes/initialize.php"; ?>

<?php
//Check if a topic is attached to the link to automatically add the topic.
$get_topic = "";

if (isset($_GET['topic'])) {
   $get_topic = $_GET['topic'];
 } 

//get draft if available.
$post = Post::find_by_draft();   

$type = "tip";

   $msg="";
?>

<?php $page_title = "Post a tip - ".$site_title.""; $active_header = 'id="active"'; ?>
<?php include("layouts/header.php"); ?>
<?php if(!$session->is_logged_in()){$session->message("You must be logged in to post a tip."); redirect_to("login?".return_to_link("post_tip")."");} ?>

<div id="left"> 

<h1>Post a tech tip</h1>

<p>
Have a question to ask? <a href="ask"> Ask a question</a>
</p>

<?php include 'refactor/postForm.php'; ?>  

</div>

<?php include("layouts/rightside.php"); ?>
<?php include("layouts/footer.php"); ?> 
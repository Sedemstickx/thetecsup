<?php require_once "includes/initialize.php"; ?>

<?php  
//Check if a topic is attached to the link to automatically add the topic.
$get_topic = "";

if (isset($_GET['topic'])) {
   $get_topic = $_GET['topic'];
 } 

//get draft if available.
$post = Post::find_by_draft(); 

$type = "question";

   $msg="";
?>

<?php $page_title = "Post question - ".$site_title.""; $active_ask = 'id="active"'; ?>
<?php include("layouts/header.php"); ?>
<?php if(!$session->is_logged_in()){$session->message("You must be logged in to post a question."); redirect_to("login?".return_to_link("ask")."");} ?>

<div id="left"> 

<h1>Ask a question</h1>

<p>
Want to share your knowledge? <a href="post_tip"> write a tip</a>
</p>

<?php include 'refactor/postForm.php'; ?> 

</div>

<?php include("layouts/rightside.php"); ?>
<?php include("layouts/footer.php"); ?> 
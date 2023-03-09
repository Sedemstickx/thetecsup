<?php require_once "../includes/initialize.php"; ?>
<?php $page_title = "Add topic"; ?>

<?php 
  $server_url = $_SERVER['REQUEST_URI'];

$msg = "";
if(isset($_POST['submit']) && csrf_protect()){
  
  $topic_class = new Topic(); 

  $result = $topic_class->create();

   if($result){
    //success
      $session->message("You have succesfully added a new topic.");

      redirect_to($server_url);
    }
   else {
    //failure
     $msg = "<br><center><span class='error-feedback-messages'>Something went wrong.</span></center>";
    }
}
?>

<?php include("../layouts/admin_header.php"); ?>

<?php if($page_title == "Add topic"){ $c_bold = "font-weight:bold;background-color:#555;";}; ?>

<?php include("../layouts/admin_navigation.php"); ?>

  <div id="admin-right">

<?php echo $nav; ?>

     <h1>Add a topic</h1>

     <?php echo display_message($msg); ?>

<b style="font-size: 1.062rem;">Enter a new topic and add details to it.</b>

    <form action="<?php echo htmlentities($server_url); ?>" enctype="multipart/form-data" method="post">
    <?php echo csrf_token(); ?>

    <p><label for="name">Topic name</label>
      <br>
       <input id="name" type="text" name="topic" placeholder="Topic" value="" maxlength="50" required>
       </p>
    <p><label for="about">About topic</label> 
      <br>
    <textarea id="about" name="about" placeholder="Enter details about the topic here..." title="Not more than 150 characters" maxlength="150" required></textarea>
    </p>
    <br>
    <label for="upload" style="display:block;font-size: 0.812rem;color:#0066CC;">topic icon. (Max 2MB)</label>
       <?php list($attach_image,$max_size) = max_upload_file_size(2); ?>
   <?php echo $max_size; ?> 
    <input id="upload" type="file" name="image_upload" accept="image/*" required>
      <br>
       <br>
    <input type="submit" name="submit" value="Add topic">&nbsp;&nbsp; <a href="manage_topics">Cancel</a>

    </form>

  </div>

<?php include("../layouts/admin_footer.php"); ?>
<?php require_once "../includes/initialize.php"; ?>
<?php $page_title = "Edit topic details"; ?>

<?php 
$return = return_to();

$server_url = $_SERVER['REQUEST_URI'];

$msg = "";
if(isset($_POST['submit']) && csrf_protect()){
  
 $topics = new Topic(); 

  $result = $topics->update(htmlentities($_GET['id']));

   if($result){
    //success
      $session->message("You have succesfully edited this topic. <a href=". $return .">Go back to manage topics</a>");

      redirect_to($server_url);
    }
   else {
    //failure
     $msg = "<br><center><span class='error-feedback-messages'>Something went wrong.";
    }
}
?>

<?php include("../layouts/admin_header.php"); ?>

<?php if($page_title == "Edit topic details"){ $c_bold = "font-weight:bold;background-color:#555;"; }; ?>

<?php include("../layouts/admin_navigation.php"); ?>

  <div id="admin-right">

<?php echo $nav; ?>

     <h1>Edit topic details</h1>

     <?php echo display_message($msg); ?>

<?php 
  //return selected topic details from database.
   $topic = Topic::find_by_id(htmlentities($_GET['id']));

     //process image if one exist.
   $image_url = display_image($topic->icon);
?>
<b style="font-size: 1.062rem;">You can edit topic details and change topic icon.</b>

    <form action="<?php echo htmlentities($server_url); ?>" enctype="multipart/form-data" method="post">
    <?php echo csrf_token(); ?>

    <p><label for="name">Topic name</label>
      <br>
       <input id="name" type="text" name="topic" placeholder="Topic" value="<?php echo htmlentities($topic->topic); ?>" maxlength="50" required>
       </p>
    <p><label for="about">About topic</label>
      <br>
    <textarea id="about" name="about" placeholder="Enter details about the topic here..." title="Not more than 200 characters" maxlength="200" required><?php echo htmlentities($topic->about); ?></textarea>
    </p>
    <br>
    <label for="upload" style="display:block;font-size: 0.812rem;color:#0066CC;">topic icon. (Max 2MB)</label>
    <?php list($attach_image,$max_size) = max_upload_file_size(2); ?>
   <?php echo $max_size; ?>   
<img src="<?php echo admin_image_loc($image_url); ?>" alt="image" class="topic-icon-list">&nbsp; <input id="upload" type="file" name="image_upload" accept="image/*">
      <br>
       <br>
    <input type="submit" name="submit" value="Edit topic">&nbsp;&nbsp; <a href="javascript:void(0)" onclick="goBack('admindash')">Cancel</a>

    </form>

  </div>

<?php include("../layouts/admin_footer.php"); ?>
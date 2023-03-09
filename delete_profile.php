<?php require_once "includes/initialize.php"; ?>

<?php
$server_url = $_SERVER['REQUEST_URI'];

$msg = "";
  if($session->is_logged_in() && isset($_POST['submit']) && csrf_protect()){ 
 
   $user_class = new User(); 

   $result = $user_class->remove_profile($session->user_id);

    if($result){

      $session->logout();
    }
   else {
    //failure
     $msg = "<br><center><span class='error-feedback-messages'>Something went wrong, please try again.</span></center>";
    }
  }
?>

<?php $page_title = "Delete profile - ".$site_title.""; ?>
<?php include("layouts/header.php"); ?>
<?php if(!$session->is_logged_in()){redirect_to($home);} ?>

  
<div id="middle">

  <h2>Delete your profile</h2>

<hr>

<p> We are sad you want to delete your account :(. Before confirming that you would like to delete your profile, we will like to take a moment to explain the implications of deletion:</p>

<p>Deletion is irreversible, and you will have no way to regain any of your original content, should this deletion be carried out and you change your mind later on.
  Your questions and answers will remain on the site, but will be disassociated and anonymized (the author will be listed as "user<?php echo $session->user_id; ?>") and will not indicate your authorship even if you later return to the site.</p>

<p><input id="delete_terms" onchange="accept_delete_terms()" type="checkbox"> I have read the information stated above and understand the implications of having my profile deleted. I wish to proceed with the deletion of my profile.</p>
<br>
 <br>

<form action="<?php echo htmlentities($server_url); ?>" method="post">
<?php echo csrf_token(); ?>    

<input id="delete_profile" type="submit" name="submit" style="background-color:red;border: 1px solid red;opacity:0.4;cursor: not-allowed;" value="Delete profile" disabled="disabled">&nbsp;&nbsp; <a href="javascript:void(0)" onclick="goBack()">Cancel</a>
    <br>
</form>
     
     </div>


<?php include("layouts/footer.php"); ?>
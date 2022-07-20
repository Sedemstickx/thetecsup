<?php require_once "includes/initialize.php"; ?>

<?php 
$server_url = $_SERVER['REQUEST_URI'];

  $user = User::find_by_id($session->user_id);

$msg = "";
   if (isset($_POST['submit']) && csrf_protect()){ 

   $result = $user->update_password($session->user_id);

   if($result){

     //success
      $session->message("Your password has been succesfully updated.");

      $message = "Dear user,<br>
      <p>Your password has been changed.</p>
      <p>If you did not take this action, please <a href='mailto:{$site_title}@gmail.com'>Contact us</a>.</p>";

      //send email notif.
      send_email($user->email,"Password change",$message);

      redirect_to($server_url);
    } 
    else{
      //failure
     $msg = "<br><center><span class='error-feedback-messages'>You haven't entered the correct current password.</span></center>";
       }
    }
?>

<?php $page_title = "Change password - {$site_title}"; ?>
<?php include("layouts/header.php"); ?>
<?php if(!$session->is_logged_in()){redirect_to($home);} ?>

  
     <div id="middle">

    <h2>Change your password</h2>

<p>Please note that you would have to enter your <b>current password</b> to confirm you are the one.</p>

<hr>

    <form action="<?php echo htmlentities($server_url); ?>" method="post">
    <?php echo csrf_token(); ?>
    
    <p><b style="line-height:2.0em;">Your current password</b> 
      <br>
       <input type="password" name="currentpassword" placeholder="Current password" value="" required>
       </p>
    <p><b style="line-height:2.0em;">Your new password</b> 
      <br>
       <input type="password" name="newpassword" placeholder="New password" value="" required>
       </p>   
         <br>
    <input type="submit" name="submit" value="Save Changes">&nbsp;&nbsp; <a href="editprofile" >Cancel</a>
    <br>
    </form>
<br>


     </div>


<?php include("layouts/footer.php"); ?>
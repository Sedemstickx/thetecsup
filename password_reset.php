<?php require_once "includes/initialize.php"; ?>

<?php  
$server_url = $_SERVER['REQUEST_URI'];

$email = $_GET["email"];

$current_date_time = date('Y-m-d H:i:s');

$msg = "";
   if (isset($_POST['submit']) && csrf_protect() && isset($_GET["token"]) && isset($email)){

   $pwdreset = Pwdreset::find_by_token_email($_GET["token"],$email);

if ($pwdreset != null && $pwdreset->expire >= $current_date_time) {

   $user_class = new User();

   $result = $user_class->reset_password($email);

   if($result){

    //delete passwrod token.
    $pwdreset->delete($email);

     //success
      $session->message("Your password has been succesfully resetted.<a href='login'> Go to login page</a>");

      $message = "Dear user,<br>
      <p>Your password has been reset.</p>
      <p>If you did not request this reset, please <a href='mailto:{$site_title}@gmail.com'>Contact us</a>.</p>";

      //send email notif.
      send_email($email,"Password has been reset",$message);

      redirect_to($server_url);
    }
  }
  else{
    $msg="<br><br><center><span class='error-feedback-messages'>Invalid link/expired. Either you did not click the correct link from the email or your token has expired or has been already used.<a href='forgot_password'> Click here to resend your email</a></span></center>";
   }
  }
?>

<?php $page_title = "Reset password - ".$site_title.""; ?>
<?php include("layouts/header.php"); ?>
<?php if($session->is_logged_in()){redirect_to($home);} ?>

  
     <div id="middle">

    <h2>Reset your password</h2>

<p>Please enter a new password to reset the old one.</p>

<hr>

    <form action="<?php echo htmlentities($server_url); ?>" method="post">
    <?php echo csrf_token(); ?>
    

    <p><b style="line-height:2.0em;">Enter new password</b> 
      <br>
       <input type="password" name="newpassword" placeholder="******" value="" required>
       </p>   
         <br>
    <input type="submit" name="submit" value="Reset password">&nbsp;&nbsp; <a href="login">Cancel</a>
    <br>
    </form>
<br>


     </div>


<?php include("layouts/footer.php"); ?>
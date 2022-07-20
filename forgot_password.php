<?php require_once "includes/initialize.php"; ?>

<?php 
   $server_url = $_SERVER['REQUEST_URI'];

   $msg="";
   if(isset($_POST['submit']) && csrf_protect()){ 

   $user_class = new User();
   $email_result = $user_class->check_email_exist($_POST["email"]);

   if ($email_result->num_rows >= 1) {
     
     $pwdReset_class = new Pwdreset();
     //WARNING! results only should be returned.
     $result = $pwdReset_class->create_token(); 

     if($result){
      //success
      $session->message("An email has been sent to you with instructions to reset your password. Please check your email.");

      redirect_to($server_url);
     }

   }else{
    $msg="<br><br><center><span class='error-feedback-messages'>Email doesn't exist.</span></center>";
   }

   }
?>

<?php $page_title = "Forgot password - ".$site_title.""; $active_login = ''; ?>
<?php include("layouts/header.php"); ?>
<?php if($session->is_logged_in()){redirect_to($home);} ?>

    
     <div class="signup_login" style="margin:70px;">

    <h2>Forgot password</h2>
     <br>
    <center><p style="color:orange;font-size:1.125rem;">Please enter the email you used to sign up for password reset:</p></center>
     <br>
    <form action="<?php echo htmlentities($server_url); ?>" method="post">
    <?php echo csrf_token(); ?>

    <center><p>
       <input type="email" name="email" placeholder="me@example.com" value="<?php if(isset($_POST["email"])){ echo htmlentities($_POST["email"]); } ?>" required>
       </p>
        <br>
    <input type="submit" class="login_signup_submit" name="submit" value="Submit">
       <br>
         </form>      <br>
      <br>
    <p>Not a member? <a href="signup">sign up</a> now.
    </p></center>

     </div>

<?php include("layouts/footer.php"); ?> 
<?php require_once "includes/initialize.php"; ?>

<?php 
$return_to = page_return();

$msg="";

if (isset($_POST['submit']) && csrf_protect()){
   
   $user_class = new User();//create new instance 
    
   $result = $user_class->sign_up();

   if($result === true){

    //get random tokens for cookies.
    $token = bin2hex(random_bytes(16));

    $cookie_expire_time = time() + (172800 * 365);// 2 years.

    //store login token in cookies
    setcookie("tsp_token", $token, $cookie_expire_time);

    //2 years expiry date
    $login_expiry = date('Y-m-d H:i:s', strtotime("+2year"));

    $auth = new Auth();

    //create new token.
    $auth->create_token($_SESSION["username"],$token,$login_expiry);

    log_action($_SESSION["username"]);//write login info into logfile.

    //send welcome mail notification.
  $message = "Dear {$_SESSION["username"]},<br>
  <p>Welcome to {$site_title}. Thank you for creating an account with us. {$site_title} is an online tech support platform that makes solving tech issues easy and free to do.<br> 
  You can ask questions, share knowledge in forms of tips, get answers in forms of replies and also answer questions other users face. You can also now hire IT freelancers that can help solve your issue if you can't solve it yourself at an affordable price.<br>
  <br>
  Enjoy a hustle free experience with the {$site_title}. We hope you do.
  </p>
  <p><a href='https://www.{$site_title}.com'>Click here to go to the site now</a></p>
  <p>Cheers, {$site_title} team.</p>";

  send_email($_SESSION["email"],"Welcome to {$site_title}",$message);

  $session->message("Welcome to {$site_title} community {$_SESSION["username"]}.<br>Ask to get help, answer to provide help or hire an IT freelancer to solve your issue without doing it yourself. <a href='editprofile'>Click here to update your profile</a>");

  //clear username and email data from session.
  unset($_SESSION["username"]);
  unset($_SESSION["email"]);

  //return user to the previous pages which user wanted to interact with or return user to the homepage. 
  $user_class->return_to();
  }
  else{
    $msg = $result;//error messages.
  }

}
?>

<?php $page_title = "Sign up - ".$site_title.""; $active_signup = ''; ?>
<?php include("layouts/header.php"); ?>
<?php if($session->is_logged_in()){redirect_to($home);} ?>

  
     <div class="signup_login">

    <h2>Sign up</h2>

    <div class="signup">

<div class="fb_login">
  <a href="<?php echo get_fb_login_url(); ?>"><i class="fa-brands fa-facebook social-media-icons fb-v-align"></i> Sign up with Facebook</a>
</div>   

  <p class="or">- OR -</p>
   
    <form action="<?php echo htmlentities($_SERVER['REQUEST_URI']); ?>" method="post">
   <?php echo csrf_token(); ?>
    <p><label for="name">Username</label> 
      <br>
       <input id="name" type="text" name="username" placeholder="Name must be unique" value="<?php if(isset($_POST["username"])){ echo htmlentities($_POST["username"]); } ?>" maxlength="30" required>
       </p>
    <p><label for="email">Email</label>
      <br>
       <input id="email" type="email" name="email" placeholder="me@example.com" value="<?php if(isset($_POST["email"])){ echo htmlentities($_POST["email"]); } ?>" required>
       </p>
    <p><label for="password">password</label>
      <br>
      <input id="password" type="password" minlength="6" name="password" placeholder="******" value="" required>
       </p>
         <br>
    <center><input type="submit" class="login_signup_submit" name="submit" value="Signup"></center>
    <br>
    </form>

  </div>
     <center><p>
      Already a member? <a href="login<?php echo $return_to; ?>">Login</a></p>
     <hr>
    <p style="font-size:0.875rem;">By creating your account you accept our <a href="privacy">privacy policy</a> and <a href="terms">terms of use</a></p>
    </center>
     </div>

<?php include("layouts/footer.php"); ?> 
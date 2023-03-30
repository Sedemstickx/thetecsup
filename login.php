<?php require_once "includes/initialize.php"; ?>

<?php
$return_to = page_return();

$msg="";
$fb_details = null;

//if submit is set or fb state is set run below code.
if (isset($_POST['submit']) && csrf_protect() || isset($_GET['state']) && FB_APP_STATE == $_GET['state']) {

$user_class = new User();

//check if fb code is set to prevent errors.
if (isset($_GET['code'])) {

$access_token = get_access_token($_GET['code']);//get fb access token from fb code.

$fb_user_info = get_fb_user_info($access_token['access_token']);//use fb access_token to get fb user details.

$fb_details = $user_class->check_fb_id_email($fb_user_info['id'],$fb_user_info['email']);//Check if fb user has matched details on our site

}

  
//if user enters credentials and submit is set authenticate user without fb details.
if(isset($_POST['submit']) && csrf_protect()){
$result = $user_class->authenticate_user();
}   
elseif($fb_details != null){
//log in the user using data gotten from facebook if user is already signed up
$result = $user_class->authenticate_user($fb_user_info['id'],$fb_user_info['email']);

}
else{
//sign up the user using data gotten from facebook if user has never signed up
$result = $user_class->sign_up($fb_user_info['first_name'],$fb_user_info['last_name'],$fb_user_info['id'],$fb_user_info['email'],$fb_user_info['picture']['data']['url']);
}

if ($result === true) {

    //get random tokens for cookies.
    $token = bin2hex(random_bytes(16));

    $cookie_expire_time = time() + (172800 * 365);//2 years.

    //store login token in cookies
    setcookie("tsp_token", $token, $cookie_expire_time);

    //2 years expiry date
    $login_expiry = date('Y-m-d H:i:s', strtotime("+2years"));

    $auth = new Auth();

    //create new token.
    $auth->create_token($_SESSION["username"], $token,$login_expiry);

    $session->message("Welcome " . $_SESSION["username"] . "");//Succees message

    log_action($_SESSION["username"]);//write login to log file.

    //clear username and email data from session if they are set.
    if (isset($_SESSION["username"]) || isset($_SESSION["email"])) {
    unset($_SESSION["username"]);
    unset($_SESSION["email"]);
    }

    //return user to the previous pages which user wanted to interact with or return user to the homepage.
    $user_class->return_to();

}
 else{
    $msg = $result;//error messages.
 }
   
}
?>

<?php $page_title = "Login - ".$site_title.""; $active_login = ''; ?>
<?php include("layouts/header.php"); ?>
<?php if($session->is_logged_in()){redirect_to($home);} ?>


     <div class="signup_login">

    <h2>Login</h2>

<div class="signup">

<div class="fb_login">
<a href="<?php echo get_fb_login_url(); ?>"><i class="fa-brands fa-facebook social-media-icons fb-v-align"></i> Login with Facebook</a>
</div>  

   <p class="or">- OR -</p>

    <form action="<?php echo htmlentities($_SERVER['REQUEST_URI']); ?>" method="post">
    <?php echo csrf_token(); ?>

    <p><label for="name">Name/Email</label> 
      <br>
       <input id="name" type="text" name="username" placeholder="Your name or email" value="<?php if(isset($_POST["username"])){ echo htmlentities($_POST["username"]); } ?>" required>
       </p>
    <p><label for="password">Password</label>
      <br>
      <input id="password" type="password" name="password" placeholder="Your Password" value="" required>
       </p>
        <br>
    <center><input type="submit" class="login_signup_submit" name="submit" value="Login"></center>
       <br>
         </form>
</div>

    <center>
      
     <p><a href="forgot_password">Forgot password?</a>
     </p>
    <p>Not a member? <a href="signup<?php echo $return_to; ?>">Sign Up</a>
    </p>
  </center>

     </div>

 <br>
  <br>
   <br>
    <br>
     <br>
      <br>
       <br>
        <br>
         <br>

<?php include("layouts/footer.php"); ?> 
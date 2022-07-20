<?php require_once "../includes/initialize.php"; ?>
<?php $page_title = "Block user"; ?>

<?php 
if (isset($_GET['id'])) {$user_id = $_GET['id'];}

$return = return_to();

$server_url = $_SERVER['REQUEST_URI'];

//return the user's details from the database.
$user = User::find_by_id($user_id);

$msg = "";
  if(isset($_POST['submit']) && csrf_protect()){

  $result =  $user->update_block_status($user_id);
   
    if($result){
    //success


 if($user->block == 1){
      $message = "Dear {$user->username},<br>
  <p>An action you took on {$site_title} violated our terms of use and you have been blocked from accessing your account temporarily. 
  </p>
  <p>Please do not reply this email as it's not monitored. If there has been a mistake you can <a href='mailto:{$site_title}@gmail.com'>contact us</a>.</p>";

  //send email notif.
  send_email($user->email,"Account suspension",$message);
  }
   elseif ($user->block == 0) {
          $message = "Dear {$user->username},<br>
  <p>Your account has been reactivated after being blocked. We hope that you do comply with our terms of service to prevent suspension again. 
  </p>
  <p>Please do not reply this email as it's not monitored. Do visit us again :).</p>";

  //send email notif.
  send_email($user->email,"Account activated",$message);
   }

  //subtract points
  $user->subtract_points($user_id,10);

    $session->message("You have succesfully changed user's status. Mail notice has been sent to the user. <a href={$return}>Go back</a>");

      redirect_to($server_url);
    }
   else {
     $msg = "<br><center><span class='error-feedback-messages'>Something went wrong or you haven't changed anything.</span></center>";
    }

   }

 
$status = $user->block_status($user->block);
?>

<?php include("../layouts/admin_header.php"); ?>

<?php if($page_title == "Block user"){ $u_bold = "font-weight:bold;background-color:#555;"; }; ?>

<?php include("../layouts/admin_navigation.php"); ?>

  <div id="admin-right">

<?php echo $nav; ?>

     <h1>Block user</h1>

     <?php echo display_message($msg); ?>

<b style="font-size: 1.062rem;">Change user's block status :</b>

<p>Username: <b><?php echo htmlentities($user->username); ?></b></p>

<p>User is currently: <b><?php echo $status; ?></b></p>

    <form action="<?php echo htmlentities($server_url); ?>" method="post">
    <?php echo csrf_token(); ?>

<input id="active" type="radio" name="status" value="0" <?php if($user->block == 0){echo "checked";} ?>> <label for="active">Active</label> &nbsp; 
<input id="block" type="radio" name="status" value="1" <?php if($user->block == 1){echo "checked";} ?>> <label for="block">Block</label>
    <br>
      <br>
<select name="duration" <?php if($user->block == 0){echo "required";} ?>>
  <option value="">Block lifespan</option>
   <option value="1week">1 week</option> 
  <option value="1month">1 month</option>
   <option value="Indefinite">Indefinite</option>
</select>
     <br>
      <br> 
    <input type="submit" name="submit" value="Save changes" onclick="return confirm('Confirm change')">&nbsp;&nbsp; <a href="javascript:void(0)" onclick="goBack('admindash')">Cancel</a>

    </form>

  </div>

<?php include("../layouts/admin_footer.php"); ?>
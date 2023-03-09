<?php require_once "../includes/initialize.php"; ?>
<?php $page_title = "User admin status"; ?>

<?php 
if (isset($_GET['id'])) {
  $user_id = htmlentities($_GET['id']);
}

$server_url = $_SERVER['REQUEST_URI'];

$return = return_to();

$msg = "";
if(isset($_POST['submit']) && csrf_protect()){

  $user_class = new User(); 
  
  $result = $user_class->update_admin_status($user_id);

   if($result){
    //success
      $session->message("You have succesfully changed user's admin status. <a href='{$return}'>Go back to manage users</a>");

      redirect_to($server_url);
    }
   else {
    //failure
     $msg = "<br><center><span class='error-feedback-messages'>Something went wrong or you haven't changed anything.</span></center>";
    }
}

//return the user's details from the database.
$user = User::find_by_id($user_id); 

 $admin_status = $user->admin_status($user->admin);
?>

<?php include("../layouts/admin_header.php"); ?>

<?php if($page_title == "User admin status"){ $u_bold = "font-weight:bold;background-color:#555;"; }; ?>

<?php include("../layouts/admin_navigation.php"); ?>

  <div id="admin-right">

<?php echo $nav; ?>

     <h1>User admin status</h1>

     <?php echo display_message($msg); ?>

<b style="font-size: 1.062rem;">Change user's Admin status :</b>

<p>Username: <b><?php echo htmlentities($user->username); ?></b></p>

<p>User is currently: <b><?php echo $admin_status; ?></b></p>

    <form action="<?php echo htmlentities($server_url); ?>" method="post">
    <?php echo csrf_token(); ?>

<input id="normal" type="radio" name="adminstatus" value="0" <?php if($user->admin == 0){echo "checked";} ?>> <label for="normal">Normal user</label> &nbsp; 
<input id="admin" type="radio" name="adminstatus" value="1" <?php if($user->admin == 1){echo "checked";} ?>> <label for="admin">Admin</label>
    <br>
      <br>
 <?php
 if (basename($_SERVER['HTTP_REFERER']) == 'admins') {$return_to = 'admins';} else{ $return_to = 'users';}
 ?>     
    <input type="submit" name="submit" value="Save changes" onclick="return confirm('Confirm change')">&nbsp;&nbsp; <a href="javascript:void(0)" onclick="goBack('admindash')">Cancel</a>

    </form>

  </div>

<?php include("../layouts/admin_footer.php"); ?>
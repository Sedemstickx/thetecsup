<?php require_once "includes/initialize.php"; ?>

<?php
$server_url = $_SERVER['REQUEST_URI'];

$msg = "";
  if(isset($_POST['submit']) && csrf_protect()){ 
 
   $user_class = new User(); 

   $result = $user_class->update($session->user_id);

    if($result){

    //Change the name that has been updated if in the login_auth table.
    $auth = new Auth();
    $auth->update_name($user_class->username);

       setcookie("tsp_username", stripslashes($user_class->username), time() + (172800 * 365));

      //success
      $session->message("Your profile has been succesfully updated.");

      redirect_to($server_url);
    }
   else {
    //failure
     $msg = "<br><center><span class='error-feedback-messages'>Something went wrong or you haven't 
     changed anything or you are entering a username or email that already exist.</span></center>";
    }
  }
?>

<?php $page_title = "edit profile - ".$site_title.""; ?>
<?php include("layouts/header.php"); ?>
<?php if(!$session->is_logged_in()){redirect_to($home);} ?>

  
     <div id="middle">


<?php 
//return the user's details from the database.
$user = User::find_by_id($session->user_id); 

  //get user profile pic location.
  $picture = get_pic_location($user->pic);
?>

    <h2>Edit your profile</h2>

<hr>

<!-- display the uploaded picture. -->
<div class="img_container">
 <center>
  <img id="image" src="<?php echo $picture; ?>" alt="image" class="profile-pic" data-value="<?php echo empty($user->pic) ? "" : htmlentities($user->pic); ?>">
   <div class="img_preview"><b>Preview</b></div>
</center>
 </div>
<br>
<a href="editpassword">Click here to change your Password.</a>
 <br>

    <form action="<?php echo htmlentities($server_url); ?>" enctype="multipart/form-data" method="post">
    <?php echo csrf_token(); ?>
    
    <p><label for="file_upload">Upload profile picture. (Max 2MB) <span id="img_req" style="color: red;font-size: 1.3rem;display:none;">*</span></label> 
      <br> 
   <?php list($attach_image,$max_size) = max_upload_file_size(2); ?>
   <?php echo $max_size; ?> 
   <input id="file_upload" onchange="preview_image()" type="file" name="image_upload" accept="image/*">
       </p>
    <p><label for="name">Your Name</label>
      <br>
       <input id="name" type="text" name="username" placeholder="Display name" value="<?php echo htmlentities($user->username); ?>" maxlength="15" required>
       </p>
    <p><label for="email">Your Email</label> 
      <br>
       <input id="email" type="email" placeholder="me@example.com" value="<?php echo htmlentities($user->email); ?>" readonly disabled>
       </p>  
    <p><label for="bio">Bio</label> 
      <br>
    <textarea id="bio" name="bio" placeholder="Your bio" title="Not more than 255 characters" maxlength="255"><?php echo htmlentities($user->bio); ?></textarea>
       </p>   
      <p><label for="location">Location <span id="loc_req" style="color: red;font-size: 1.3rem;display:none;">*</span></label>
      <br>
       <input id="location" type="text" name="location" placeholder="City, Country" value="<?php echo htmlentities($user->location); ?>" maxlength="200" <?php echo $user->freelancer == 1 ? "required" : ""; ?>>
       </p>
       <br>

  <h2>Earn money</h2>

    <p>Would you wish to opt in for our freelancer service and earn some money?
       </p>       
    <p>
  <input id="freelance" name="freelance" type="checkbox" <?php echo $user->freelancer == 1 ? "value=1" : ""; ?> <?php echo $user->freelancer == 1 ? "checked" : ""; ?>> Yes I will and I agree to the <a href="terms" target="_blank">terms</a> behind it.
     </p>
     <div id="freelance_form" style="<?php echo $user->freelancer == 0 ? 'display:none;' : '';?>"> 
     <p><label for="phone">Phone number <span class="required">*</span></label>
      <br>
       <input id="phone" type="tel" name="phone" placeholder="Enter phone number" value="<?php echo htmlentities($user->m_number); ?>" maxlength="10">
       </p>
         <p><label for="specialties">Specialties <span class="required">*</span></label>
      <br>
       <input id="specialties" type="text" name="specialties" placeholder="hardware, software, printers etc..." value="<?php echo htmlentities($user->specialties); ?>" maxlength="80">
       </p>
       </div>    
      <br>       
    <input type="submit" name="submit" value="Save changes">&nbsp;&nbsp; <a href="javascript:void(0)" onclick="goBack()">Cancel</a>
    <br>
    </form>
<br>
    <hr>
 <br>
   <a href="logout" style="color:red;" onclick="return confirm('Confirm logout')">Logout</a> &nbsp; <a href="delete_profile" style="color:red;">Delete Account</a>
 <br>
  <br>
   <br>
     </div>


<?php include("layouts/footer.php"); ?>
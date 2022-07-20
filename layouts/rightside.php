<div id="right">
	

<!--Right side block-->
<?php
$file_name = basename($_SERVER['PHP_SELF']);

//refactor
if (isset($_SERVER['HTTPS'])) {
	$server_https = $_SERVER['HTTPS'];
}

$server_http_host = $_SERVER['HTTP_HOST'];
$server_request_url = $_SERVER['REQUEST_URI'];

//if user is on the profile page make a share link option for the profile.
if($file_name == "profile.php"){

  //Conditional profile text.
  if ($user->id == $session->user_id) {
    $profile_link = "My profile on {$site_title} ";
  }
   else{
    $profile_link = "Profile on {$site_title} for {$user->username} ";
   }

//refactored share modal.
 share_link();  
?>
<div><button id="share_button" class="share-right-link" data-text="<?php echo $profile_link; ?>" onclick="copyLink(this)" value=" <?php echo (isset($server_https) ? "https" : "http") ."://". $server_http_host.urlencode($server_request_url); ?>">Share my profile</button></div>
 <?php
}
//if user is on other pages without a share page link show sahre link option for the site.
 else {

 share_link();  
?>
<div><button id="share_button" class="share-right-link" data-text="Hi, I have found an easy-to-use tech support platform. Visit " onclick="copyLink(this)" value="<?php echo (isset($server_https) ? "https" : "http") ."://". $server_http_host; ?>">Invite people to <?php echo $site_title; ?></button></div>
<?php
 }


 if ($file_name == "freelancers.php" || $file_name == "profile.php" && $user->freelancer == 1) {

 echo safety_tips(); 

  if(isset($user->freelancer) && $user->freelancer == 1 && $session->is_logged_in() && $user->id != $session->user_id){
?>
  <div class="profile-contact-div">  
 <b onclick="show_contact()" class="profile-contact"><i class="fa-solid fa-square-phone" style="color:MediumSeaGreen;font-size:1.2rem;"></i> Click to contact freelancer</b><br>
  <br>
  <div id="contact_details" class="profile-details" style="display:none;"> 
  <span id="desktop_mno" class="contact">Please wait...</span><br><br>
  </div>
  </div>
<?php
  }

}
 else{
?>
<div class="Discussions"> <span>Other posts</span>
	<hr>
<?php 
$post_discussions = Post::find_discussions(); 

if ($post_discussions->num_rows >= 1) {

  while ($post = $post_discussions->fetch_object()){

  $dots = "";

  if (strlen($post->title)>80) { $dots = "..."; } ;

  echo '<p><a class="discussions" href="' .Post::forum_link($post->id,$post->title). '" target="_blank">' . htmlentities(substr($post->title,0,80)) . $dots .'</a>
  </p>';
  }
  }
  else{
  echo "<br><br><center style='color:silver;font-size:0.937rem;line-height: 1.4;'>No questions or tips have been posted yet.</center><br><br>";
  }

  $post_discussions->free_result();
?>
    </div>
<?php
}
?>

<?php 
if(!empty($add_new_post)){echo $add_new_post;} 
else {echo "<hr><a class='love' href='donate'>Show some love &#128151;</a>";}
?>

</div>
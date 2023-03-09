<?php require_once "includes/initialize.php"; ?>

<?php
$pagination = new Pagination($per_page=15,$total_count=User::count_freelancers());//get limit per page and number of sql query.

$user_result = User::find_all_freelancers($per_page,$pagination->offset()); 
?>

<?php $page_title = "Hire freelancers - ".$site_title.""; $active_hire = 'id="active"'; ?>
<?php include("layouts/header.php"); ?>

  <div id="left"> 

  <h1>Hire freelancers</h1> 

<span>Click <b>Look up</b> to go to a freelancer&apos;s profile to see more details and contact them. </span>
<br>
 <br>
<div class="notice">
<span ><strong>Please note:</strong> If a freelancer has to come to your place to work it&apos;s recommended that you pay for their total transport on arrival. </span>
</div> 

<p>
<form class="freelancers_form" method="get" autocomplete="on">
  <input id="fsb" class="freelancers_search_bar" type="search" name="q" placeholder="Find by location, specialty or name" required>
 <input onclick="find_freelancers(event)" class="freelancers_submit" type="submit" value="Search">
</form>  
</p>

<hr>
  <br>

<div id="freelancers_list"> 

<!--Freelancers list-->  
<?php
if($user_result->num_rows > 0){

  include 'refactor/freelancerList.php';

 //display list of questions posted.
  while ($user = $user_result->fetch_object()) {

   //get user profile pic location.
  $pic = get_pic_location($user->pic);

  $freelancer = "";
 
  if ($user->freelancer == 1) {
    $freelancer = "<a class='freelancer-small' href='".User::profile_link($user->username)."'>Look up</a>";
  }
?>

<?php 
echo freelancer_list($user->username,$pic,$freelancer,$user->specialties,$user->location,$user->points);
?>

<?php
 }
}
 else{
  echo "<br><br><center style='color:gray;'> No freelancers are available yet. </center><br><br><br>";
 }
//free results in memory after loop.
 $user_result->free_result(); 
?>

</div> 

 <br>
<div id="pagination">
 <?php
//Provide page links.
$pagination->page_links();
?>
   </div>

<p>Want to earn money as an IT freelancer? <a href='editprofile'>Update your profile</a> to become a freelancer.</p>

    </div>

<?php include("layouts/rightside.php"); ?>
<?php include("layouts/footer.php"); ?> 
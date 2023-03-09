<?php require_once "includes/initialize.php"; ?>

<?php
$pagination = new Pagination($per_page=20,$total_count=User::count_all_by_points());

$user_result = User::find_all_by_points($per_page,$pagination->offset()); 
?>

<?php $page_title = "Users - ".$site_title.""; $active_header = 'id="active"'; ?>
<?php include("layouts/header.php"); ?>

  <div id="left"> 
<!--User list-->

<h1>Users</h1>

<p style="color:gold;font-weight: bold;"><?php echo $statement = page_conditional_statement("Top contributors", ""); ?> </p>

<?php
 //display list of all users.
  while ($user = $user_result->fetch_object()) {

  //get user profile pic location.
  $pic = get_pic_location($user->pic);
?>

<div class="public-list">

<p>
<div class="left-right-items no-margin">
  <div class="flex">
<div class="profile-div"><a class="profile-link" href="<?php echo User::profile_link($user->username); ?>  "><img src="<?php echo htmlentities($pic); ?>" alt="image" class="profile-pic-small"></a></div> 
<div class="name-div"><a class="profile-link" href="<?php echo User::profile_link($user->username); ?>  "><b><?php echo htmlentities($user->username); ?></b></a></div> 
  </div>
<div style="color:gray;"> Points: <?php echo htmlentities($user->points); ?>
  </div>  
  </div>
</p>

</div>
<?php
}
//free results in memory after loop.
 $user_result->free_result(); 
?>
<br>
 <br>
<div id="pagination">
 <?php
//Provide page links.
$pagination->page_links();
?>
   </div>
 <br>
  <br>
    </div>

<?php include("layouts/rightside.php"); ?>
<?php include("layouts/footer.php"); ?> 
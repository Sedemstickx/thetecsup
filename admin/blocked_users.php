<?php require_once "../includes/initialize.php"; ?>
<?php $page_title = "Blocked users"; ?>

<?php
$pagination = new Pagination($per_page=15,$total_count=User::count_all_blocked_users());

$user_result = User::find_blocked($per_page,$pagination->offset()); 

$server_url = $_SERVER['REQUEST_URI'];
?>

<?php include("../layouts/admin_header.php"); ?>

<?php if($page_title == "Blocked users"){ $u_bold = "font-weight:bold;background-color:#555;"; }; ?>

<?php include("../layouts/admin_navigation.php"); ?>

  <div id="admin-right">

<?php echo $nav; ?>

     <h1>Blocked users (<?php echo number_format(User::count_all_blocked_users()); ?>)</h1>

<?php echo display_message(); ?>
     
<?php
$current_date_time = date('Y-m-d H:i:s');

if($user_result->num_rows >= 1){
 //display list of questions posted.
  while ($user = $user_result->fetch_object('User')) {

  //get user profile pic location.
  $pic = get_pic_location($user->pic);

  $status = $user->block_status($user->block);
  $admin_status = $user->admin_status($user->admin);

   $Request_unblock = "";
  if ($current_date_time >= $user->block_exp && $user->block == 1) {
    $Request_unblock = "<span style='color:lightgreen'>Blocked lifespan has expired.</span>";
  }
?>

<div class="admin-list">
  
<span style="float:right;color:silver;margin-top:5px;"><a class='status' href="block?id=<?php echo urlencode($user->id);?>&<?php echo return_to_link($server_url);?>">status (<?php echo $status; ?>)</a>
</span>
  <p>
<div class="profile-div"><a class="profile-link" href="<?php echo User::profile_link_admin($user->username); ?>  "><?php echo User::profile_pic_admin($pic); ?></a></div> 
<div class="name-div"><a class="profile-link" href="<?php echo User::profile_link_admin($user->username); ?>  "><b><?php echo htmlentities($user->username); ?></b></a></div> 
<span>Email: <b><?php echo htmlentities($user->email);?></b></span>     <span class="admin-date-time"><?php echo date_converter($user->date). " at " . time_converter($user->time); ?></span>
<br>
<?php echo $Request_unblock; ?>
     </p>

</div>
<?php
 }
}
 else{
  redirect_to("users");
 }
//free results in memory after loop.
 $user_result->free_result();
?>
 <br>
<div id="pagination">
 <?php
//Provide page links.
$pagination->page_links();
list($page_number,$total_pages) = $pagination->page_number();
?>
   </div>
 <br>
<span style="color:#333;float:right;"><?php echo $page_number; ?> of  <?php echo number_format($total_pages); ?></span> <br> 
 
  </div>

<?php include("../layouts/admin_footer.php"); ?>
<?php require_once "../includes/initialize.php"; ?>
<?php $page_title = "Admin"; ?>

<?php
$admin_name = $_COOKIE['tsp_username'];

//record user admin page entry.
 if ($session->is_admin_logged_in() && isset($_GET['login']) && $_GET['login'] == 'true') {
 	
    //Record user who just entered the admin panel.
 	log_action(htmlentities($admin_name),"entered admin",$logfile = "../logs/logs.txt");
}
elseif(!$session->is_admin_logged_in()){
	redirect_to($admin_home);
}

?>

<?php include("../layouts/admin_header.php"); ?>

<?php if($page_title == 'Admin'){ $h_bold = "font-weight:bold;background-color:#555;";}; ?>

<?php include("../layouts/admin_navigation.php"); ?>

  <div id="admin-right">
     <h1>Welcome, <?php echo htmlentities($admin_name); ?></h1>

  <p><b>Online users: <?php echo number_format($online_status->count_all()); ?></b></p>

<b>Please select what you want to do :</b>

<div class="admin_grids">

<div class="admin_grid" style="padding-left: 0 !important;">
   <div class="admin_grid_list" style="background-color:#a2d200;"><a class="admin_grid_link" href="posts"> Manage posts <br> <?php echo number_format(Post::count_all()); ?></a></div>
 </div>

<div class="admin_grid">
   <div class="admin_grid_list" style="background-color:#22beef;"><a class="admin_grid_link" href="replies">Manage replies <br> <?php echo number_format(Reply::count_all()); ?></a></div>
</div>

<div class="admin_grid">
<div class="admin_grid_list" style="background-color:red;"><a class="admin_grid_link" href="users">Manage users <br> <?php echo number_format(User::count_all()); ?></a>
   </div>
</div>

<div class="admin_grid">
<div class="admin_grid_list" style="background-color:dimgray;"><a class="admin_grid_link" href="manage_topics">Manage topics <br> <?php echo number_format(Topic::count_all()); ?>
</a></div>
</div>	


<div class="admin_grid" style="padding-left: 0 !important;">
<div class="admin_grid_list" style="background-color:#8e44ad;"><a class="admin_grid_link" href="reports_list">Manage reports <br> <?php echo number_format(Report::count_all()); ?> <?php echo Report::count_not_viewed(); ?></a></div>
</div>	

<div class="admin_grid">
<div class="admin_grid_list" style="background-color:darkgray;"><a class="admin_grid_link" href="announcements">Announcements <br> <?php echo number_format(Announcement::count_all()); ?></a></div>
</div>

<div class="admin_grid">
<div class="admin_grid_list" style="background-color:darkseagreen;"><a class="admin_grid_link" href="admins">Manage admins <br> <?php echo number_format(User::count_all_admins()); ?></a></div>
</div>

<div class="admin_grid">
   <div class="admin_grid_list" style="background-color:#1e2933;padding: 4em 0;"><a class="admin_grid_link" href="logfile">View log <br> file</a></div>
</div>

</div>

<br>
 <br>

  </div>

<?php include("../layouts/admin_footer.php"); ?>
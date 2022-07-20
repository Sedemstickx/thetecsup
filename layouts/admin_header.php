<?php if(!$session->is_admin_logged_in()){redirect_to($admin_home);} ?>
<?php

//This variables mut be declared to avoid error logs.
$h_bold ="";
$u_bold ="";
$q_bold ="";
$r_bold ="";
$c_bold ="";
$rep_bold ="";
$l_bold ="";
$announce_bold ="";
$admins_bold ="";

?>
<!DOCTYPE html>
<html lang="en">
 <head>
 <title><?php echo $page_title; ?></title>
 <link rel="stylesheet" href="../styles/style.css?v=<?php echo filemtime("../styles/style.css"); ?>" type="text/css" media="all">
 <link rel="icon" type="image/jpg" href="../images/thetecsup_logo_icon.png">
  <link rel="shortcut icon" type="image/x-icon" href="../images/thetecsup_icon.ico" sizes="32x32">
    <!--load all Font Awesome styles -->
  <link href="../fontawesome/css/all.css" rel="stylesheet">
 <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
 </head>

<body class="unified-body">

<div id="admin-container">

  <header id="admin-header">
    <a href="<?php echo $admin_home; ?>" class="admin-logout">Exit</a>
</header>
  <?php $nav = "<div class='navigation'><a href='admindash'>Dashboard</a> &gt; <span class='navigation_span'>".$page_title."</span></div><hr>"; ?>
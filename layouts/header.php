<!DOCTYPE html>
<html lang="en">
<head>
  <?php 
  $title_notifs_records = "";
  
if ($session->is_logged_in()){ 

  //get total number of unviewed notifs.
  $records_count = Notification::count_not_viewed($session->user_id);

  if($records_count > 0){
    $title_notifs_records = "(". $records_count .")";
    $view = "1";
  }
  else{
    $title_notifs_records = "";
    $view = "0";
  }
} 
?>
 <title><?php echo $title_notifs_records; ?> <?php echo $page_title; ?></title>
 <meta charset="UTF-8">
 <meta name="keywords" content="tech, support, question, answer, replies, reply, forum, discussion, hire, freelancer, tip, technology, solution, fix, repair, solve, how to, help, community">
 <meta name="description" content="<?php echo $page_title; ?>"> 
 <meta name="author" content="Sedem Pious Kwame Datsa">
 <meta property="og:image" content="https://thetecsup.com/images/thetecsup_logo_icon.png">
 <link rel="stylesheet" href="styles/style.css?v=<?php echo filemtime("styles/style.css"); ?>" type="text/css" media="all">
 <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0"> 
 <link rel="icon" type="image/png" href="images/thetecsup_logo_icon.png">
 <link rel="shortcut icon" type="image/x-icon" href="images/thetecsup_icon.ico" sizes="32x32"> 
 <!--load all Font Awesome styles -->
 <link href="fontawesome/css/all.css" rel="stylesheet">
 <noscript><center class="error-feedback-messages">Javascript is required for a better experience on <?php echo $site_title; ?></center><br><br></noscript>
 <?php echo isset($encode_shared_link) ? $encode_shared_link : "" ;//shared link hasher ?>
</head>

<body class="unified-body">

<header>

<nav>

<div class="container">

 <div class="nav-left">

  <a href="<?php echo $home; ?>" class="nohover h1-space"><h1><?php echo $site_title; ?></h1></a>

<?php 
if ($session->is_logged_in()){ 
?>
<a href="notifications?view=<?php echo urlencode($view); ?>" class="mobile-notif-link"><i class="fa-solid fa-bell notif-icon-mobile"></i><span class="notif-alert notif-alert-mobile"><?php echo $records_count; ?></span></a>
<?php
}
?>

  <div id="mobile-menu" class="menu-button" onclick="showMenu()">
    <div class="bar1"></div>
    <div class="bar2"></div>
    <div class="bar3"></div>
  </div>

 </div> 


 <div id="right-nav" class="nav-right">  

  <a <?php echo isset($active_home) ? $active_home : "";?> href="<?php echo $home; ?>">Home</a>
  <a <?php echo isset($active_ask) ? $active_ask : "";?> href="ask">Ask</a>
  <a <?php echo isset($active_hire) ? $active_hire : "";?> href="freelancers">Hire</a>

<?php
if ($session->is_logged_in()){ 
  //Return user profile picture and username.
  list($username,$header_picture) = User::find_user_profile($session->user_id);
?>   
<a <?php echo isset($active_notif) ? $active_notif : "";?> href="notifications?view=<?php echo urlencode($view); ?>" class="notif-res">Notifs <span class="notif-alert"><?php echo $records_count; ?></span></a>
<div class="profile-container">
<a href="<?php echo User::profile_link($username); ?>" class="profile" title="<?php echo htmlentities($username); ?>"><img class="profile-pic" src="<?php echo $header_picture; ?>"> <span class="profile-name"><?php echo htmlentities($username); ?></span></a>
</div>
<?php
}
 else{
?> 
  <a <?php echo isset($active_topic) ? $active_topic : "";?> href="topics">Topics</a>
  <!-- sign up /login container -->
  <div class="sign-log-container">
  <a href="signup" class="signup">Signup</a> 
  <a href="login" class="login">Login</a>
  </div>
<?php
}         
?>  

<form method="get" action="search" autocomplete="on">
  <input type="search" name="q" value="<?php if(!empty($search)){echo htmlentities($search);} ?>" placeholder="Find Posts, Topics and Users" required>
  <button type="submit" value="Search"><i class="fa-solid fa-magnifying-glass"></i></button>
</form>

 </div>

</div>

</nav>
 
</header>

<?php 

if(isset($_SESSION["message"])) {
  echo display_message();//display message if one is set. 
}
elseif(isset($msg)) {
  echo display_message($msg);//display error message if one is set.
}

?>  

<?php 
$file_name = basename($_SERVER['PHP_SELF']);
if($file_name != "index.php"){
?>
<div class="container top-container-spacing">
<?php
}
?>
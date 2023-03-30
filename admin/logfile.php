<?php require_once "../includes/initialize.php"; ?>
<?php $page_title = "Logs"; ?>

<?php 

$logfile = "../logs/logs.txt";

 if ($session->is_admin_logged_in() && isset($_GET['clear']) && $_GET['clear'] == 'true') {
 	
 	//empty log file.
 	file_put_contents($logfile, '');

    //Record user who cleared previous log info in the log file.
 	log_action(htmlentities($session->active_username),"cleared logs",$logfile);

    //Reload page to a fresh one so that the url wouldn't contain "clear=true" anymore.
 	redirect_to("logfile");
}

?>

<?php include("../layouts/admin_header.php"); ?>

<?php if($page_title == 'Logs'){ $l_bold = "font-weight:bold;background-color:#555;"; }; ?>

<?php include("../layouts/admin_navigation.php"); ?>

  <div id="admin-right">

<?php echo $nav; ?>

     <h1>View logs</h1>

<br>
<a class="admin-options-link" style="color:red;" onclick="return confirm('Confirm removal')" href="logfile?clear=true">- Clear log file</a>
<br>
 <br>

<?php 

  if($handle = fopen($logfile, 'r')){


  $content = fread($handle, 1000000);


  fclose($handle);
   }


  echo nl2br($content); 

?>


  </div>

<?php include("../layouts/admin_footer.php"); ?>
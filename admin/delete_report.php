<?php require_once "../includes/initialize.php"; ?>
<?php

if($session->is_admin_logged_in()){
 
  $report_class = new Report();

 $result = $report_class->delete();

    if($result){
    //success
    $session->message("Report successfully deleted.");

      redirect_to("reports_list");
    }
}
?>
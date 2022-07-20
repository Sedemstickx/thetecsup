<?php 
header('Content-Type: application/json');
require_once "../includes/initialize.php"; 

ini_set ( 'max_execution_time', 1200); //20mins max execution time

//return
$ok = array("success" => "ok");//return ok
$partial = array("success" => "partial");//return partial
$failure = array("error" => "yes");//return yes on error 


//check if post data is received
if(isset($_POST['title']) && isset($_POST['message']) && csrf_protect()) {
  
  $user_class = new User();
  $announce_class = new Announcement(); 

  $announce_duplicate = $announce_class->count_duplicate($_POST['title']);

  //if announcement title is a duplicate prevent same data from being inserted in the db
  // when sending batches of emails.  
  if ($announce_duplicate < 1) {
  $result = $announce_class->create();//insert data into db.
  }
  else{
  $result = true;//return true to make results work with output.
  }


//admin ticks checkbox to allow this message to be sent to user emails
if ($_POST['send_emails'] == 1) {
  

  //style message.
  $message = "
  <p>{$_POST['message']}</p>
  <p>Cheers, {$site_title} team.</p>
  <p><a href='https://www.{$site_title}.com'>Click here to visit to the site</a></p>
  <p>Copyright &copy;  ".date("Y")." {$site_title}</p>";


  //send bulk emails
  $output = $user_class->send_bulk_mails($_POST['title'], $message);

  $sent_count = User::count_sent_emails();//return number of sent emails.

  //full success
  if($result && $output == ''){
  
  $user_class->default_read_announcements_status();//set read status back default.

  $ok["count"] = $sent_count;//add count assoc array to the $ok array.

  echo json_encode($ok);//return ok on success.

    }
  //Partial failure  
  elseif ($result && $output != '') {
     
    $partial["count"] = $sent_count;//add count assoc array to the $partial array.

    echo json_encode($partial);//return partial failure on success.

    }
  else {//failure 
   echo json_encode($failure);//return error message on failure.
    } 

}
//success checking announcement db insert only.
elseif ($result) {

  $user_class->default_read_announcements_status();//set read status back default.

  echo json_encode($ok);//return ok on success.
  }
  else {//failure
  echo json_encode($failure);//return error message on failure.
  }

}
?>
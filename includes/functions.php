<?php
//display a feedback message if a form is submitted.
function display_message($msg="")
{
  if(isset($_SESSION["message"])) {

  $message = "
  <div class='feedback-messages'> 
  <span class='feedback-text'>{$_SESSION["message"]}</span> &nbsp; 
  <span id='close_button' class='close_button' title='close' onclick='this.parentElement.style.display = \"none\";'>&times;</span>
  </div>";
  

  $_SESSION["message"] = null;
  unset($_SESSION["message"]);

   return $message;
  }
  elseif(!empty($msg)){
     return $msg;
  }
  
}


//convert default date to be more meaningfull.
function date_converter($date)
{
  $date = strtotime ($date); 
  $converted_date = date('F j, Y',$date);
  
  return $converted_date;
} 


//convert default date to be more meaningfull.
function time_converter($time)
{
  $time = strtotime ($time); 
  $converted_time = date('h:i a',$time);
  
  return $converted_time;
}


//combine header and exit; functions to refactor it.
function redirect_to($location = null)
{
 if ($location != null){

 header("location: {$location}");
  exit();
 }

}


//write user logins into a file.
function log_action($Username, $log_action = "logged in",$logfile = "logs/logs.txt")
{
  if($handle = fopen($logfile, 'a')){

	$logdate = date('F j, Y');
	$logtime = date('h:i a');

	$content = "User :{$Username} {$log_action} on {$logdate} at {$logtime} from IP address: ".$_SERVER['REMOTE_ADDR'].".\r\n";

  fwrite($handle, $content);
  fclose($handle);
   }
} 


//Refactored version to move_uploaded_file to given destination.Recommended upload_max_filesize should be 4MB. 
function upload_to_destination($file_type,$file_size,$new_jpeg_image,$image_name,$temp_file,$upload_folder)
{
    //If jpeg image is larger than 1MB highly compress the image and move uploaded file to upload destination.
    if ($file_type == 'image/jpeg' && $file_size > 1000000) {
    imagejpeg($new_jpeg_image, $upload_folder . $image_name, 25); 
      }
    elseif ($file_type == 'image/jpeg') {
    //lowly compress images below 1MB and move uploaded file to upload destination.  
      imagejpeg($new_jpeg_image, $upload_folder . $image_name, 70); 
    }
    elseif ($file_type == 'image/png') {
    //Get png file.
    $new_image = imagecreatefrompng($temp_file);
    //Compress new png image with imagejpeg() func. and move uploaded file to upload destination.
      imagejpeg($new_image, $upload_folder . $image_name, 70); 
    //free up memory(i don't know, that's what the php manual said).
    imagedestroy($new_image);
    }
      else{
    //simply move other file image types to upload destination without file size compression(most are already small in file size by default).
        move_uploaded_file($temp_file, $upload_folder . $image_name);
      }

    if ($file_type == 'image/jpeg') {
    //free up memory(i don't know, that's what the php manual said).
    imagedestroy($new_jpeg_image);
    }

}


//upload image and get return image name value. $upload folder loc for admin files should use "../uploads/" rather.
function image_upload($image,$upload_folder = 'uploads/')
{
   $temp_file = $_FILES["image_upload"]["tmp_name"];
   $file_type = $_FILES['image_upload']['type'];
   $file_size = $_FILES['image_upload']['size'];


   //Ensure that uploaded files are images with the main file types.
   $allowtype = array('image/jpeg','image/png','image/gif');

   if((in_array($file_type, $allowtype))) {

   $new_jpeg_image = "";

   //Create a new image if uploaded file type is a jpeg format to ready it for file size compression. fix iphone orientation issues.
   if ($file_type == 'image/jpeg') {

   $new_jpeg_image = imagecreatefromjpeg($temp_file);
   
   // Enable interlacing
   imageinterlace($new_jpeg_image, true);

   //get image orientation.
   $orientation = orientation($temp_file);

   //correctly rotate iphone images(orientation 6) to it's correct place.
   if ($orientation == 6) {$new_jpeg_image = imagerotate($new_jpeg_image , -90, 0);}
   }

   //generate new filename using time function since it's random to prevent duplicate image naming
   $image_name = time().$image;

   //run migration
   upload_to_destination($file_type,$file_size,$new_jpeg_image,$image_name,$temp_file,$upload_folder);

   //return new name to be saved in db
   return $image_name;

   }
   else{
   	$image_name = null;
   return $image_name;//return empty name for db
   }

}


//return the image location of a uploaded image specific to profile pictures.
function get_pic_location($image="",$upload_folder = 'uploads/')
{  
   if($image == null){
    $image = "images/profile.jpg";
    }
  elseif (!file_exists(dirname($image))) {
  $image = $image;
  } 
  else {
   $image = $upload_folder . htmlentities($image);
   }

   return $image; 
}


//return the image location of an uploaded image.
function get_image_location($image="",$upload_folder = 'uploads/')
{
 if($image == null){
    $image = "";
    return $image;
    }
   else {
   $image = $upload_folder . htmlentities($image);
   return $image;
   }
}


//Get image details and return image orientation details if available. Use to fix iphone image orientations on other OS.
function orientation($image_url)
{
  if(!$image_url == null && exif_imagetype($image_url) != IMAGETYPE_PNG){

  //read image meta data. set to true if thumbnails need to be read too.
  $exif = exif_read_data($image_url, 0, true);
 
  if (!empty($exif['IFD0']['Orientation'])){

  $orientation = $exif['IFD0']['Orientation'];

   return $orientation;

   }
  }
}


//check if links where included in the text and display it as a clickable hyperlink.
function create_hyperlinks($details="")
{  
  //pattern to check
  $url_pattern = '@(https?://([-\w\.]+[-\w])+(:\d+)?(/([\w/_\.#-]*(\?\S+)?[^\.\s])?)?)@';
     
  if($url_pattern.$details){
      
  //replace matched text with hyperlink.    
  $link = '<a href="$0" target="_blank">$0</a>';
     
  //subject to find pattern and replace text with.   
  $details = preg_replace($url_pattern,$link,$details);

  return $details;
  }
}


//write proper grammar for numbers of the given data.
function num_grammar($records,$singular_string,$plural_string)
{
  if($records == 0){
  $grammer = $plural_string;
  }
  else if($records == 1){
  $grammer =  $singular_string;
   }  
   else {
   $grammer = $plural_string;
   }

   return $grammer;

}


//Send emails using php mailer. Production email address must be different from development email address.
function send_email($to,$subject,$message,$from="hello@example.com")
{
  global $site_title;

  $email_body = "
  <html>
  <head>
  <title>{$subject}</title>
  </head>
  <body>
  {$message}
  </body>
  </html>";

   //initialize php mailer and set it up
  $phpmailer = new PHPMailer();
  $phpmailer->isSMTP();
  $phpmailer->Host = 'sandbox.smtp.mailtrap.io';
  $phpmailer->SMTPAuth = true;
  $phpmailer->Port = 2525;
  $phpmailer->Username = '';
  $phpmailer->Password = '';

  //Send mail
  $phpmailer->setFrom($from,$site_title);
  $phpmailer->addAddress($to);
  $phpmailer->Subject = $subject;
  $phpmailer->MsgHTML($email_body);
  //send the message, check for errors
  if (!$phpmailer->send()) {
      echo "ERROR: " . $phpmailer->ErrorInfo;
  } else {
      echo "SUCCESS.";
  }

}


//display image and and get orientation details.
function display_image($image="",$upload_folder = 'uploads/')
{
 if($image == null){
    $image_url = "";
    }
   else {
   $image_url = $upload_folder . htmlentities($image);
   }

   return $image_url;
}


//display image and and get orientation details.
function admin_image_loc($image_url)
{
  $admin_image_url = '../'.$image_url;

   return $admin_image_url;
}


//refactor the share option div to use on other pages.
function share_link()
{
  echo '<div id="modal" class="modal">

  <div id="share_block" class="share-block">

  <div class="share-block-close"><span id="close_button" class="close_button" title="close">&times;</span></div> 
  <span id="share_link_text" style="font-size:16px;">Share this link</span>
  <p id="link" style="font-size:0.937rem;color:gray;"></p>
  <button id="copy_link" class="buttons" data-clipboard-action="copy" data-clipboard-text="">Copy link</button>
  <a id="fb" href="" target="_blank"><i class="fa-brands fa-facebook social-media-icons" style="color:#1877f2;"></i></a> &nbsp;
  <a id="twitter" href="" target="_blank"><i class="fa-brands fa-twitter social-media-icons" style="color:#2daae1;"></i></a> &nbsp;
  <a id="whatsapp" href="" target="_blank"><i class="fa-brands fa-whatsapp social-media-icons" style="color:#54cc61;"></i></a>

  <br>
  </div>

  </div>';

}


//refactor the Image enlarger div to use on other pages.
function image_enlarge_div()
{
 echo '<div id="image_modal" class="image_modal">

  <span class="close_img_enlarge" title="Close this enlargement" onclick="document.getElementById(\'image_modal\').style.display = \'none\';">&times;</span>
 <br>
 <br>

 <img id="img_content" alt="image" class="enlarged_image">
  
 </div>';

}


//refactor share button
function post_share_reply_buttons()
{
  $file_name = basename($_SERVER['PHP_SELF']);

  if (isset($_SERVER['HTTPS'])) {
  $server_https = $_SERVER['HTTPS'];
  }

  $server_http_host = $_SERVER['HTTP_HOST'];
  $server_request_url = $_SERVER['REQUEST_URI'];

 //refactored share modal.
 share_link();

 $show = '<div style="float:right;margin-top: -2px;">';

  $show.=  '<span title="Share the link of this page with others."><button id="share_button" class="share-link" onclick="copyLink(this)" 
 value="'. (isset($server_https) ? "https" : "http") ."://". $server_http_host.urlencode(strtok($server_request_url,"&")).'">Share</button></span> ';

 //if code runs in the forum.php page display the reply post button.
 if ($file_name == "forum.php") {
   $show.= ' <span title="Click here to skip all replies to quickly write a reply of your own."><a href="javascript:void(0)" 
   onclick="scrolltoeditor();" class="go-to-reply"><i class="fa-solid fa-arrow-down" style="font-size:0.75rem;"></i> Reply</a></span>';
 }

 $show.=  '</div>';

  return $show;
}


//if user is on the first page of a list of information 
//make the page description look newer or more recent or top.
function page_conditional_statement($statement1, $statement2)
{
  if (!isset($_GET['page'])) { 
  $statement = $statement1; 
  }
  else { $statement = $statement2; 
  } 

  return $statement;
}


//refactor the return url in given pages.
function return_to_link($page_link)
{
  $return_to = "return=".urlencode($page_link)."";

  return $return_to;
}


//If the $_GET['return'] super global is set assign the page link to the return_to_link function.
//This is mainly used for storing previous url link while navigating between the login and sign up pages.
function page_return()
{
  if (isset($_GET['return'])) {
  $return_to = "?".return_to_link($_GET['return'])."";
  $_SESSION['lastpage'] = $_GET['return'];//store given return url into session for redirection after fb login
  }
  else{$return_to = '';}

  return $return_to;
}


//Check if page contains the $_GET['return'] super global and 
//return the given url to a href attribute to redirect user to the previous page 
function return_to()
{
   if (isset($_GET['return'])) {
    $return_to = $_GET['return'];
  }

  urlencode($return_to);

  return $return_to;
}


function max_upload_file_size($number=4)
{
  //get the max upload filesize from php.ini that was set
  $upload_max_size = "Max {$number}MB";

  //refactor this statement.
  $attach_image = "Attach an image."; 

   if ($number == 2) {
   $max_size = '<input type="hidden" name="MAX_FILE_SIZE" value="2097152">';
   }
   elseif ($number > 2) {
   $max_size = '<input type="hidden" name="MAX_FILE_SIZE" value="4194304">';
   }

   return array($attach_image,$max_size);
}


//Refactor safety tips button.
function safety_tips(){

  $safety_tips = "
  <div class='safety_tips'>
  <ul>  
  <strong>Safety Tips</strong>
  <br>
   <br>
  <li>Don&apos;t release payment until problem has been solved.</li>
  <br>
  <li>Make sure freelancer matches the info here.</li>
  <br>
  <li>Chat with freelancer to clarify work details.</li>
  <br>
  <li>Let at least one person know your whereabouts.</li>
  <br>
  <li>Don&apos;t allow remote connections from people you don&apos;t trust.</li>
  <br>
  <a href='#'>View all tips</a>
  </ul>
  </div>
  <br>";

  return $safety_tips;
}


function csrf_token()
{
    if (!isset($_SESSION["crsf_token"])) {
      $_SESSION["crsf_token"] = bin2hex(random_bytes(12));//get random token
    }

    $csrf_token = '<input id="csrf" type="hidden" name="_token" value="'.$_SESSION["crsf_token"].'">';//put token in a hidden input

    return $csrf_token;

}


function csrf_protect()
{
  //Protect from cross site request forgery attackes.

  //Valitdate csrf token and return true to allow user to post input.
  if (isset($_POST["_token"]) && $_POST["_token"] == $_SESSION["crsf_token"]) {
     return true;
  }
  else{
    return false;
  }
  
}


?>
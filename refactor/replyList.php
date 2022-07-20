<?php
    function reply_list($reply_id,$username,$pic,$post_title,$post_id,$reply_userid,$replied_to,$reply_text,$reply_image,$reply_date,$reply_time,$like_class,$edited_reply,$b_answer,$reply_instance,$shared="")
    {
      global $session;

      if (isset($_SERVER['HTTPS'])) {
      $server_https = $_SERVER['HTTPS'];
      }

      $server_http_host = $_SERVER['HTTP_HOST'];
      $server_request_url = $_SERVER['REQUEST_URI'];

      $file_name = basename($_SERVER['PHP_SELF']);

      $reply_button = "";
      $report = ''; 
      $br = ''; 

      if ($file_name == "more_replies.php") {
      $url = strtok($_SERVER['HTTP_REFERER'],"&")."&rid=".htmlentities($reply_id)."";
      }
      elseif($shared == "yes"){
      $url = (isset($server_https) ? "https" : "http") ."://". $server_http_host.urlencode(strtok($server_request_url,"&"))."&rid=".htmlentities($reply_id)."";
      }
      else{
      $url = (isset($server_https) ? "https" : "http") ."://". $server_http_host.urlencode(strtok($server_request_url,"&"));
      }

      $share = '<span title="Share your reply with others."><button id="share_button" class="share-reply-button" onclick="copyLink(this)" 
      data-text="Please check out this reply to this question: " value="'.$url.'-'.$reply_id.'">Share</button></span>';

      //If user is logged in show these options
      if ($session->is_logged_in()) {
      $reply_button = '
      <button id="reply-to" title="Click here to select a user you want to reply whiles being automatically scrolled down to write the reply." class="reply-button" value="'.urlencode($reply_id).'" onclick="insertAtid(this);">Reply <input type="hidden" value="'.htmlentities($username).'"></button>';
      
      $report = '<button class="reply-report" value="'.$reply_report_link = Report::reply_report_link($reply_id).'" onclick="report_modal(this)">Report</button>';
      } 

      $refactor_reply = '<a class="share-view-spacing" id="'.$reply_id.'"></a>
      <div class="replies-list">
      <!--Main answer body-->
      '.$b_answer.'

      <div class="left-right-items no-margin">
      <div class="flex">
      <div class="profile-div"><a class="profile-link" href="'.User::profile_link($username).'"><img src="'.$pic.'" alt="image"  class="profile-pic-small"></a>
      </div> 
      <div class="reply-name-div"><a class="profile-link" href="'.User::profile_link($username).'"><b>'.htmlentities($username).'</b></a>
      <br>
      <span class="reply-date-time-under-name no-margin">'.date_converter($reply_date).' at '.time_converter($reply_time).'</span><br>
      '.$edited_reply.'
      </div> 
      </div>
      <div class="edit-delete-container">'.$reply_instance->auth_reply_edit($reply_id,$post_title,$post_id,$reply_userid).' '.$reply_instance->auth_reply_delete($reply_id,$reply_userid).'</div> 
      </div>
      <p>'.nl2br(create_hyperlinks($replied_to.$reply_text)).'</p>

      '.$reply_image.'

      <div>
      <!--Number of likes-->
      '.$like_class->num_of_likes($reply_id).'
      </div>

      <div class="left-right-items no-margin">
      <div>
      <!--report link-->
      '.$report.'

      <!--like/unlike button--> 
      '.$like_unlike_feild = $like_class->like_unlike_field($reply_id).'
      </div>

      <!--reply button-->
      <div>'.$share.$reply_button.'</div>  
      </div>
        </div>';

      return $refactor_reply;
    }
?>
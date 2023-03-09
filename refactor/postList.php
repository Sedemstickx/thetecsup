<?php
    function post_list($pic,$username,$id,$title,$details,$topic,$views,$date,$time,$reply_result_num,$type="")
    {

      $short_details = "";
      $tip = "";
      $dots = "";
      $tip_indicator = '';
      $ask_written = "asked by";
      $link = Post::forum_link($id,$title);
      $reply_result = ''.number_format($reply_result_num).' '.num_grammar($reply_result_num,"reply","replies").'&nbsp;';
    
      if (strlen($details)>100) { $dots = "..."; } ;
    
      if ($details != null ) {
        $short_details = "<p>" . nl2br(htmlentities(substr($details,0,130))) . $dots ."</p>" ;
      }
    
       if ($type == "tip") {
       $tip_indicator = '<span class="tech_tip_indicator">Tech tip</span>';
       $ask_written = "written by";
       $link = Post::tip_link($id,$title);
       $reply_result = "";
       }
        
        $post_list = '<div class="post-list">
    
      '.$tip_indicator.'
    
      <p><a class="posts" href="' .$link. '"><b>'.htmlentities($title).'</b></a></p>
      
       ' . $short_details . '
    
      <span class="topic-ask-list">In <a class="post-topic-list" href="'.topic::link($topic).'">'.htmlentities(strtok($topic, ",")).'</a>
      '.$ask_written.' <a href="'.User::profile_link($username).'"><img src="'.htmlentities($pic).'" alt="image"  class="profile-pic-small"></a> <a class="profile-link-list" href="'.User::profile_link($username).' "><b>'.htmlentities($username).'</b></a></span>
      
      <div class="left-right-items no-margin">
      <span class="bottom-gray-text">'.$reply_result.' Views: '.htmlentities(number_format($views)).'</span>
      <span class="bottom-gray-text">'.date_converter($date). " at " . time_converter($time).'</span>
      </div> 
       </div>';
    
      return $post_list;
    }
?>
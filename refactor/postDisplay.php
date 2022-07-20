<?php
$display_question_image = "";
$display_tip_image = "";

      if ($type == "question") {
          //process image if one exist.
      $display_question_image = $post->display_image($post->image);
      }
      elseif ($type == "tip") {
            //process image if one exist.
      $display_tip_image = $post->display_image($post->image);
      }

      //get user profile pic location.
      $post->pic_url = get_pic_location($post->pic);

      $report = '';

      if($session->is_logged_in()){$report = '<button class="report" value="'.$post_report_link = Report::post_report_link($post_id).'" onclick="report_modal(this)">Report</button>';}

      //Show if question is edited.
      $post->edited_question = "";
      if ($post->edited == 1) {$post->edited_question = "<span class='edited'>Edited</span>";}

      $details = "";

      if($post->details != null) { $details = '<p class="details">' . nl2br(create_hyperlinks($post->details)) .'</p>'; } 
?>

<div class="post-div">
      
      <?php echo topic::topics_list($post->topic); ?>
      <div class="left-right-items no-margin">
      <div class="flex">
      <div class="profile-div"><a href="<?php echo User::profile_link($post->username); ?>"><img src="<?php echo $post->pic_url; ?>" alt="image"  class="profile-pic-small"></a></div> 
      <div class="name-div"> <a class="profile-link" href="<?php echo User::profile_link($post->username); ?>"><b><?php echo htmlentities($post->username); ?></b></a></div>
      </div> 
      <div class="edit-delete-container"><?php echo $p_edit = $post->auth_post_edit($post_id,$post->userid); ?>
      <?php echo $report; ?></div> 
      </div>
      <h2><?php echo htmlentities($post->title); ?></h2> 
    
      <?php echo $display_tip_image; ?>

      <?php echo $details; ?>
      
      <?php echo $display_question_image; ?>

      <div class="flex">
      <span style="color:gray;font-size:0.812rem;"> Views: <?php echo htmlentities(number_format($post->views)); ?></span>&nbsp; <?php echo $post->edited_question; ?>
      </div>

      <div class="left-right-items no-margin">
      <span class="post-forum-date-time"><?php echo date_converter($post->date). ' at ' . time_converter($post->time); ?></span>
      <!--share block-->
      <?php echo $show = post_share_reply_buttons(); ?>
      </div>

      <?php
      //image enlarger
      echo image_enlarge_div(); 
      ?>
</div>
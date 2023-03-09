<?php 
      $draft_indicator = "";
      $topic_result = "";
      $placeholder = "";
      $required = "";

      //Show time draft was last saved,title and details that are in draft to the given user.
      if($post != null && $type != "tip" && $post->type == "question" || $post != null &&  $type != "question" && $post->type == "tip"){
      $draft_indicator = "<span id='draft_indicator' style='color:gray;'>Draft last saved on ".date_converter($post->date)." at ".time_converter($post->time)."</span>&nbsp; ";
      $topic_result = $post->topic;
      }
      elseif(!empty($get_topic)){ $topic_result = $get_topic;}//if there are topics in get request assign them to topics var

      //Context of the placeholder.required if post is a tip
      if ($type == "tip") {$placeholder = "A brief title for the tip";$required = "required";}else{$placeholder = "You can start with How/What/Why etc.";}

      list($attach_image,$max_size) = max_upload_file_size();
?>

    <?php echo $draft_indicator; ?>

      <span id="draft_indicator" style="color:gray;"></span> 


<form id="post" action="<?php echo htmlentities("post"); ?>" enctype="multipart/form-data" method="post">
        <?php echo csrf_token(); ?>

        <input id="type" type="hidden" name="type" value="<?php echo $type; ?>">

        <input id="draft" type="hidden" name ="draft" value="1">

        <label for="title">Title</label> 
          <br>
          <input id="title" type="text" name="title" minlength="5" onfocus="draft_post()" placeholder="<?php echo $placeholder; ?>" value="<?php echo isset($post->title) ? htmlentities($post->title) : ''; ?>" maxlength="100" required>
          
        <p><label for="details">Details - optional</label>
          <br>
        <textarea id="details" name="details" onfocus="draft_post()" placeholder="Enter more details for better description..." title="Not more than 2000 characters" maxlength="2000" <?php echo $required; ?>><?php echo isset($post->title) ? htmlentities($post->details) : ''; ?></textarea>
            </p> 
            <br>
        <p> 
      <span class="ask-image-upload">
      <label for="img"><?php echo $attach_image; ?></label>
      <?php echo $max_size; ?>
          <input id="img" type="file" name="image_upload" accept="image/*"></span>
        </p>
        
        <?php include 'refactor/postTopicsForm.php'; ?>

            <br>
        <div class="post_buttons"><input type="submit" name="submit" onclick="undraft()" value="Post <?php echo $type; ?>">&nbsp;&nbsp; <a href="<?php echo $home; ?>">Cancel</a></div>
        <br>

        </form>
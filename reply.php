<?php
require_once "includes/initialize.php";

  //Fetch all replies related to the given question id.
  $post_id = (int)$_POST['post_id'];

   //return selected question from database.
   $post = Post::find_by_id(htmlentities($post_id));
?>

<?php
  //include refactored reply list
  include 'refactor/replyList.php';  

 //display reply posted.
 //create likereply instance
$like_reply = new LikeReply();

//Get all results from replies with the best answer selected to the given question.
$best_answer_set = Reply::find_best_answer($post_id); 
    
  $reply = Reply::find_current_reply($post_id);

  //get user profile pic location.
  $pic = get_pic_location($reply->pic);  

    //process image if one exist.
   $reply_image = $reply->display_image($reply->image); 

   //mark reply as edited if true.
     $edited_reply = "";
  if ($reply->edited == 1) {$edited_reply = "<span style='color:gray;font-size:0.812rem;'>Edited</span><br>";} 

  //Find the userid using the reply_to property which matches a given reply id.
  $reply_to_userid = Reply::find_userid($reply->reply_to_id);
  
  //get the username from the userid that has been replied to.
  list($reply_username) = User::find_user_profile($reply_to_userid);
  
  $replied_to_username = Reply::replied_username($reply_username);//return replied to username

//Get or let the asker select his/her best answer.
$best_answer = $reply->auth_best_answer($best_answer_set,$post->userid,$reply->userid,$reply->id,$reply->b_answer,$post_id);
?>

<?php echo reply_list($reply->id,$reply->username,$pic,$post->title,$post_id,$reply->userid,$replied_to_username,$reply->text,$reply_image,$reply->date,$reply->time,$like_reply,$edited_reply,$best_answer,$reply); ?> 
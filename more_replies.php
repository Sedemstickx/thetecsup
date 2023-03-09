<?php
require_once "includes/initialize.php";

  //Fetch all replies related to the given question id.
  $post_id = "";//default data.
  if (isset($_GET['id'])) {
  $post_id = htmlentities($_GET['id']);
  }

   //return selected post details from database.
   $post = Post::find_by_post(htmlentities($post_id)); 

   $pagination = new Pagination($per_page=15,$total_count=Reply::count_by_questionid($post_id));//get limit per page and number of sql query.

   $reply_result = Reply::find_by_questionid($post_id,$per_page,$pagination->offset()); 
?>


<?php
  //include refactored reply list
  include 'refactor/replyList.php';  

 //display list of a limited number of replies posted.
 //create likereply instance
$like_reply_class = new LikeReply();

//Get all results from replies with the best answer selected to the given question.
$best_answer_set = Reply::find_best_answer($post_id); 

  while ($reply = $reply_result->fetch_object('Reply')) {

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

<?php 
 echo reply_list($reply->id,$reply->username,$pic,$post->title,$post_id,$reply->userid,$replied_to_username,$reply->text,$reply_image,$reply->date,$reply->time,$like_reply_class,$edited_reply,$best_answer,$reply);
 }
 
//free results in memory after loop.
 $reply_result->free_result(); 
?>
<div id="pagination">
<br>  
<?php
//Provide page links.
$pagination->page_load_ajax($per_page,$total_count,"more_replies");
?>
   </div>

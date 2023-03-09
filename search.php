<?php require_once "includes/initialize.php"; ?>

<?php 
//get search query and filter harmful characters.
if (isset($_GET['q'])) {
  $search = $_GET['q'];
}
?>

<?php
$user_search_result = User::find_by_search($search);  

$total_count=Post::count_by_search($search);

$post_search_result = Post::find_by_search($search); 
?>

<?php $page_title = htmlentities($search) . " - ".$site_title.""; $active_header = 'id="active"'; ?>
<?php include("layouts/header.php"); ?>

  <div id="left">
    
<!--show search query and show total count of search results.-->
<span style="color:#333;float:right;margin-top: 17px;">Total results: <?php echo number_format(User::count_by_search($search) + $total_count); ?></span>
<p>Search results for : <b><?php echo htmlentities($search); ?></b></p>
<hr>

<!--search results for users-->
<?php
if($user_search_result->num_rows >= 1){

 echo "<p><b>Users</b></p>";

 //display list of users.
  while ($user = $user_search_result->fetch_object()) {

  //get user profile pic location.
  $pic = get_pic_location($user->pic);

  $freelancer = "";
 
  if ($user->freelancer == 1) {
    $freelancer = "<a class='freelancer-small' href='".User::profile_link($user->username)."'>Look up</a>";
  }
?>

<p>
<div class="left-right-items no-margin">
  <div class="flex">
<div class="profile-div"><a class="profile-link" href="<?php echo User::profile_link($user->username); ?>  "><img src="<?php echo htmlentities($pic); ?>" alt="image" class="profile-pic-small"></a></div> 
<div class="name-div"><a class="profile-link" href="<?php echo User::profile_link($user->username); ?>  "><b><?php echo htmlentities($user->username); ?></b></a></div> 
  </div>
<div style="color:gray;"> Points: <?php echo htmlentities($user->points); ?>
  </div>  
  </div>
</p>

<?php
 }
 echo "<br>";
} 
?>


<!--search results for questions--> 
<?php
if($post_search_result->num_rows >= 1){

  include 'refactor/postList.php';

 echo "<p><b>Post</b></p>";

 //display list of questions.
  while ($post = $post_search_result->fetch_object()) {

  //get user profile pic location.
  $pic = get_pic_location($post->pic);

 //get reply results.
 $reply_result_num = Reply::count_by_questionid($post->id);
?>

<?php echo post_list($pic,$post->username,$post->id,$post->title,$post->details,$post->topic,$post->views,$post->date,$post->time,$reply_result_num,$post->type); ?>
  
<?php
 }
?>

<br>
 <br>

<?php 
}
 elseif($post_search_result->num_rows == null && $user_search_result->num_rows == null){
  echo "<br><br><br><center style='color:gray;'> No results were found. </center><br><br><br>";
 }
 //free results in memory after loop.
 $user_search_result->free_result();
//free results in memory after loop.
 $post_search_result->free_result(); 
?>
<p>Not the post you are looking for?...<a href="ask">Click here to post a question</a> or <a href="post_tip">Click here to post a tip</a>.</p> 

  <br>
    </div>

<?php include("layouts/rightside.php"); ?>
<?php include("layouts/footer.php"); ?> 
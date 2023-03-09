<?php require_once "includes/initialize.php"; ?>

<?php
//get search query and filter harmful characters.
$search = "";//default data.
if (isset($_GET['q'])) {
  $search = $_GET['q'];
}

$pagination = new Pagination($per_page=15,$total_count=User::count_searched_freelancers($search));//get limit per page and number of sql query.

$user_result = User::search_freelancers($search,$per_page,$pagination->offset()); 
?>


<!--Freelancers list-->  
<?php
if($user_result->num_rows > 0){

  include 'refactor/freelancerList.php';

 //display list of questions posted.
  while ($user = $user_result->fetch_object()) {

   //get user profile pic location.
  $pic = get_pic_location($user->pic);

  $freelancer = "";
 
  if ($user->freelancer == 1) {
    $freelancer = "<a class='freelancer-small' href='".User::profile_link($user->username)."'>Look up</a>";
  }
?>

<?php 
echo freelancer_list($user->username,$pic,$freelancer,$user->specialties,$user->location,$user->points);
?>

<?php
 }
}
 else{
  echo "<br><br><center style='color:gray;'> No freelancers with such details are available yet. </center><br><br><br>";
 }
//free results in memory after loop.
 $user_result->free_result(); 
?>
<div id="pagination">
<br>  
<?php
//Provide page links.
$pagination->page_load_ajax($per_page,$total_count,"search_f_pages");
?>
   </div>

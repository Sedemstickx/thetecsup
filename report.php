<?php require_once "includes/initialize.php"; ?>

<?php 
//get report type.
if (isset($_GET["type"])) {
  $report_type = $_GET["type"];
}

//Get id based on the type of selectec id.
if (isset($_GET["rid"])) {
   $activity_id = $_GET["rid"];
}
elseif (isset($_GET["pid"])) {
   $activity_id = $_GET["pid"];
}
elseif (isset($_GET["uid"])) {
   $activity_id = $_GET["uid"];
}  

  $msg="";
  if (isset($_POST['submit'])) {

  if(csrf_protect()){
   
   $report_class = new Report();
   
  $result = $report_class->post_report($report_type,$activity_id);//insert into db.

     if($result){

      $session->message("You have succesfully submitted your report. We will look into the issue.</a>");

      redirect_to($_SERVER['HTTP_REFERER']);
    }
   else {
     $msg = "<br><center><span class='error-feedback-messages'>Something went wrong.</span></center>";
    }
 }
 else {
    redirect_to($home);
 }
}
?>


<div id="modal_bg" class="modal_bg">

<div id="main_modal" class="main_modal">

<div class="share-block-close"><span id="close" class="close_button" title="close">&times;</span></div> 

  <h2>Report this</h2>

  <b>Select one of the available reasons below</b>

  <form id="report_form" action="" method="post">
  <?php echo csrf_token(); ?>
  

    <p>       
<select name="subject" required>
   <option value="Security issue">Security issue</option> 
    <option value="Offensive">Offensive</option> 
     <option value="Fake account">Fake account</option>
      <option value="Fraud">Fraud</option>
       <option value="Irrelevant post">Irrelevant post</option>
        <option value="Spam">Spam</option>
         <option value="Threats and harrasments">threats and harrasments</option>
          <option value="Bugs">Bugs</option>
           <option value="Sarcasm">Sarcasm</option>
            <option value="Others">Others</option>
</select>
     </p>     
    <p>
<textarea name="message" placeholder="Enter details about the issue here(Optional)..." title="Not more than 500 characters" maxlength="500"></textarea>
        </p> 

    <input type="submit" name="submit" value="Submit">&nbsp;&nbsp; <a id="cancel" href="javascript:void(0)">Cancel</a> 
    <br>

    </form> 

</div>    

    </div>

/*
thetecsup javascript codes.
@author Sedem Datsa <sedemdatsa69@gmail.com>
*/


//Register service worker for PWA
if ("serviceWorker" in navigator) {
  navigator.serviceWorker.register("sw.js").then(registration => {
  console.log("Service Worker Registered");
  }).catch(error => {

  console.log("Service worker could not register: "+error);

  });
} else {
  console.log("Service worker not supported in this browser");
}


//Toggle between showing and hiding the navigation menu link when user clicks on menu button.
function showMenu() {
  $('#right-nav').toggleClass('show-nav-right');
  $('#mobile-menu').toggleClass('menu-button-blue');
}



//auto save question draft till user submits it finally.
function save_post_draft()  
      {   
  var title = document.getElementById("title").value;
  var details = document.getElementById("details").value;
  var topic = document.getElementById("topic").value;
  var draft = document.getElementById("draft").value;
  var type = document.getElementById("type").value;
  var csrf = document.getElementById("csrf").value; 

   if (title.length > 4 && draft != '') { 
  $.ajax({
    type: 'POST',
        url: "post",
        data: {
      title: title,
      details: details,
      topic: topic,
      draft: draft,
      type: type,
      _token: csrf
        },
        success: function()
        {
      document.getElementById("draft_indicator").innerHTML = "Draft saved";
        },
        error: function(jqXHR, textStatus, errorThrown) {
        alert(textStatus + '. Reload page.');
    }                  
    });
   }            
} 

//when user clicks the title bar or textarea, run the save_post_draft function in timed intervals.     
function draft_post(){  

   var auto_draft_question;
   var title = document.getElementById("title");
   var details = document.getElementById("details");

   // when the user has stopped pressing on keys, set the timeout
   // if the user presses on keys before the timeout is reached, then this timeout should be canceled via the keypress event
   function postKeyUp() {
   window.clearTimeout(auto_draft_question); // Stop errant multiple timeouts from being run.
   auto_draft_question = window.setTimeout(save_post_draft, 1000);
   }

   // when user is pressing down on keys, clear the timeout
   // a keyup event always follows a keypress event so the timeout will be re-initiated there
   function postKeyPress() {
   window.clearTimeout(auto_draft_question);
   document.getElementById("draft_indicator").innerHTML = 'Typing...';
   }

   // detects when the user is actively typing in the title bar.
   title.onkeypress = function() {postKeyPress()};

   //Detects when the user has removed his finger from a key.
   title.onkeyup = function() {postKeyUp()}; 

      // detects when the user is actively typing in the details textarea.
   details.onkeypress = function() {postKeyPress()};

   //Detects when the user has removed his finger from a key.
   details.onkeyup = function() {postKeyUp()}; 
}



//If user clicks to submit post without using an ajax method set draft value to 0.
function undraft(){     
  document.getElementById("draft").value= 0;
}
//End of post part.



//auto save reply draft till user submits it finally.
function save_reply_draft()  
      { 
    //get the question_id value and pass it to the reply page so that the page can
  //process replies based on the given forum page id number. 
  var post_id = Number($('#post_id').html());
        
  var text = document.getElementById("text").value;
  var reply_to_id = document.getElementById("reply-to-id").value;
  var draft = document.getElementById("draft").value;
  var csrf = document.getElementById("csrf").value; 
   if (text.length > 4 && draft != '') { 
  $.ajax({
    type: 'POST',
        url: "post_reply",
        data: {
      post_id: post_id,
      text: text,
      reply_id: reply_to_id,
      draft: draft,
      _token: csrf
        },
        success: function()
        {
      document.getElementById("draft_indicator").innerHTML = "Draft saved";
        },
        error: function(jqXHR, textStatus, errorThrown) {
        alert(textStatus + '. Reload page.');
    }                  
    });
   }            
} 
//when user clicks the textarea auto run the save_reply_draft function.     
function draft_reply(){

   var auto_draft_reply;
   var text = document.getElementById("text");

   // when the user has stopped pressing on keys, set the timeout
   // if the user presses on keys before the timeout is reached, then this timeout should be canceled via the keypress event
   function replyKeyUp() {
   window.clearTimeout(auto_draft_reply); // Stop errant multiple timeouts from being run.
   auto_draft_reply = window.setTimeout(save_reply_draft, 1000);
   }

   // when user is pressing down on keys, clear the timeout
   // a keyup event always follows a keypress event so the timeout will be re-initiated there
   function replyKeyPress() {
   window.clearTimeout(auto_draft_reply);
   document.getElementById("draft_indicator").innerHTML = 'Typing...';
   }

   // detects when the user is actively typing.
   text.onkeypress = function() {replyKeyPress()};

   //Detects when the user has removed his finger from a key.
   text.onkeyup = function() {replyKeyUp()}; 
}


//Post reply with support for file_upload(in this case images).
$(document).on('submit','#uploadform', (function(){

  //set draft value to 0 before ajax request.
  document.getElementById("draft").value= 0;

  event.preventDefault();//prevent page from reloading like normal form submission does 
  $.ajax({
    type: 'POST',
        url: "post_reply",
        data: new FormData(this),//Get all form data in the given form to send trough ajax
        contentType: false,//This is needed because otherwise jQuery will set it incorrectly
        processData:false,//Prevents jQuery from automatically transforming the data into a query string
            beforeSend: function(){
       $('#loadstyle').show();
       },
      success: function()
        {
    $('#uploadform')[0].reset();//reset form set up     
    load_reply();//load the posted reply. 
    $('#loadstyle').hide(); //hide postin msg
    $('#reply-username').html('');//Empty replied to username is one was set
    $('#text').val("");//Empty textarea.
        },
        error: function(jqXHR, textStatus, errorThrown) {
      $('#loadstyle').html(textStatus + '. Reload page.');
    }     
       });                  
})); 



//updates replies from reply page and insert it into forum div to view new replies 
//without reloading page.
function load_reply(){

  //get the question_id value and pass it to the reply page so that the page can
  //process replies based on the given forum page id number. 
  var post_id = Number($('#post_id').html());
  var num_of_replies;

  $.ajax({
        url: 'reply',
        method: 'POST',
        data: {
        post_id: post_id
        },  
      success: function(response)
        {   

    $('#reply_num').html(parseInt($('#reply_num').html())+1);//update count by 1 to show when a reply is successfully submitted.

    //change the grammar based on the following conditions.
    if(parseInt($('#reply_num').html()) == 1){
     $('#reply_num_gram').html('reply');
     }  
    else {
    $('#reply_num_gram').html('replies');
    } 

    //insert given replies to current_reply innerHTML to show all current replies to the given post.       
    $('#current_reply').append(response);//add new reply to the available replies. 
    $('#draft_indicator').html('');//hide draft ind.
    $('#draft').val(1);//reset dreft val to 1
    $('#reply-to-id').val('');//empty reply-to-id if it's not empty.

    var no_reply = document.getElementById("no_reply");//get no_reply id. this holds the "no replies msg" in the page
    if (no_reply != null) {$('#no_reply').remove();}//if no_reply is available hide it.

       } 
    }) 

}


//End of reply part.



//insert reply-to-id into form for processing and reply-username into html tag to display the selected username a user wants to reply to. 
//Scroll to text editor.
function insertAtid(id){

 var reply_to_id = id.value;
 var username = id.lastChild.value;

 if (reply_to_id) {
  document.getElementById("reply-to-id").value = reply_to_id;
  document.getElementById("reply-username").innerHTML = 'Replying to: '+username;
  document.getElementById("scroll-target").scrollIntoView(false);//false to align to bottom of element so that what's above it can be seen.
  }  
} 



//scroll to texteditor when clicked.
function scrolltoeditor() {
   document.getElementById("scroll-target").scrollIntoView(false);//false to align to bottom of element so that what's above it can be seen.
}



//This part is for the jquery ajax like/button.

//.on() allows a click event on any like/unlike button (even ones that have been replaced and added again) since the event 
//is handled by the ever-present document element after it bubbles up to there.

  //ajax like button

$(document).on('click', '#like', function() {

 //get onclicked value id number and get converted records of likes from  #like_num_.

  var like_id = $(this).val();
  var num_of_likes = Number($('#like_num_'+like_id).html());

  //show #like_num_grammar_ tags even if they were hidden after the unlike button has been clicked
  //this will enable the <br> tag to position other options below the number of likes.
  $('#like_num_grammar_'+like_id).show();

  //if num_of_likes returns an empty string, assign an empty string to the variable.
  if (!Number.isInteger(num_of_likes)) {  num_of_likes = ''; }

  if (like_id) {
  $.ajax({
    type: 'POST',
        url: 'like_reply',
        data: {
      like_id: like_id
        },
    beforeSend: function(){
      //add one number to the num_of_likes variable. 
      //parseInt() ensures it returns a string as an integer number.   
      num_of_likes = parseInt(num_of_likes+1);

      //display the changed number on the selected like_num_ id html.
      $('#like_num_'+like_id).html(num_of_likes);

      //change the onclicked like button to the unlike button and give it the same value index number.    
      $("#like_buttons_"+like_id).html('&nbsp;<button id="unlike" value='+like_id+' class="like_button">Unlike</button>');
   
    //change the text based on the following conditions.
    if(num_of_likes == 1){
     $('#like_num_grammar_'+like_id).html('person liked this<br>');
     }  
    else {
    $('#like_num_grammar_'+like_id).html('people liked this<br>');
    }  
     },
        success: function()
        { 
        },
      error: function(jqXHR, textStatus, errorThrown) {
      alert(textStatus + '. Reload page.');
    }               
    });
   }
});

//ajax unlike button

$(document).on('click', '#unlike', function() {

  //get onclicked value id number and get converted records of likes from  #like_num_.

  var unlike_id = $(this).val();
  var num_of_likes = Number($('#like_num_'+unlike_id).html());

  $.ajax({
    type: 'POST',
        url: 'unlike_reply',
        data: {
      unlike_id: unlike_id
        },
    beforeSend: function(){
     //subtract one number from the num_of_likes variable.  
          num_of_likes = num_of_likes-1;

     //display nothing if number of likes is equal to 0.
      if (num_of_likes == 0 ) { num_of_likes = ''; }

      //display the changed number on the selected like_num_ id html. 
      //parseInt() ensures it returns a string value as an integer number.
      $('#like_num_'+unlike_id).html(parseInt(num_of_likes));

      //change the onclicked unlike button to the like button and give it the same value index number. 
      $("#like_buttons_"+unlike_id).html('&nbsp;<button id="like" value='+unlike_id+' class="like_button">Like</button>');
        
     //change the text based on the following conditions. 
     //hide #like_num_grammar_ if records of likes are zero. this will also hide the <br> tag and fill the empty gap it gives.  
     if(num_of_likes == 0){
     $('#like_num_grammar_'+unlike_id).hide();
     }
     else if(num_of_likes == 1){
     $('#like_num_grammar_'+unlike_id).html('person liked this<br>');
     }  
     else {
     $('#like_num_grammar_'+unlike_id).html('people liked this<br>');
     }
  },
        success: function()
        { 
        },
      error: function(jqXHR, textStatus, errorThrown) {
      alert(textStatus + '. Reload page.');
    }               
    });
}); 

//End of part for the jquery ajax like/button.
 


//Copy selected link to clipboard to share.
function copyLink(id) {

  //copy selected link to clipboard using clipboard.js api object.
  //copies data in "data-clipboard-text" attribute dataset.
  var clipboard = new ClipboardJS('#copy_link');

  var modal = document.getElementById('modal');//display black transparent background
  var share_block = document.getElementById("share_block");//Share popup-box.
  var close_button = document.getElementById("close_button");//close popup-box button.
  var copy_link_button = document.getElementById("copy_link");//copy button.
  var share_link_text = document.getElementById("share_link_text");//"share this link" text.

  modal.style.display = 'flex';
  share_block.style.display = "inline-block";//Show share popup-box.
  copy_link_button.style.display = "block";//Show copy button.code needed to return to default.
  share_link_text.style.display = "block";//Show "share this link" text.code needed to return to default.

  //If share button doesn't have data-text attributes do not add any extra paramenters to the query string.
  if (id.dataset.text != undefined) {
      document.getElementById("copy_link").dataset.clipboardText = id.dataset.text+decodeURIComponent(id.value);//Insert "this" share-link into the data-clipboard-text attribute of clipboardjs.
    var fb_quote = "&quote="+id.dataset.text;
    var twitter_text = "&text="+id.dataset.text;
    var whatsapp_text = id.dataset.text;
  }
   else{
      document.getElementById("copy_link").dataset.clipboardText = decodeURIComponent(id.value);//Insert "this" share-link into the data-clipboard-text attribute of clipboardjs.
    id.dataset.text = "";
    var fb_quote = "";
    var twitter_text = "";
    var whatsapp_text = "";
   }

  document.getElementById("link").style.fontWeight = "lighter";//make text light.code needed to return to default.
  document.getElementById("link").style.color = "gray";//make text gray.code needed to return to default.
  document.getElementById("link").innerHTML = id.dataset.text+decodeURIComponent(id.value);//Insert "this" selected values in #link inner html.

  //Social media links.
  document.getElementById("fb").href = "https://www.facebook.com/sharer/sharer.php?u="+id.value+fb_quote;
  document.getElementById("twitter").href = "https://twitter.com/share?url="+id.value+twitter_text;
  document.getElementById("whatsapp").href = "https://api.whatsapp.com/send?text="+whatsapp_text+id.value;

   //hide share popup-box.
   close_button.onclick = function() {
   share_block.style.display = "none"; 
   modal.style.display = 'none';
    } 

    //Show copied to clipboard success, hide copy button,"share this link text" and replace #link inner html.
    copy_link_button.onclick = function() {
    copy_link_button.style.display = "none";
    share_link_text.style.display = "none";
    document.getElementById("link").style.fontWeight = "bolder";//make text bold.
    document.getElementById("link").style.color = "green"; //make text green.  
    document.getElementById("link").innerHTML = "Copied link to clipboard and ready to share.";//show success message.

   //hide share popup-box after 5 seconds.
   setTimeout(function () {
   share_block.style.display = "none"; 
   modal.style.display = 'none';
   },5000);    
   }  

  // When the user clicks anywhere outside of the img_content, close it
  window.onclick = function(event) {
  if (event.target == modal) {
    modal.style.display = "none";//if user clicks on the targeted image modal hide it
   }
  }

}



//hide announcement post    
    
 function hideAnnounce(){
 document.getElementById("Announce").style.display = "none";
 }



//Enlarges an image with a black background when the image is clicked on.
function enlargeImage(id){

  //display black transparent background.
  var image_modal = document.getElementById('image_modal');

  //get the image from the onclicked image insert it inside the img tag.
  var img_content = document.getElementById('img_content');
   
  image_modal.style.display = 'block';
  img_content.src = id.src;

  // When the user clicks anywhere outside of the img_content, close it
  window.onclick = function(event) {
  if (event.target == image_modal) {
    image_modal.style.display = "none";//if user clicks on the targeted image modal hide it
   }
  }

}



//When user clicks cancel, send user to the previous page they came from.
//if current page reloads send user to the homepage or admin page.
function goBack(admin) {

  if (window.location.href != document.referrer) {
    window.history.back();
  }
  else if(admin){
   window.location.href = admin;
  }
  else{
    window.location = "/";
  }
  
}



function accept_delete_terms(){

  var delete_profile = document.getElementById('delete_profile');
  var delete_terms = document.getElementById('delete_terms').checked;

  if (delete_terms == true) {
  delete_profile.disabled = false;//enable button
  delete_profile.style.opacity = "1.0";//return opacity to default 
  delete_profile.style.cursor = "pointer";//return cursor to default 
  }
   else{
  delete_profile.disabled = true;//disable button
  delete_profile.style.opacity = "0.4";//set opacity to 0.4  
  delete_profile.style.cursor = "not-allowed";//return cursor to default   
   }

}




//Tagify text inputs. this is for tagging topics
$(function() {

  //tags array out of function to prevent recreating the array each time the function is run
  var tags;
  var string = "";

  if (document.getElementById("topic") != null) {
    string = document.getElementById("topic").value;
  }


  //if value is not empty convert strings into arrays and assign them to the tags array else use an empty tags array
  if (string.length) {
    tags = string.split(",");
    document.getElementById("inputText").placeholder = "";//empty placeholder
  }
   else{
     tags = [];
   }


  $("#inputText").keydown(function(event) {

    document.getElementById("inputText").placeholder = "";//empty placeholder

    if (event.which == 13) {//if user press enter prevent default action
      event.preventDefault();
    } else if (event.which == 188) {//if user press , key append new tags and prevent any default funtions
    event.preventDefault();

    if (tags.length < 5) {
    $("#tagContainer").append('<span class="tagify">' + ($(this).val()) + ' &times;</span> ');//append text

    refreshTags();//get current tags and add new tag to the hidden input value

    $(this).val("");//empty value in the shown input tag 
    }
     else{
     $(this).val("");//empty value in the shown input tag 
      alert("Seriously...5 is the limit :)");
      }
    }
  });


  $('#tagContainer').on('click', '.tagify', function() {

      tags.splice($( this ).index(), 1);//At the given position(gotten from the clicked index number), remove 1 item

      $( this ).remove();//remove clicked tag

      document.getElementById("topic").value = tags;//get remaining tags in array
   });


  function refreshTags(){

    tags.push($("#inputText").val());//add new value to the end of the tags array

    document.getElementById("topic").value = tags.join(",");//insert tags into topic hidden value
  }

});



function report_modal(id){

  var main_modal = document.getElementById("main_modal");//Main modal.
  var modalBg = document.getElementById("modal_bg");//Modal background.
  var close = document.getElementById("close");//close button.
  var cancel = document.getElementById("cancel");//Cancel button.

  document.getElementById("report_form").action = id.value;//assign selected report link to the form action.

  //Show modals.flex helps position the modal better.
  main_modal.style.display = 'block'; 
  modalBg.style.display = 'flex';

   //hide main_modal and transparent dark background.
   close.onclick = function() {
   modalBg.style.display = 'none';
    } 

  //hide main_modal and transparent dark background.
   cancel.onclick = function() {
   modalBg.style.display = 'none';
    } 

  // When the user clicks anywhere outside of the modal, hide everything linked to the modal.
  window.onclick = function(event) {
  if (event.target == modalBg) {
    modalBg.style.display = "none";
   }
  }   

}  



$("#freelance").change(function(){

  var freelance = $(this).is(":checked");//return t/f if button is checked
  var image = $("#image").data("value");//return data in data-value

  if (freelance == true) {

  //Show user tips to help them look legit and get the jobs they need.
  alert("Few tips on becoming a legit freelancer here:\n - You must upload a profile picture showing your real face.\n "+
    "- Your real name is preferred as compared to a psuedo name.\n "+
    "- You are required to fill bars that have the * above them.\n "+
    "- Adding a portfolio link to your bio will boost a help seeker's confidence in you.");

  $(this).val("1");//set checkbox value to 1
  $("#freelance_form").slideDown();//show extra inputs for freelance details
  //add require attribute to inputs 
  $("#phone").attr("required","required"); 
  $("#specialties").attr("required","required");  
  $("#location").attr("required","required");
  $("#loc_req").show();//show required indicator for location input.

  //if data-value content is empty add required value to upload input tag
  if (image == "") {$("#file_upload").attr("required","required");
  $("#img_req").show();//show required indicator for upload image input.
   }
  }
   else{
  $(this).val("0");//set checkbox value to 0  
  $("#freelance_form").slideUp();//hide extra inputs for freelance details 
  //remove required attribute from inputs
  $("#phone").removeAttr("required"); 
  $("#specialties").removeAttr("required"); 
  $("#location").removeAttr("required"); 
  $("#loc_req").hide();//hide required indicator for location input. 
  $("#file_upload").removeAttr("required");
  $("#img_req").hide();//hide required indicator for upload image input.

  //Empty values
  $("#phone").val(""); 
  $("#specialties").val(""); 
   }

});



function show_contact(){

  $.getJSON("view_notif",function(data, status){//response should be in json

  if (status == "success" && data != "") {//prevent receiving the same data on every click.
    $("#desktop_mno").html(data.number);//assign mobile data to id to show number for call.
  }
  else if(status != "success") {
      alert("Something went wrong. Please reload page.");
    }
  });

 $("#contact_details").slideDown();

}



function show_contact_mobile(){

  $.getJSON("view_notif",function(data, status){//response should be in json

  if (status == "success" && data != "") {//prevent receiving the same data on every click.
    $("#bold_mno").html(data.number);//assign mobile data to id to show number for call.
    $("#mobile_sms").attr("href","sms:"+data.number);//assign mobile data to id for sms.
    $("#mobile_tel").attr("href","tel:"+data.number);//assign mobile data to id for call.
  }
  else if(status != "success") {
      alert("Something went wrong. Please reload page.");
    }
  }); 

 //animate showing mobile contact modal.
 $("#mobile_contact_modal").show();

 $("#modal_contact_bg").show();

 //animate hiding mobile contact modal and modal background.
 $("#close_mobile").on('click', function(){
  $("#modal_contact_bg").hide();
  $("#mobile_contact_modal").fadeOut();
 });

  // When the user clicks anywhere outside of the modal, hide everything linked to the modal.
 $(window).on('click', function(e){

  if (e.target == document.getElementById("modal_contact_bg")) {
  $("#modal_contact_bg").hide();
  $("#mobile_contact_modal").fadeOut();
  }

 });

}



//ajax search freelancers
function find_freelancers(event){

  event.preventDefault();//prevent submit button default behaviour.

  var search = document.getElementById("fsb").value;//get search value.

  if (search != "") {//ensure search bar has text before running below code.

  $("#freelancers_list").html("<br><br><center style='color:gray;'> Please wait... </center><br><br><br>");//show default message.

  $.get("search_f?q="+search, function (response,status) {//return selected page content trough ajax.
  
  if (status == "success") {
  $("#freelancers_list").html(response);//load content into div. 
  }
  else{
      alert("Something went wrong. Please reload page.");//show error message.
    }
  });
  }
  else{
    alert("Please enter a text in the search box.");//show error message.
  }

}


//ajax pagination for loading more replies in forum page
function more_replies(pageNumber){

  $("#pagination").remove();//remove pagination div before new content is loaded.

  var id = $('#post_id').html();//get page id value.

  $('#shared_reply').remove();//Remove shared target reply to prevent duplication.
  $('#current_reply').html('');//Remove(hide innerhtml) current reply to prevent duplication. 
  $("#reply-list").append("<center id='wait' style='color:gray;'><br><br> loading... <br><br></center>");//show default message.

  $.get("more_replies?page="+pageNumber+"&id="+id, function (response,status) {//return selected page content trough ajax.
  
  if (status == "success") {
  $("#wait").remove();//remove please wait message before new content is loaded.  
  $("#reply-list").append(response);//append new content into div. 
  }
  else{
      alert("Something went wrong. Please reload page.");//show error message.
    }

  });

}


//ajax pagination for freelancer search
function search_f_pages(pageNumber){

  $("#pagination").remove();//remove pagination div before new content is loaded.

  var search = document.getElementById("fsb").value;//get search value.

  $("#freelancers_list").append("<center id='wait' style='color:gray;'><br><br> Please wait... <br><br><br></center>");//show default message.

  $.get("search_f?page="+pageNumber+"&q="+search, function (response,status) {//return selected page content trough ajax.
  
  if (status == "success") {
  $("#wait").remove();//remove please wait message before new content is loaded.  
  $("#freelancers_list").append(response);//append new content into div. 
  }
  else{
      alert("Something went wrong. Please reload page.");//show error message.
    }

  });

}



//show image preview if user selects an image file to upload.
function preview_image() {

 var upload_file = document.getElementById("file_upload");
 var file = upload_file.files[0];//Get the selected file.
 var image = document.getElementById("image");

 image.src = URL.createObjectURL(file);//Create reference url and assign it the image source to display image.

 image.onload = function(){//if image loads new source data run below code.
 URL.revokeObjectURL(image.src);//remove reference url to prevent memory leaks.
 }

} 


function send_announcement(event){

  event.preventDefault();//prevent submit default action

  //get title and message values
  var title = $("#announce_title").val();
  var message = $("#announce_msg").val();
  var send_emails = $("#send_emails").val();
  var csrf_token = $("#csrf").val();

  $("#announce_feedback").show();//display announcment feedback
  $("#announce_status").text("Sending...Please wait...");//show sending text when ever user clicks the submit button.
  $("#close_feedback").hide();//hide close button when ever user clicks the submit button.

  //response(post request)
  $.post("send_announcement",{title: title, message: message, _token: csrf_token, send_emails: send_emails },function(data, status){

  if (status == "success" && data.success == 'ok') {

  $("#close_feedback").show();//show close button
  $("#announce_status").text("Announcement delivery was successful.");
  $("#sent_count").html(data.count);//update number of sent emails.

  }
  else if(status == "success" && data.success == 'partial') {

  $("#close_feedback").show();//show close button
  $("#announce_status").text("Announcement submitted succesffuly but there were some errors sending the message to some emails."); 
  $("#sent_count").html(data.count);//update number of sent emails.

    }
  else if(status == "success" && data.error == 'yes') {

  $("#close_feedback").show();//show close button
  $("#announce_feedback").removeClass("feedback-messages");
  $("#announce_feedback").addClass("error-feedback-messages"); 
  $("#announce_status").text("There were some errors sending the message to some emails.");

    }  
  else if(status != "success") {
      alert("Something went wrong. Please reload page.");
    }
  });

}
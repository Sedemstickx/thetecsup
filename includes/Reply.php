<?php 

class Reply
{	   
    public $id;
    public $post_id;
    public $userid;
    public $text;
    public $image;
    public $reply_to_id;
    public $date;
    public $time;
    public $edited;
    public $b_answer; 	
    public $draft;


    public function save_draft($post_id)
    {
      global $db;
      global $session;

      $this->userid = $session->user_id;
      $this->post_id = (int)$post_id;
      $this->text = $_POST["text"];
      $this->reply_to_id = (int)$_POST["reply_id"];
      $this->date = date('Y-m-d');
      $this->time = date('H:i:s');
      $this->draft = 1;

      //remove harmful html tag inputs in text from user.  
      $this->text = strip_tags($this->text);

      if ($this->userid == $this->reply_to_id) {$this->reply_to_id = 0;}

      //insert data into database. Single quotes all around values.
      $sql = "INSERT INTO replies (userid,post_id,text,reply_to_id,date,time,draft) VALUES (?, ?, ?, ?, ?, ?, ?)";

      $stmt = $db->prepare($sql);//prepared statement
      $stmt->bind_param('iisissi',$this->userid,$this->post_id,$this->text,$this->reply_to_id,$this->date,$this->time,$this->draft);//bind params
      $result = $stmt->execute();//execute query
      $stmt->close();//close statement

      return $result;
    }

    public function update_draft($post_id)
    {
      global $db;
      global $session;

      $this->post_id = (int)$post_id;
      $this->text = $_POST["text"];
      $this->reply_to_id = (int)$_POST["reply_id"];
      $this->date = date('Y-m-d');
      $this->time = date('H:i:s');

      //remove harmful html tag inputs in text from user.  
      $this->text = strip_tags($this->text);

      if ($session->user_id == $this->reply_to_id || $this->reply_to_id == null) {$this->reply_to_id = 0;}

      //single quotes all around values. 
      $sql = "UPDATE replies SET text = ?, reply_to_id = ?, date = ?, time = ? WHERE post_id = ? AND draft = 1 AND userid = ? LIMIT 1";
      $stmt = $db->prepare($sql);//prepared statement
      $stmt->bind_param("sissii", $this->text,$this->reply_to_id,$this->date,$this->time,$this->post_id,$session->user_id);//bind params
      $result = $stmt->execute();//execute query

      return $result;
    }

    public function unsave_draft($post_id)
    {
      global $db;
      global $session;

      $this->post_id = (int)$post_id;
      $this->text = $_POST["text"];
      $this->image = basename($_FILES["image_upload"]["name"]);
      $this->reply_to_id = (int)$_POST["reply_id"];
      $this->date = date('Y-m-d');
      $this->time = date('H:i:s');

      //remove harmful html tag inputs in text from user.  
      $this->text = strip_tags($this->text);

      //upload picture and return picture name.
      $image_name = image_upload($this->image);

      if ($session->user_id == $this->reply_to_id || $this->reply_to_id == null) {$this->reply_to_id = 0;}

      //single quotes all around values. 
      $sql = "UPDATE replies SET draft = 0, text  = ?, reply_to_id = ?, image = ?, date = ?, time = ? WHERE post_id = ? AND draft = 1 AND userid = ? LIMIT 1";
      $stmt = $db->prepare($sql);//prepared statement
      $stmt->bind_param("sisssii", $this->text,$this->reply_to_id,$image_name,$this->date,$this->time,$this->post_id,$session->user_id);//bind params
      $result = $stmt->execute();//execute query

      return $result;
    }  

    public static function find_by_draft($post_id)
    {
      global $db;
      global $session;

      if($session->is_logged_in()) {
      //select a particular row based on given id.
      $sql = "SELECT * FROM replies WHERE draft = 1 AND userid = ? AND post_id = ? LIMIT 1";
      $stmt = $db->prepare($sql);//prepared statement
      $stmt->bind_param("ii", $session->user_id,$post_id);//bind params
      $stmt->execute();//execute query
      $result = $stmt->get_result();//return results

      return $result->fetch_object('Reply');
      }
    }

    public static function count_all()
    {
      global $db;

      $sql = "SELECT id FROM replies";
      $stmt = $db->prepare($sql);//prepared statement
      $stmt->execute();//execute query
      $result = $stmt->get_result();//return results
      
      return $result->num_rows;
    }

    public static function count_by_questionid($post_id=0,$post_userid=0)
    {
      global $db;

      $sql = "SELECT id FROM replies WHERE post_id = ? AND draft = 0 AND userid <> ?";
      $stmt = $db->prepare($sql);//prepared statement
      $stmt->bind_param("ii", $post_id,$post_userid);//bind params
      $stmt->execute();//execute query
      $result = $stmt->get_result();//return results
      
      return $result->num_rows;
    }

    public static function count_by_userid($userid)
    {
      global $db;

      $sql = "SELECT id FROM replies WHERE userid = ? AND draft = 0";
      $stmt = $db->prepare($sql);//prepared statement
      $stmt->bind_param("i", $userid);//bind params
      $stmt->execute();//execute query
      $result = $stmt->get_result();//return results
      
      return $result->num_rows;
    }

    public static function count_remaining_replies($post_id=0,$reply_id=0)
    {
      global $db;

      $sql = "SELECT id FROM replies WHERE post_id = ? AND draft = 0 AND id > ? ORDER BY date ASC,time ASC";
      $stmt = $db->prepare($sql);//prepared statement
      $stmt->bind_param("ii", $post_id,$reply_id);//bind params
      $stmt->execute();//execute query
      $result = $stmt->get_result();//return results
      
      return $result->num_rows;
    }

    public static function find_all($per_page,$pagination_offset)
    {
      global $db;

      //select all replies rows from database.
      $sql = "SELECT replies.id,replies.post_id,replies.userid,replies.text,replies.image,replies.b_answer,replies.draft,replies.date,replies.time,users.username,users.pic,posts.id as replied_id,posts.title FROM replies JOIN users ON replies.userid = users.id JOIN posts ON replies.post_id = posts.id ORDER BY replies.id DESC LIMIT {$per_page} OFFSET {$pagination_offset}";
      $stmt = $db->prepare($sql);//prepared statement
      $stmt->execute();//execute query
      $result = $stmt->get_result();//return results

      return $result;
    }

    public static function find_by_id($id=0)
    {
      global $db;

      //select a particular row based on given id.
      $sql = "SELECT * FROM replies WHERE id= ? AND draft = 0 LIMIT 1";
      $stmt = $db->prepare($sql);//prepared statement
      $stmt->bind_param("i", $id);//bind params
      $stmt->execute();//execute query
      $result = $stmt->get_result();//return results

      return $result->fetch_object('Reply');
    }

    public static function return_text($id=0)
    {
      global $db;

      //select a particular row based on given id.
      $sql = "SELECT text FROM replies WHERE id= ? AND draft = 0 LIMIT 1";
      $stmt = $db->prepare($sql);//prepared statement
      $stmt->bind_param("i", $id);//bind params
      $stmt->execute();//execute query
      $result = $stmt->get_result();//return results

      return $result->fetch_object();
    }

    public static function find_by_user_id($userid,$per_page,$pagination_offset)
    {
      global $db;

      //select all questions rows from database.
      $sql = "SELECT replies.id,replies.post_id,replies.userid,replies.text,replies.image,replies.reply_to_id,replies.b_answer,replies.draft,replies.date,replies.time,users.pic,posts.id as replied_id,posts.title FROM replies JOIN users ON replies.userid = users.id JOIN posts ON replies.post_id = posts.id WHERE replies.userid = ? AND replies.draft = 0 ORDER BY replies.id DESC LIMIT {$per_page} OFFSET {$pagination_offset}";
      $stmt = $db->prepare($sql);//prepared statement
      $stmt->bind_param("i", $userid);//bind params
      $stmt->execute();//execute query
      $result = $stmt->get_result();//return results

      return $result;
    }

    public static function find_userid($reply_id)
    {
      global $db;

      $sql = "SELECT userid FROM replies WHERE id = ? AND draft = 0 LIMIT 1";
      $stmt = $db->prepare($sql);//prepared statement
      $stmt->bind_param("i", $reply_id);//bind params
      $stmt->execute();//execute query
      $result = $stmt->get_result();//return results

      $record = $result->fetch_object();

      if ($record != null) {
        $reply_to_userid =  $record->userid;;
      }
      else{
        $reply_to_userid = 0;
      }

      return $reply_to_userid; 
    }

    public static function find_current_reply($post_id=0)
    {
      global $db;
      global $session;

      //select all questions rows from database.
      $sql = "SELECT replies.id,replies.post_id,replies.userid,replies.text,replies.image,replies.reply_to_id,replies.b_answer,replies.draft,replies.date,replies.time,replies.edited,users.username,users.pic FROM replies JOIN users ON replies.userid = users.id WHERE post_id= ? AND userid= ? AND draft = 0 ORDER BY date DESC,time DESC LIMIT 1";
      $stmt = $db->prepare($sql);//prepared statement
      $stmt->bind_param("ii", $post_id,$session->user_id);//bind params
      $stmt->execute();//execute query
      $result = $stmt->get_result();//return results

      return $result->fetch_object('Reply');
    }

    public static function find_reply_id($reply_id=0)
    {
      global $db;
      global $session;

      //select all questions rows from database.
      $sql = "SELECT replies.id,replies.post_id,replies.userid,replies.text,replies.image,replies.reply_to_id,replies.b_answer,replies.draft,replies.date,replies.time,replies.edited,users.username,users.pic FROM replies JOIN users ON replies.userid = users.id WHERE replies.id= ? AND draft = 0 LIMIT 1";
      $stmt = $db->prepare($sql);//prepared statement
      $stmt->bind_param("i", $reply_id);//bind params
      $stmt->execute();//execute query
      $result = $stmt->get_result();//return results

      return $result->fetch_object('Reply');
    }

    public static function find_by_questionid($post_id,$per_page,$pagination_offset)
    {
      global $db;

      //select all questions rows from database.
      $sql = "SELECT replies.id,replies.post_id,replies.userid,replies.text,replies.image,replies.reply_to_id,replies.b_answer,replies.draft,replies.date,replies.time,replies.edited,users.username,users.pic FROM replies JOIN users ON replies.userid = users.id WHERE post_id= ? AND draft = 0 ORDER BY date ASC,time ASC LIMIT {$per_page} OFFSET {$pagination_offset}";
      $stmt = $db->prepare($sql);//prepared statement
      $stmt->bind_param("i", $post_id);//bind params
      $stmt->execute();//execute query
      $result = $stmt->get_result();//return results

      return $result;
    }

    public static function search_questionId($post_id=0)
    {
      global $db;

      //select all questions rows from database.
      $sql = "SELECT replies.id,replies.post_id,replies.userid,replies.text,replies.image,replies.reply_to_id,replies.b_answer,replies.draft,replies.date,replies.time,replies.edited,users.username,users.pic FROM replies JOIN users ON replies.userid = users.id WHERE post_id= ? AND draft = 0 ORDER BY date ASC,time ASC LIMIT 1";
      $stmt = $db->prepare($sql);//prepared statement
      $stmt->bind_param("i", $post_id);//bind params
      $stmt->execute();//execute query
      $result = $stmt->get_result();//return results

      return $result;
    }

    public static function find_best_answer($question_id=0)
    {
      global $db;

      //select all questions rows from database.
      $sql = "SELECT * FROM replies WHERE post_id = ? AND b_answer = 1";
      $stmt = $db->prepare($sql);//prepared statement
      $stmt->bind_param("i", $question_id);//bind params
      $stmt->execute();//execute query
      $result = $stmt->get_result();//return results

      return $result;
    }

    public function display_image($image="")
    {
     if($image == null){
        $image_url = "";
        }
      else {
      $image_url = "uploads/" . htmlentities($image);
      }

      if(!empty($image_url)){
      
      $display_image = '<img src="'. $image_url . '" alt="image"  onclick="enlargeImage(this);" class="image reply-images">';
      return $display_image;
      }
      else{

      $display_image = "";
      return $display_image;
      }
    }

    public function display_image_admin($image="")
    {
     if($image == null){
        $image_url = "";
        }
      else {
      $image_url = "uploads/" . htmlentities($image);
      }

      if(!empty($image_url)){
      
      $display_image = '<img src="../'. $image_url . '" alt="image"  onclick="enlargeImage(this);" style="cursor:zoom-in;" class="image"><br><br>';
      return $display_image;
      }
      else{

      $display_image = "";
      return $display_image;
      }
    }

    public function update_reply($reply_id)
    {
      global $db;

      $this->text = $_POST["text"];

      //remove harmful html tag inputs in text from user.  
      $this->text = strip_tags($this->text);

      //single quotes all around values. 
      $sql = "UPDATE replies SET text = ?, edited = 1 WHERE id = ? LIMIT 1";
      $stmt = $db->prepare($sql);//prepared statement
      $stmt->bind_param("si", $this->text,$reply_id);//bind params
      $result = $stmt->execute();//execute query

      if ($db->affected_rows > 0) {
        return $result;
      }
    }

    public function delete($reply_image="",$upload_folder = 'uploads/')
    {
      global $db;

      $this->id = $_GET['id'];

      //Delete the user's picture from the uploads folder. check if file exist to prevent errors.
      if(file_exists($upload_folder . $reply_image) && !empty($reply_image)) { unlink($upload_folder . $reply_image);}

      $sql = "DELETE FROM replies WHERE id = ? LIMIT 1";
      $stmt = $db->prepare($sql);//prepared statement
      $stmt->bind_param("i", $this->id);//bind params
      $result = $stmt->execute();//execute query

      return $result;
    }

    public function admin_delete($reply_image="",$upload_folder = '../uploads/')
    {
      global $db;

      $this->id = $_GET['id'];

      //Delete the user's picture from the uploads folder. check if file exist to prevent errors.
      if(file_exists($upload_folder . $reply_image) && !empty($reply_image)) { unlink($upload_folder . $reply_image);}

      $sql = "DELETE FROM replies WHERE id = ? LIMIT 1";
      $stmt = $db->prepare($sql);//prepared statement
      $stmt->bind_param("i", $this->id);//bind params
      $result = $stmt->execute();//execute query

      return $result;
    }

    public function reset_reply_to($reply_to_id)
    {
      global $db;

      //single quotes all around values. 
      $sql = "UPDATE replies SET reply_to_id = 0 WHERE reply_to_id = ?";
      $stmt = $db->prepare($sql);//prepared statement
      $stmt->bind_param("i", $reply_to_id);//bind params
      $result = $stmt->execute();//execute query
        
      return $result;
    }

    public static function find_questionids($id=0)
    {
      global $db;

      //select a particular row based on given id. for use for getting images when there is a mass deletion of related questions.
      $sql = "SELECT * FROM replies WHERE post_id= ? AND draft = 0";
      $stmt = $db->prepare($sql);//prepared statement
      $stmt->bind_param("i", $id);//bind params
      $stmt->execute();//execute query
      $result = $stmt->get_result();//return results

      return $result;

    }

    public function delete_all_related_replies($id=0,$upload_folder = 'uploads/')
    {
      global $db;

      //find image name from database.
      $reply_result = self::find_questionids($id);

      $like_reply = new LikeReply();

      //loop tru available reply images related to a given question and remove all.
      while ($reply = $reply_result->fetch_object()){

        //create likereply instance and deleted all likes to this reply.
      $like_reply->delete_all_related_likes($reply->id);

      //if user uploads a picture delete the user's previous picture from the uploads folder. check if file exist to prevent errors.
      if(file_exists($upload_folder . $reply->image) && !empty($reply->image)) { unlink($upload_folder . $reply->image);}
      }

      //delete all replies to the given question.
      $sql = "DELETE FROM replies WHERE post_id = ?";
      $stmt = $db->prepare($sql);//prepared statement
      $stmt->bind_param("i", $id);//bind params
      $result = $stmt->execute();//execute query

      return $result;
    }

    public function auth_reply_edit($reply_id,$post,$post_id,$userid)
    {
      global $session;

      $file_name = basename($_SERVER['PHP_SELF']);

      if(isset($session->user_id) && $session->user_id == $userid) {
      
      //If edit link is available in the forum page use the forum page's link for redirects else use the referrer page for other files e.g ajax files.
      if ($file_name == "forum.php" || $file_name == "shared_reply.php") {
      $r_edit = "<a class='edit' href='editreply?id=". urlencode($reply_id) ."&qid=". urlencode($post_id) ."&question=". urlencode($post) ."&" .return_to_link($_SERVER['REQUEST_URI']) ."'>Edit</a> |";
      }
        else{
      $r_edit = "<a class='edit' href='editreply?id=". urlencode($reply_id) ."&qid=". urlencode($post_id) ."&question=". urlencode($post) ."&" .return_to_link($_SERVER['HTTP_REFERER']) ."'>Edit</a> |";
        }

      }
      else { 
        $r_edit = "";
      } 

      return $r_edit;
    }

    public function auth_reply_delete($reply_id,$userid)
    {
      global $session;

      if(isset($session->user_id) && $session->user_id == $userid) {
      
        $r_delete = "<a class='delete' onclick='return confirm(\"Confirm deletion (your points would be removed)\")' href='delete_reply?id=". urlencode($reply_id) ."'>Delete</a>";
      }
      else { 
        $r_delete = "";
      } 

      return $r_delete;
    }

    public function send_reply_email($post,$post_id,$post_userid,$reply_userid)
    {
      global $session;
      global $site_title;

      $message = "Hello user,<br>
      <p>A user has replied to the Post: <a href=".(isset($_SERVER['HTTPS']) ? "https" : "http") ."://{$_SERVER['HTTP_HOST']}/" .Post::forum_link($post_id,$post). ">".htmlentities($post)."</a>
      </p>
      <p>Please do not reply this email as it's not monitored.</p>";

      //send email notification to the person that posted the question.
      if ($post_userid != $session->user_id) {
      $user = User::find_by_id($post_userid);

      //send email notif.
      send_email($user->email,"Question reply",$message);
      }

      //if reply id is not empty and the user is not posting the reply to his/her self and 
      //the question originator id and reply to id is not the same send the email notif to the user that has been replied to. 
      if (!empty($reply_userid) && $reply_userid != $session->user_id && $post_userid != $reply_userid) {
      $user = User::find_by_id($reply_userid);

      //send email notif.
      send_email($user->email,"Reply to your reply",$message);
      }
    }

    public function send_replyEdit_email($post,$post_userid,$reply_userid)
    {
      global $session;
      global $site_title;

      $message = "Hello user,<br>
      <p>A user has edited their reply to the Post: <a href=".(isset($_SERVER['HTTPS']) ? "https" : "http") ."://{$_SERVER['HTTP_HOST']}/" .Post::forum_link($_GET['id'],$post). ">".htmlentities($post)."</a>
      </p>
      <p>Please do not reply this email as it's not monitored.</p>";

      //send email notification to the person that posted the question.
      if ($post_userid != $session->user_id) {
      $user = User::find_by_id($post_userid);

      //send email notif.
      send_email($user->email,"Question reply edit",$message);
      }

      //if reply id is not empty and the user is not posting the reply to his/her self and 
      //the question originator id and reply to id is not the same send the email notif to the user  that has been replied to. 
      if (!empty($reply_userid) && $reply_userid != $session->user_id && $post_userid != $reply_userid) {
      $user = User::find_by_id($reply_userid);

      //send email notif.
      send_email($user->email,"Question reply edit",$message);
      }
    }

    public function auth_best_answer($best_result,$post_userid,$reply_result_userids,$reply_id,$b_anwser,$question_id)
    {
      global $db;
      global $session;

      $b_answer="";
      if (isset($session->user_id) && $best_result->num_rows == 0 && $session->user_id == $post_userid && $reply_result_userids != $session->user_id) {
        $b_answer = "<a class='best-answer' onclick='return confirm(\"Confirm selection (once selected it cannot be undone)\")' href='best_answer?rid=". urlencode($reply_id) ."&id={$question_id}'>Select as best answer</a>";
      }   
      elseif ($b_anwser == 1 && $best_result->num_rows == 1) {
      $b_answer = "<span class='best-answer'>Best answer <i class='fa-solid fa-check' style='vertical-align:middle;'></i></span>";
      }

      return $b_answer;
    }

    public function update_best_answer($reply_id)
    {
      global $db;

      //single quotes all around values. 
      $sql = "UPDATE replies SET b_answer = 1 WHERE id = ? LIMIT 1";
      $stmt = $db->prepare($sql);//prepared statement
      $stmt->bind_param("i", $reply_id);//bind params
      $result = $stmt->execute();//execute query
        
      return $result;
    }

    public static function admin_del_link($reply_id,$report="")
    {
      if (!empty($report) && $report == "report") {
        $report = "&report=true";
      }

      $admin_del_link = "admin_del_reply?id=" .urlencode($reply_id).$report;

      return $admin_del_link;
    }

    public static function replied_username($reply_username)
    {
        //display replied_to_username.
        $replied_to_username = "";
      if (!empty($reply_username)) { 
      $replied_to_username = "<a href='".User::profile_link($reply_username)." '>@".htmlentities($reply_username)."</a> ";

      return $replied_to_username;
      } 

    }
}
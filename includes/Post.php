<?php

class Post{

    public $id;
    public $userid;
    public $title;
    public $details;
    public $topic;
    public $image;
    public $date;
    public $time;
    public $views; 
    public $edited;
    public $draft;
    public $type;
    public $last_edited;


    public function save_draft()
    {
      global $db;
      global $session;

      $this->userid = $session->user_id;
      $this->title = $_POST["title"];
      $this->details = $_POST["details"];
      $this->topic = $_POST["topic"];
      $this->date = date('Y-m-d');
      $this->time = date('H:i:s');
      $this->draft = 1;
      $this->type = $_POST["type"];

      //remove harmful html tag inputs in text from user.
      $this->title = strip_tags($this->title);  
      $this->details = strip_tags($this->details);
      $this->topic = strip_tags($this->topic);

      //insert data into database. Single quotes all around values.
      $sql = "INSERT INTO posts (userid,title,details,topic,date,time,draft,type) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";

      $stmt = $db->prepare($sql);//prepared statement
      $stmt->bind_param('isssssis',$this->userid,$this->title,$this->details,$this->topic,$this->date,$this->time,$this->draft,$this->type);//bind params
      $result = $stmt->execute();//execute query
      $stmt->close();//close statement

      return $result;
    }

    public function update_draft()
    {
      global $db;
      global $session;

      $this->title = $_POST["title"];
      $this->details = $_POST["details"];
      $this->topic = $_POST["topic"];
      $this->date = date('Y-m-d');
      $this->time = date('H:i:s');
      $this->type = $_POST["type"];

      //remove harmful html tag inputs in text from user.
      $this->title = strip_tags($this->title);  
      $this->details = strip_tags($this->details);
      $this->topic = strip_tags($this->topic);

      $sql = "UPDATE posts SET title = ?, details = ?, topic = ?, date = ?, time = ?, type = ? WHERE draft = 1 AND userid = ? LIMIT 1";
      $stmt = $db->prepare($sql);//prepared statement
      $stmt->bind_param("ssssssi", $this->title,$this->details,$this->topic,$this->date,$this->time,$this->type,$session->user_id);//bind params
      $result = $stmt->execute();//execute query

      return $result;
    }

    public function unsave_draft()
    {
      global $db;
      global $session;

      $this->title = $_POST["title"];
      $this->details = $_POST["details"];
      $this->topic = !empty($_POST["topic"]) ? $_POST["topic"] : "Unorganized";
      $this->image = basename($_FILES["image_upload"]["name"]);
      $this->date = date('Y-m-d');
      $this->time = date('H:i:s');

      //remove harmful html tag inputs in text from user. the important place to put it!
      $this->title = strip_tags($this->title);  
      $this->details = strip_tags($this->details);
      $this->topic = strip_tags($this->topic);

      //upload picture and return picture name.
      $image_name = image_upload($this->image);

      $sql = "UPDATE posts SET draft = 0, title = ?, details = ?, topic = ?, image = ?, date = ?, time = ? WHERE draft = 1 AND userid = ? LIMIT 1";
      $stmt = $db->prepare($sql);//prepared statement
      $stmt->bind_param("ssssssi", $this->title,$this->details,$this->topic,$image_name,$this->date,$this->time,$session->user_id);//bind params
      $result = $stmt->execute();//execute query

      return $result;
    }

    //$post_draft_result is used to check if the user in session has started a draft.
    //$message is the return message to the user if the user has successfully posted.
    //$redirect_link is used to redirect the user to the appropriate page based on the type of post.
    //$type determines the type of post. $reward sets the points based on post.
    public function create($post_draft_result)
    {
      global $session;
      global $home;

      if (isset($_POST["type"]) == "question") {
        $message = "Question successfully posted!";
        $redirect_link = $home;
        $reward = 5;
      }
      elseif (isset($_POST["type"]) == "tip") {
        $message = "Tip successfully posted!";
        $redirect_link = "tips";
        $reward = 15;
      }

      //Check if user started writing and save their post as a draft. 
      if (isset($_POST['draft']) && isset($_POST['details']) && $session->is_logged_in()){

      //refactor
      $draft = $_POST['draft'];  

      //if there is already a draft by the user update the draft if user continues writing.
      if ($draft == 1 && $post_draft_result != null) {
          $result = $this->update_draft();
      }
      //Update draft status to 0 if user has completed draft and wants to submit their post to the community.
      elseif (isset($_POST['submit']) && $draft == 0 && $post_draft_result != null) {
          
      $result = $this->unsave_draft();

      if($result){
      //success

        //create a new user class instance.
        $user_class = new User();
        
      //Update points
      $user_class->update_points($session->user_id,$reward);

      $session->message($message);

      redirect_to($redirect_link);

      }   
        } 
      //if there is no draft by user start one if user starts writing post.     
      elseif ($draft == 1 && $post_draft_result == null && csrf_protect()){
      $result = $this->save_draft();
      }

      }
      else {
        echo "There was an error.";
      }

    }  

    public static function count_all()
    {
      global $db;

      $sql = "SELECT id FROM posts";
      $stmt = $db->prepare($sql);//prepared statement
      $stmt->execute();//execute query
      $result = $stmt->get_result();//return results
      
      return $result->num_rows;
    }

    public static function count_all_posted()
    {
      global $db;

      $sql = "SELECT id FROM posts WHERE draft = 0";
      $stmt = $db->prepare($sql);//prepared statement
      $stmt->execute();//execute query
      $result = $stmt->get_result();//return results
      
      return $result->num_rows;
    }

    //count all posted tips.
    public static function count_all_posted_tips()
    {
      global $db;

      $sql = "SELECT id FROM posts WHERE draft = 0 AND type = 'tip'";
      $stmt = $db->prepare($sql);//prepared statement
      $stmt->execute();//execute query
      $result = $stmt->get_result();//return results
      
      return $result->num_rows;
    }

    public static function count_by_search($search="")
    {
      global $db;

        //sanitize strings.
      $search = "%$search%";

      $sql = "SELECT id FROM posts WHERE draft = 0 AND MATCH(topic) AGAINST (?) OR MATCH(title) AGAINST (?) OR title LIKE ? OR topic LIKE ? ORDER BY id DESC LIMIT 15";
      $stmt = $db->prepare($sql);//prepared statement
      $stmt->bind_param("ssss", $match,$match,$search,$search);//bind params
      $stmt->execute();//execute query
      $result = $stmt->get_result();//return results
      
      return $result->num_rows;
    }

    public static function count_by_userid($userid)
    {
      global $db;

      $sql = "SELECT id FROM posts WHERE userid = ? AND draft = 0";
      $stmt = $db->prepare($sql);//prepared statement
      $stmt->bind_param("i", $userid);//bind params
      $stmt->execute();//execute query
      $result = $stmt->get_result();//return results
      
      return $result->num_rows;
    }

    public static function count_by_topic($topic="")
    {
      global $db;

      $topic_like = "%$topic%";

      $sql = "SELECT id FROM posts WHERE topic = ? OR topic LIKE ? AND draft = 0";
      $stmt = $db->prepare($sql);//prepared statement
      $stmt->bind_param("ss", $topic,$topic_like);//bind params
      $stmt->execute();//execute query
      $result = $stmt->get_result();//return results
      
      return $result->num_rows;
    }

    public static function find_by_search($search)
    {
      global $db;

      $match = $search;
      $search = "%$search%";
      
      //select all posts rows from database.
      $sql = "SELECT posts.id,posts.userid,posts.title,posts.details,posts.topic,posts.views,posts.date,posts.time,posts.type,users.username,users.pic FROM posts JOIN users ON posts.userid = users.id WHERE draft = 0 AND MATCH(topic) AGAINST (?) OR MATCH(title) AGAINST (?) OR title LIKE ? OR topic LIKE ? ORDER BY id DESC LIMIT 15";
      $stmt = $db->prepare($sql);//prepared statement
      $stmt->bind_param("ssss", $match,$match,$search,$search);//bind params
      $stmt->execute();//execute query
      $result = $stmt->get_result();//return results

      return $result;
    }

    public static function find_all($per_page,$pagination_offset)
    {
      global $db;

      //select all posts rows from database.
      $sql = "SELECT posts.id,posts.userid,posts.title,posts.details,posts.topic,posts.views,posts.date,posts.time,posts.type,posts.draft,users.username,users.pic FROM posts JOIN users ON posts.userid = users.id ORDER BY id DESC LIMIT {$per_page} OFFSET {$pagination_offset}";
      $stmt = $db->prepare($sql);//prepared statement
      $stmt->execute();//execute query
      $result = $stmt->get_result();//return results

      return $result;
    }

    public static function find_all_posted($per_page,$pagination_offset)
    {
      global $db;

      //select all posts rows from database.
      $sql = "SELECT posts.id,posts.userid,posts.title,posts.details,posts.topic,posts.views,posts.date,posts.time,posts.type,users.username,users.pic FROM posts JOIN users ON posts.userid = users.id WHERE draft = 0 ORDER BY date DESC,time DESC LIMIT {$per_page} OFFSET {$pagination_offset}";
      $stmt = $db->prepare($sql);//prepared statement
      $stmt->execute();//execute query
      $result = $stmt->get_result();//return results

      return $result;
    }

    //find all posted tips.
    public static function find_all_posted_tips($per_page,$pagination_offset)
    {
      global $db;

      //select all posts rows from database.
      $sql = "SELECT posts.id,posts.userid,posts.title,posts.details,posts.topic,posts.views,posts.date,posts.time,posts.type,users.username,users.pic FROM posts JOIN users ON posts.userid = users.id WHERE draft = 0 AND type = 'tip' ORDER BY date DESC,time DESC LIMIT {$per_page} OFFSET {$pagination_offset}";
      $stmt = $db->prepare($sql);//prepared statement
      $stmt->execute();//execute query
      $result = $stmt->get_result();//return results

      return $result;
    }

    public static function find_by_topic($topic,$per_page,$pagination_offset)
    {
      global $db;

      $topic = "%$topic%";

      //select all posts rows from database.
      $sql = "SELECT posts.id,posts.userid,posts.title,posts.details,posts.topic,posts.views,posts.date,posts.time,posts.type,users.username,users.pic FROM posts JOIN users ON posts.userid = users.id WHERE topic LIKE ? AND draft = 0 ORDER BY id DESC LIMIT {$per_page} OFFSET {$pagination_offset}";
      $stmt = $db->prepare($sql);//prepared statement
      $stmt->bind_param("s", $topic);//bind params
      $stmt->execute();//execute query
      $result = $stmt->get_result();//return results

      return $result;
    }

    public static function find_by_id($id=0)
    {
      global $db;

      //select a particular row based on given id.
      $sql = "SELECT * FROM posts WHERE id= ? LIMIT 1";
      $stmt = $db->prepare($sql);//prepared statement
      $stmt->bind_param("i", $id);//bind params
      $stmt->execute();//execute query
      $result = $stmt->get_result();//return results

      return $result->fetch_object('Post');
    }

    public static function return_title($id=0)
    {
      global $db;

      //select a particular row based on given id.
      $sql = "SELECT title FROM posts WHERE id= ? AND draft = 0 LIMIT 1";
      $stmt = $db->prepare($sql);//prepared statement
      $stmt->bind_param("i", $id);//bind params
      $stmt->execute();//execute query
      $result = $stmt->get_result();//return results

      return $result->fetch_object();
    }

    public static function find_by_post($id=0)
    {
      global $db;

      //select a particular row based on given id.
      $sql = "SELECT posts.userid,posts.title,posts.details,posts.topic,posts.image,posts.views,posts.date,posts.time,posts.edited,users.username,users.pic FROM posts JOIN users ON posts.userid = users.id WHERE posts.id= ? LIMIT 1";
      $stmt = $db->prepare($sql);//prepared statement
      $stmt->bind_param("i", $id);//bind params
      $stmt->execute();//execute query
      $result = $stmt->get_result();//return results

      return $result->fetch_object('Post');
    }

    public static function find_by_user_id($userid,$per_page,$pagination_offset)
    {
      global $db;

      //select all posts rows from database.
      $sql = "SELECT id,title,topic,views,date,time,type FROM posts WHERE userid = ? AND draft = 0 ORDER BY id DESC LIMIT {$per_page} OFFSET {$pagination_offset}";
      $stmt = $db->prepare($sql);//prepared statement
      $stmt->bind_param("i", $userid);//bind params
      $stmt->execute();//execute query
      $result = $stmt->get_result();//return results

      return $result;
    }

    public static function find_by_draft()
    {
      global $db;
      global $session;

      if($session->is_logged_in()) {
      //select a particular row based on given id.
      $sql = "SELECT title,details,topic,date,time,type FROM posts WHERE draft = 1 AND userid = ? LIMIT 1";
      $stmt = $db->prepare($sql);//prepared statement
      $stmt->bind_param("i", $session->user_id);//bind params
      $stmt->execute();//execute query
      $result = $stmt->get_result();//return results

      return $result->fetch_object();
      }
    }

    public static function find_related_tips($topic,$type,$title)
    {
      global $db;

      //select all posts rows from database.
      $sql = "SELECT * FROM posts WHERE MATCH(topic) AGAINST (?) AND type = ? AND title <> ? ORDER BY id DESC LIMIT 5";
      $stmt = $db->prepare($sql);//prepared statement
      $stmt->bind_param("sss", $topic,$type,$title);//bind params
      $stmt->execute();//execute query
      $result = $stmt->get_result();//return results

      return $result;
    }

    public static function find_related_post($topic,$title)
    {
      global $db;

      //select all posts rows from database.
      $sql = "SELECT * FROM posts WHERE MATCH(topic) AGAINST (?) AND type <> 'tip' AND title <> ? ORDER BY id DESC LIMIT 5";
      $stmt = $db->prepare($sql);//prepared statement
      $stmt->bind_param("ss", $topic,$title);//bind params
      $stmt->execute();//execute query
      $result = $stmt->get_result();//return results

      return $result;
    }

    public function update_post($post_id)
    {
      global $db;
      global $session;

      if (!empty($_POST["title"]) && !empty($_POST["details"])) {
      $this->title = $_POST["title"];
      $this->details = $_POST["details"];
      }

      $this->topic = $_POST["topic"];

      //remove harmful html tag inputs in text from user.
      $this->title = strip_tags($this->title);   
      $this->details = strip_tags($this->details);
      $this->topic = strip_tags($this->topic);

      if (!empty($_POST["title"]) && !empty($_POST["details"])) {
      $sql = "UPDATE posts SET title = ?, details = ?, edited = 1, topic = ? WHERE id = ? LIMIT 1";
      $stmt = $db->prepare($sql);//prepared statement
      $stmt->bind_param("sssi", $this->title,$this->details,$this->topic,$post_id);//bind params
      $result = $stmt->execute();//execute query

      }
      else{
        //single quotes all around values.
      $sql = "UPDATE posts SET last_edited = ?, topic = ? WHERE id = ? LIMIT 1";
      $stmt = $db->prepare($sql);//prepared statement
      $stmt->bind_param("isi", $session->user_id,$this->topic,$post_id);//bind params
      $result = $stmt->execute();//execute query
      }

      if($db->affected_rows > 0){
        return $result;
      }
    }

    public function delete($post_image="",$upload_folder = 'uploads/')
    {
      global $db;

      $this->id = $_GET['id'];

      //if user uploads a picture delete the user's previous picture from the uploads folder. check if file exist to prevent errors.
      if(file_exists($upload_folder . $post_image) && !empty($post_image)) { unlink($upload_folder . $post_image);}

        $reply_class = new Reply();
        $result = $reply_class->delete_all_related_replies($this->id);

      $sql = "DELETE FROM posts WHERE id = ? LIMIT 1";
      $stmt = $db->prepare($sql);//prepared statement
      $stmt->bind_param("i", $this->id);//bind params
      $result = $stmt->execute();//execute query

      return $result;
    }

    public function admin_delete($post_image="",$upload_folder = '../uploads/')
    {
      global $db;

      $this->id = $_GET['id'];

      //if user uploads a picture delete the user's previous picture from the uploads folder. check if file exist to prevent errors.
      if(file_exists($upload_folder . $post_image) && !empty($post_image)) { unlink($upload_folder . $post_image);}

        $reply_class = new Reply();
        $result = $reply_class->delete_all_related_replies($this->id);

      $sql = "DELETE FROM posts WHERE id = ? LIMIT 1";
      $stmt = $db->prepare($sql);//prepared statement
      $stmt->bind_param("i", $this->id);//bind params
      $result = $stmt->execute();//execute query

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
      
      $display_image = '<center><img src="'. $image_url . '" alt="image"  onclick="enlargeImage(this);" style="cursor:zoom-in;" class="image"></center><br><br>';
      return $display_image;
      }
      else{

      $display_image = "";
      return $display_image;
      }
    }

    public function auth_post_edit($post_id,$userid)
    {
      global $session;

      if(isset($session->user_id) && $session->user_id == $userid) {
      
        $p_edit = "<a class='edit' href='edit_post?id=". urlencode($post_id) ."&" .return_to_link($_SERVER['REQUEST_URI']) ."'>Edit</a> |";
      }
      elseif($session->is_logged_in() && isset($session->user_id)) {
      
        $p_edit = "<a class='edit' href='edit_post?id=". urlencode($post_id) ."&" .return_to_link($_SERVER['REQUEST_URI']) ."'>Edit topics</a> |";
      }
      else { 
        $p_edit = "";
      } 

      return $p_edit;
    }

    public function auth_post_delete($post_id,$userid)
    {
      global $session;

      if(isset($session->user_id) && $session->user_id == $userid) {
      
        $q_delete = "<a class='delete' onclick='return confirm(\"Confirm deletion (your points would be removed)\")' href='delete_post?id=". urlencode($post_id) ."'>Delete</a>";
      }
      else { 
        $q_delete = "";
      } 

      return $q_delete;
    }

    public function update_views()
    {
      global $db;

      //check if page has $_GET['id'] set but the page has been freshly accessed and it's not cached and no ajax request has been called to load the page. 
      if(isset($_GET['id']) && !isset($_SERVER['HTTP_CACHE_CONTROL']) && !isset($_SERVER['HTTP_X_REQUESTED_WITH'])){

      $sql = "UPDATE posts SET views = views +1 WHERE id = ? LIMIT 1";
      $stmt = $db->prepare($sql);//prepared statement
      $stmt->bind_param("i", $_GET['id']);//bind params
      $stmt->execute();//execute query
      } 
    }

    public static function find_discussions()
    {
      global $db;

      //Display a side list of posted posts.
      $sql = "SELECT * FROM posts WHERE draft = 0 ORDER BY RAND() LIMIT 6";
      $stmt = $db->prepare($sql);//prepared statement
      $stmt->execute();//execute query
      $result = $stmt->get_result();//return results
      
      return $result;
    }

    public function post_type($type = "")
    {
      $tip = "";
      if ($type == "tip") {

        $tip = "<br><span class='tech_tip_indicator'>Tech tip</span>";
        $asked_or_written = "Written by";

      }
      else{
        $asked_or_written = "Asked by";

      }

      return array($tip,$asked_or_written);
    }

    public static function forum_link($post_id,$question)
    {
      $forum_link = "forum?id=". urlencode($post_id) ."&question=". urlencode($question) ."";

      return $forum_link;
    }

    public static function forum_link_admin($post_id,$question)
    {
      $forum_link = "../forum?id=". urlencode($post_id) ."&question=". urlencode($question) ."";

      return $forum_link;
    }

    public static function tip_link($post_id,$title)
    {
      $tip_link = "tip?id=" .urlencode($post_id). "&title=" .urlencode($title). "";

      return $tip_link;
    }

    public static function admin_del_link($post_id,$report="")
    {
      if (!empty($report) && $report == "report") {
      $report = "&report=true";
      }

      $admin_del_link = "admin_del_post?id=" .urlencode($post_id).$report;

      return $admin_del_link;
    }

    public static function add_new_post($topic_tags,$link = "ask",$echo = "Ask a new question")
    {
      $add_new_post = "<a href='".$link."?topic=".urlencode($topic_tags)."' class='add_post' target='_blank'>+ ".$echo."</a><br>";

      return $add_new_post;
    }
}
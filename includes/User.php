<?php 

class User 
{  
    public $id;
    public $username;
    public $email;
    public $password;
    public $date;
    public $time;
    public $block;
    public $admin;
    public $bio;
    public $pic;
    public $location;
    public $points;
    public $block_exp;
    public $read_announcement;
    public $fb_user_id;
    public $freelancer;
    public $m_number;
    public $specialties;
    public $update_date;
    public $is_sent;


    private function check_username()
    {
      global $db;
      global $session;

      //The id match is used to check duplicates with other user profiles if user is updating their profile.
      //Check if username macthes a user in the database but mismatches the user id with the given session id.
      $sql = "SELECT username FROM users WHERE username= ? AND id <> ? LIMIT 1";
      $stmt = $db->prepare($sql);//prepared statement
      $stmt->bind_param("si", $this->username,$session->user_id);//bind params
      $stmt->execute();//execute query
      $result = $stmt->get_result();//return results

        return $result;
    }

    private function check_email()
    {
      global $db;
      global $session;

      //Check if email macthes a user in the database but mismatches the user id with the given session id.
      $sql = "SELECT email FROM users WHERE email= ? AND id <> ? LIMIT 1";
      $stmt = $db->prepare($sql);//prepared statement
      $stmt->bind_param("si", $this->email,$session->user_id);//bind params
      $stmt->execute();//execute query
      $result = $stmt->get_result();//return results

        return $result;
    }

    public function check_fb_id_email($fb_user_id,$fb_email)
    {
      global $db;
      global $session;

      //The id match is used to check duplicates with other user profiles if user is updating their profile.
      //Check if username macthes a user in the database but mismatches the user id with the given session id.
      $sql = "SELECT fb_user_id,email,password,block,username FROM users WHERE fb_user_id= ? AND email= ? LIMIT 1";
      $stmt = $db->prepare($sql);//prepared statement
      $stmt->bind_param("ss", $fb_user_id,$fb_email);//bind params
      $stmt->execute();//execute query
      $result = $stmt->get_result();//return results

      return $result->fetch_object();
    }

    public function return_to()
    {
      global $home;
        
        if(isset($_GET['return'])) {
            redirect_to($_GET['return']);//return user to given previous page's url
        }
        elseif(isset($_SESSION['lastpage'])) {
        $lastpage = $_SESSION['lastpage'];//Assign stored previous page url to a variable
        unset($_SESSION['lastpage']);//remove stored url from given session  
            redirect_to($lastpage);//return user to session stored given previous page's url
        }
        else{redirect_to($home);}//return user to homepage.

    }

    public function sign_up($fb_first_name="",$fb_last_name="",$fb_user_id="",$fb_email="",$fb_pic="")
    {
      global $db;
      global $site_title;

      $msg = "";

      $this->username = isset($_POST["username"]) ? $_POST["username"] : $fb_first_name. " " .$fb_last_name;//signup username or fb username
      $this->password = isset($_POST["password"]) ? $_POST["password"] : '';
      $this->email = isset($_POST["email"]) ? filter_var($_POST["email"],FILTER_SANITIZE_EMAIL) : $fb_email;//signup email or fb email
      $this->date = date('Y-m-d');
      $this->time = date('H:i:s');
      $this->fb_user_id = $fb_user_id;//get fb user id

      if (isset($_POST["password"])) {$encrypted = password_hash($this->password,PASSWORD_DEFAULT);}
      else{$encrypted = "";}//if fb signup make password empty

      //Check if username or email already belongs to a user in the database.
      $username_result = $this->check_username();
      $email_result = $this->check_email();

      
      //if fb username already exist in db return error msg.
      if(!empty($fb_user_id) && $username_result->num_rows > 0){

      $msg = "<div class='error-feedback-messages'>Your facebook username already exists. Please sign up using a different username.</div>";
      return $msg;

      }
      //if fb email already exist in db return error msg.
      elseif(!empty($fb_user_id) && $email_result->num_rows > 0){

      $msg = "<div class='error-feedback-messages'>Your facebook email already exists. Please sign up using a different email.</div>";
      return $msg;

      }
      // return error if username already exist.
      elseif($username_result->num_rows > 0){

        $msg = "<div class='error-feedback-messages'>Username already exists. Please use a different username.</div>";
        return $msg;

      }
      // return error if email already exist and email is not empty like deleted accounts.
      elseif($email_result->num_rows > 0 && !empty($this->email)){

      $msg = "<div class='error-feedback-messages'>Email already exists. Please sign up using a different email.</div>";
      return $msg;

      }
      // return error if username is more than 30(max length).
      elseif(empty($this->username)){

      $msg = "<div class='error-feedback-messages'>Username cannot be empty.</div>";
      return $msg;

      }
      //return error if email doesn't contain @ keyword and email is not empty like deleted accounts.
      elseif(strpos($this->email, "@") === false && !empty($this->email)){

      $msg = "<div class='error-feedback-messages'>This is not a valid email address.</div>";
      return $msg;

      }
      elseif(!empty($this->username) && !empty($this->email)){

      //insert data into database.
      $sql = "INSERT INTO users (username,email,password,date,time,fb_user_id,pic) VALUES (?, ?, ?, ?, ?, ?, ?)";

      $stmt = $db->prepare($sql);//prepared statement
      $stmt->bind_param('sssssss',$this->username,$this->email,$encrypted,$this->date,$this->time,$this->fb_user_id,$fb_pic);//bind params
      $result = $stmt->execute();//execute query
      $stmt->close();//close statement 

      //store details in session to be used in other pages.
      $_SESSION["username"] = stripslashes($this->username);
      $_SESSION["email"] = $this->email;

      return $result;//return true.

      }
      else {

      //unexplained error. requires debugging.
      $msg = "<div class='error-feedback-messages'>Something went wrong. Please retry.</div>";
      return $msg;

      }

    }

    public function authenticate_user($fb_user_id="",$fb_email="")
    {
     global $db;

      $msg = "";

      $this->username = isset($_POST["username"]) ? $_POST["username"] : '';
      $this->password = isset($_POST["password"]) ? $_POST["password"] : '';
      $this->fb_user_id = $fb_user_id;
      $this->email = $fb_email;

      if (!empty($this->fb_user_id) && !empty($this->email)) {//if fb details are not empty match fb_id_email and return user details

      //Check if given fb user id and email macthes a user in the database.
      $user = $this->check_fb_id_email($this->fb_user_id,$this->email);
      }
      else{
      //Check if username or email macthes a user in the database.  
      $user = $this->find_by_username_email();
      }


      //if password is correct and user is not blocked or fb user id exist and user is not blocked allow user to access account.
      if($user != null && password_verify($this->password , $user->password) && $user->block === 0 || $user != null && $user->fb_user_id != null && $user->block === 0) {
      
      $_SESSION["username"] = $user->username;//use the name in the login page.

      return true;//return true.

      }
      elseif ($user == null) {
        $msg = "<div class='error-feedback-messages'>Incorrect username or password. Please try again.</div>";
        return $msg;
      }
      elseif ($user->block === 1){//failure
        $msg = "<div class='error-feedback-messages'>Your account has been temporarily blocked.</div>";
        return $msg;
      }
      elseif(!password_verify($this->password , $user->password)){//failure
        $msg = "<div class='error-feedback-messages'>Incorrect username or password. Please try again.</div>";
        return $msg;
      }
      else{//failure
        $msg = "<div class='error-feedback-messages'>There was an error. Please try again.</div>";
        return $msg;
      }

    }

    private function find_by_username_email()
    {
      global $db;

      //Check if username or email macthes a user in the database.
      //$this->username is used for both bind_params because username or email entered by user is going to be assigned to this username form name. 
      $sql = "SELECT * FROM users WHERE username= ? OR email= ? LIMIT 1";
      $stmt = $db->prepare($sql);//prepared statement
      $stmt->bind_param("ss", $this->username, $this->username);//bind params
      $stmt->execute();//execute query
      $result = $stmt->get_result();//return results

      return $result->fetch_object();
    }

    public static function count_all()
    {
      global $db;

      $sql = "SELECT id FROM users";
      $stmt = $db->prepare($sql);//prepared statement
      $stmt->execute();//execute query
      $result = $stmt->get_result();//return results

      return $result->num_rows;
    }

    public static function count_all_by_points()
    {
      global $db;

      $sql = "SELECT id FROM users WHERE block = 0";
      $stmt = $db->prepare($sql);//prepared statement
      $stmt->execute();//execute query
      $result = $stmt->get_result();//return results

      return $result->num_rows;
    }

    public static function count_all_admins()
    {
      global $db;

      $sql = "SELECT id FROM users WHERE admin = 1";
      $stmt = $db->prepare($sql);//prepared statement
      $stmt->execute();//execute query
      $result = $stmt->get_result();//return results
      
      return $result->num_rows;
    }

    public static function count_all_blocked_users()
    {
      global $db;

      $sql = "SELECT id FROM users WHERE block = 1";
      $stmt = $db->prepare($sql);//prepared statement
      $stmt->execute();//execute query
      $result = $stmt->get_result();//return results
      
      return $result->num_rows;
    }

    public static function count_by_search($search="")
    {
      global $db;

      $search = "%$search%";

      $sql = "SELECT id FROM users WHERE username LIKE ?";
      $stmt = $db->prepare($sql);//prepared statement
      $stmt->bind_param("s", $search);//bind params
      $stmt->execute();//execute query
      $result = $stmt->get_result();//return results
      
      return $result->num_rows;
    }

    public static function count_freelancers()
    {
      global $db;

      $sql = "SELECT id FROM users WHERE freelancer = 1";
      $stmt = $db->prepare($sql);//prepared statement
      $stmt->execute();//execute query
      $result = $stmt->get_result();//return results

      return $result->num_rows;
    }

    public static function count_searched_freelancers($search)
    {
      global $db;

      $search = "%$search%";

      $sql = "SELECT id FROM users WHERE location LIKE ? AND freelancer = 1 OR specialties LIKE ? AND freelancer = 1 OR username LIKE ? AND freelancer = 1";
      $stmt = $db->prepare($sql);//prepared statement
      $stmt->bind_param("sss", $search,$search,$search);//bind params
      $stmt->execute();//execute query
      $result = $stmt->get_result();//return results

      return $result->num_rows;
    }

    public static function count_all_emails($empty_emails = "")
    {
      global $db;

      //select all rows from database.
      $sql = "SELECT id FROM users WHERE email <> ? AND block = 0 ";
      $stmt = $db->prepare($sql);//prepared statement
      $stmt->bind_param("s", $empty_emails);//bind params
      $stmt->execute();//execute query
      $result = $stmt->get_result();//return results

      return $result->num_rows;
    }

    public static function count_sent_emails($empty_emails = "")
    {
      global $db;

      //select all rows from database.
      $sql = "SELECT id FROM users WHERE is_sent = 1 AND email <> ? AND block = 0 ";
      $stmt = $db->prepare($sql);//prepared statement
      $stmt->bind_param("s", $empty_emails);//bind params
      $stmt->execute();//execute query
      $result = $stmt->get_result();//return results

      return $result->num_rows;
    }

    public static function find_all($per_page,$pagination_offset)
    {
      global $db;

      //select all rows from database.
      $sql = "SELECT * FROM users ORDER BY id DESC LIMIT {$per_page} OFFSET {$pagination_offset}";
      
      $stmt = $db->prepare($sql);//prepared statement
      $stmt->execute();//execute query
      $result = $stmt->get_result();//return results

      return $result;
    }

    public static function find_by_id($id=0)
    {
      global $db;

      $sql = "SELECT * FROM users WHERE id = ? LIMIT 1";
      $stmt = $db->prepare($sql);//prepared statement
      $stmt->bind_param("i", $id);//bind params
      $stmt->execute();//execute query
      $result = $stmt->get_result();//return results

      return $result->fetch_object('User');
    }

    public static function find_by_username($username)
    {
      global $db;

      //Check if username macthes a user in the database.
      $sql = "SELECT id,username,block,admin FROM users WHERE username= ? LIMIT 1";
      $stmt = $db->prepare($sql);//prepared statement
      $stmt->bind_param("s", $username);//bind params
      $stmt->execute();//execute query
      $result = $stmt->get_result();//return results

      return $result->fetch_object();
    }

    public static function find_blocked($per_page,$pagination_offset)
    {
      global $db;

      //select all rows from database.
      $sql = "SELECT * FROM users WHERE block = 1 ORDER BY id DESC LIMIT {$per_page} OFFSET {$pagination_offset}";
      $stmt = $db->prepare($sql);//prepared statement
      $stmt->execute();//execute query
      $result = $stmt->get_result();//return results

      return $result;
    }

    public static function find_admins($per_page,$pagination_offset)
    {
      global $db;

      //select all rows from database.
      $sql = "SELECT * FROM users WHERE admin = 1 ORDER BY id DESC LIMIT {$per_page} OFFSET {$pagination_offset}";
      $stmt = $db->prepare($sql);//prepared statement
      $stmt->execute();//execute query
      $result = $stmt->get_result();//return results

      return $result;
    }

    public static function find_by_search($search)
    {
      global $db;

      $search = "%$search%";

      //select all questions rows from database.
      $sql = "SELECT username,pic,freelancer,points FROM users WHERE username LIKE ? LIMIT 15";
      $stmt = $db->prepare($sql);//prepared statement
      $stmt->bind_param("s", $search);//bind params
      $stmt->execute();//execute query
      $result = $stmt->get_result();//return results

      return $result;
    }

    public static function find_all_by_points($per_page,$pagination_offset)
    {
      global $db;

      //select all questions rows from database.
      $sql = "SELECT username,pic,points FROM users WHERE block = 0 ORDER BY points DESC LIMIT {$per_page} OFFSET {$pagination_offset}";
      $stmt = $db->prepare($sql);//prepared statement
      $stmt->execute();//execute query
      $result = $stmt->get_result();//return results

      return $result;
    }

    public static function find_all_freelancers($per_page,$pagination_offset)
    {
      global $db;

      //select all questions rows from database.
      $sql = "SELECT username,pic,freelancer,specialties,location,points FROM users WHERE freelancer = 1 ORDER BY points DESC LIMIT {$per_page} OFFSET {$pagination_offset}";
      $stmt = $db->prepare($sql);//prepared statement
      $stmt->execute();//execute query
      $result = $stmt->get_result();//return results

      return $result;
    }

    public static function search_freelancers($search,$per_page,$pagination_offset)
    {
      global $db;

      $search = "%$search%";

      //select all questions rows from database.
      $sql = "SELECT username,pic,freelancer,specialties,location,points FROM users WHERE location LIKE ? AND freelancer = 1 OR specialties LIKE ? AND freelancer = 1 OR username LIKE ? AND freelancer = 1 ORDER BY points DESC LIMIT {$per_page} OFFSET {$pagination_offset}";
      $stmt = $db->prepare($sql);//prepared statement
      $stmt->bind_param("sss", $search,$search,$search);//bind params
      $stmt->execute();//execute query
      $result = $stmt->get_result();//return results

      return $result;
    }

    public function update($userid,$upload_folder = 'uploads/')
    {
      global $db;

      $this->username = $_POST["username"];
      $this->location = $_POST["location"];
      $this->bio = $_POST["bio"];
      $this->pic = basename($_FILES["image_upload"]["name"]);
      $this->freelancer = $_POST["freelance"];
      $this->m_number = $_POST["phone"];
      $this->specialties = $_POST["specialties"];
      $this->update_date = date('Y-m-d');

      //remove harmful html tag inputs in text from user.  
      $this->bio = strip_tags($this->bio);

        //get some user details from database without slashes being included and update username session.
        $user = self::find_by_id($userid);

        //if user uploads a picture delete the user's previous picture from the uploads folder. check if file exist to prevent errors.
        if($this->pic != null && file_exists($upload_folder . $user->pic) && !empty($user->pic)) { unlink($upload_folder . $user->pic);}
        
        //upload picture and return picture name.
        $basename = image_upload($this->pic);

        //Update pic column in the database if a new picture name is available else use the current pic name.
        $pic_name = $basename != null ? $basename : $user->pic;

      //Check if username already belongs to a user in the database.
      $username_result = $this->check_username();

      // return empty string if username already exist.
      if($username_result->num_rows > 0){

      $result = '';

      }
      else{

        //single quotes all around values. mysqli syntax quotes inside the $pic_name variable.
      $sql = "UPDATE users SET username = ?, location = ?, bio = ? ,pic = ?, freelancer = ?, m_number = ?, specialties = ?, update_date = ?  WHERE id = ? LIMIT 1";
      $stmt = $db->prepare($sql);//prepared statement
      $stmt->bind_param("ssssisssi", $this->username,$this->location,$this->bio,$pic_name,$this->freelancer,$this->m_number,$this->specialties,$this->update_date,$userid);//bind params
      $result = $stmt->execute();//execute query

      }

      if($db->affected_rows > 0){
        return $result;
      }
    }

    public function update_password($userid)
    {
      global $db;

      $this->password = $_POST["newpassword"];
      $current_password = $_POST["currentpassword"];

      //get some user details from database.
      $user = self::find_by_id($userid);

      //check if passowrd matches the user's password in the database.
      if(password_verify($current_password , $user->password) && $user->block == 0){

      //hash the new password.
      $encrypted = password_hash($this->password,PASSWORD_DEFAULT);

      //single quotes all around values.
      $sql = "UPDATE users SET password = ?  WHERE id = ? LIMIT 1";
      $stmt = $db->prepare($sql);//prepared statement
      $stmt->bind_param("si", $encrypted,$userid);//bind params
      $result = $stmt->execute();//execute query

      if($db->affected_rows > 0){
        return $result;
        }
      }
      else{

      $result = false;

      return $result;
      }

    }

    public function reset_password($email)
    {
      global $db;

      $this->password = $_POST["newpassword"];

      //hash the new password.
      $encrypted = password_hash($this->password,PASSWORD_DEFAULT);

      //single quotes all around values.
      $sql = "UPDATE users SET password = ?  WHERE email = ? LIMIT 1";
      $stmt = $db->prepare($sql);//prepared statement
      $stmt->bind_param("ss", $encrypted,$email);//bind params
      $result = $stmt->execute();//execute query

      if($db->affected_rows > 0){
        return $result;
        }
    }

    public function update_block_status($userid)
    {
      global $db;

      $this->block = $_POST["status"];
      $this->block_exp = $_POST["duration"];
      $due = $this->block_exp;

      if($this->block != 0 && $due != "Indefinite"){
      $expire = date('Y-m-d H:i:s', strtotime("+". $due . ""));
      }
      else{
        $expire = "none";
      }

        //single quotes all around values. 
      $sql = "UPDATE users SET block = ?, block_exp = ? WHERE id = ? LIMIT 1";
      $stmt = $db->prepare($sql);//prepared statement
      $stmt->bind_param("isi", $this->block,$expire,$userid);//bind params
      $result = $stmt->execute();//execute query

      if($db->affected_rows > 0){
        return $result;
        }
    }

    public function update_admin_status($userid)
    {
     global $db;

      $this->admin = $_POST["adminstatus"];

      //single quotes all around values. 
      $sql = "UPDATE users SET admin = ? WHERE id = ? LIMIT 1";
      $stmt = $db->prepare($sql);//prepared statement
      $stmt->bind_param("ii", $this->admin,$userid);//bind params
      $result = $stmt->execute();//execute query

      if($db->affected_rows > 0){
        return $result;
        }
    }

    public function remove_profile($userid,$upload_folder = 'uploads/')
    {
      global $db;

        //get some user details from database without slashes being included and update username session.
        $user = self::find_by_id($userid);

        //if user uploads a picture delete the user's previous picture from the uploads folder. check if file exist to prevent errors.
        if(file_exists($upload_folder . $user->pic) && !empty($user->pic)) { unlink($upload_folder . $user->pic);}

      $username = "user".$userid;
      $empty = "";

        //single quotes all around values. mysqli syntax quotes inside the $pic_name variable.
      $sql = "UPDATE users SET username = ?, email = ?, password = ?, date = ?, time = ?, block = ?, admin = ?, bio = ?, pic = ?, location = ?, points = ?, block_exp = ?, read_announcement = ?, fb_user_id = ? WHERE id = ? LIMIT 1";
      $stmt = $db->prepare($sql);//prepared statement
      $stmt->bind_param("sssssiisssisisi", $username,$empty,$empty,$empty,$empty,$empty,$empty,$empty,$empty,$empty,$empty,$empty,$empty,$empty,$userid);//bind params
      $result = $stmt->execute();//execute query

      if($db->affected_rows > 0){
        return $result;
        }
    }



    // public function delete(){

    //   global $db;

    //   $sql = "DELETE FROM `users` WHERE id = ? LIMIT 1";
    //   $stmt = $db->prepare($sql);//prepared statement
    //   $stmt->bind_param("i", $this->id);//bind params
    //   $result = $stmt->execute();//execute query

    //   if($result){

    //     $_SESSION["message"] = "data succesfully deleted.";

    //     unset($_SESSION["message"]);

    //     	redirect_to($home);
    //     }
    //    else {
    //    	 $msg = "<br><center><span class='error-feedback-messages'>Something went wrong.</span></center>";
    //    	 return $msg;
    //     }

    // }



    public static function find_user_profile($userid=0)
    {
      global $db;

      $sql = "SELECT username,pic FROM users WHERE id= ?";
      $stmt = $db->prepare($sql);//prepared statement
      $stmt->bind_param("i", $userid);//bind params
      $stmt->execute();//execute query
      $result = $stmt->get_result();//return results

      $user = $result->fetch_object(); 

      if ($user != null) {

      $username = $user->username;

      $pic = htmlentities($user->pic);

      $picture_url = get_pic_location($pic);

      return array($username,$picture_url);
      }
    }

    public static function find_profile_by_name($name="")
    {
      global $db;

      $sql = "SELECT * FROM users WHERE username= ? LIMIT 1";
      $stmt = $db->prepare($sql);//prepared statement
      $stmt->bind_param("s", $name);//bind params
      $stmt->execute();//execute query
      $result = $stmt->get_result();//return results

      return $result->fetch_object('User');
    }

    public function auth_edit_show_freelancer($session_userid,$userid,$freelancer=0)
    {
      if(isset($session_userid) && $session_userid == $userid) {
      
        $profile_options = "<br><a style='text-decoration:none;' class='edit-profile' href='editprofile'>Edit profile</a>";
      }
      elseif($freelancer == 1){
        $profile_options = "<br><span style='text-decoration:none;' class='freelancer'>Freelancer</span>";
      }
      else { 
        $profile_options = "";
      } 

      return $profile_options;
    }

    public function auth_user_admin_access($session_userid,$userid,$block,$admin)
    {
      if(isset($session_userid) && $session_userid == $userid && $block == 0 && $admin == 1) {

      $remove_slash = "";
        
        //if site is in production mode (not needing the 'techsup' dir) strip slash else add slash to development directory.
        if(dirname($_SERVER['PHP_SELF']) == "/thetecsup"){ $remove_slash = "/";}else{$remove_slash = "";}
      
      $admin_access = "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; <a style='text-decoration:none;' class='admin-link' href='". (isset($_SERVER['HTTPS']) ? "https" : "http") ."://".$_SERVER['HTTP_HOST'].dirname($_SERVER['PHP_SELF']).$remove_slash."admin/admindash?login=true' target='_blank'>Go to admin</a>";
      }
      else { 
        $admin_access = "";
      } 

      return $admin_access;
    }

    public function update_points($userid=0,$reward=0)
    {
      global $db;

      $sql = "UPDATE users SET points = points + ? WHERE id = ? LIMIT 1";
      $stmt = $db->prepare($sql);//prepared statement
      $stmt->bind_param("ii", $reward,$userid);//bind params
      $stmt->execute();//execute query
    }

    public function subtract_points($userid=0,$subtract=0)
    {
      global $db;

      $sql = "UPDATE users SET points = points - ? WHERE id = ? LIMIT 1";
      $stmt = $db->prepare($sql);//prepared statement
      $stmt->bind_param("ii", $subtract,$userid);//bind params
      $stmt->execute();//execute query
    }

    public function block_status($block)
    {
      if($block == 0)
        {
          $status = '<span style="color:green;">Active</span>';
        } 
        else {
          $status = '<span style="color:red;">blocked</span>';
        } 

        return $status;
    }

    public function admin_status($admin)
    {
      if($admin == 0)
        {
          $status = '<span style="color:#1e2933;">Normal user</span>';
        } 
        else {
          $status = '<span style="color:lightgreen;">admin</span>';
        } 

        return $status;
    }

    public function check_email_exist($email)
    {
      global $db;

      //Check if email macthes a user in the database.
      $sql = "SELECT email FROM users WHERE email= ? LIMIT 1";
      $stmt = $db->prepare($sql);//prepared statement
      $stmt->bind_param("s", $email);//bind params
      $stmt->execute();//execute query
      $result = $stmt->get_result();//return results

        return $result;
    }

    public static function read_announcements_status()
    {
      global $db;
      global $session;

      $object = new self;

      //get Read_announce status from database.
      $sql = "SELECT read_announcement FROM users WHERE id = ? LIMIT 1";
      $stmt = $db->prepare($sql);//prepared statement
      $stmt->bind_param("i", $session->user_id);//bind params
      $stmt->execute();//execute query
      $result = $stmt->get_result();//return results

      $record = $result->fetch_assoc();

      $object->read_announcement = $record['read_announcement'];

      return $object;
    }

    public function update_read_announcements_status($userid)
    {
      global $db;

      $sql = "UPDATE users SET read_announcement = 1 WHERE id = ? AND read_announcement = 0 LIMIT 1";
      $stmt = $db->prepare($sql);//prepared statement
      $stmt->bind_param("i", $userid);//bind params
      $stmt->execute();//execute query
    }

    public function default_read_announcements_status()
    {
      global $db;

      $sql = "UPDATE users SET read_announcement = 0 WHERE read_announcement = 1";
      $stmt = $db->prepare($sql);//prepared statement
      $stmt->execute();//execute query
    }

    // A limit of 50 emails are allowed to be sent by the shared hosting to protect emails from being flagged as spam.
    // mailchimp or any 3rd party maillisting app are recommended if site grows.
    public static function find_all_emails($empty_emails = "")
    {
      global $db;

      //select all rows from database.
      $sql = "SELECT email FROM users WHERE is_sent = 0 AND email <> ? AND block = 0 ORDER BY id ASC LIMIT 50";
      $stmt = $db->prepare($sql);//prepared statement
      $stmt->bind_param("s", $empty_emails);//bind params
      $stmt->execute();//execute query
      $result = $stmt->get_result();//return results

      return $result;

    }

    //Send emails with SMTP protocol. Production email address must be different from development email address.
    //the $from varaible shall use <> at both ends of the email if the default php mail function is going to be used.
    //<mail@thetecsup.com> for production
    public function send_bulk_mails($subject,$message,$from="hello@example.com")
    {
      global $db;
      global $site_title;

      //initialize php mailer and set it up
      $phpmailer = new PHPMailer();
      $phpmailer->isSMTP();
      $phpmailer->Host = 'sandbox.smtp.mailtrap.io';
      $phpmailer->SMTPAuth = true;
      $phpmailer->Port = 2525;
      $phpmailer->Username = 'd758d62e34a91d';
      $phpmailer->Password = '6f806db6fe9f7e';

      //mail details
      $phpmailer->setFrom($from,$site_title);
      $phpmailer->Subject = $subject;
      $phpmailer->MsgHTML($message);

      $output = '';//set output to default. default will mean everything is correct. 

      $email_result = self::find_all_emails(); //get all available emails.

      //loop through each email and send mailt o each of them. 
      foreach ($email_result as $emails) { 

      //Send mail
      $phpmailer->addAddress($emails['email']);
      //send the message, check for errors 

      if ($phpmailer->send()) {
      //set is_sent to 1 to prevent sending duplicate messages to given email if this script runs again.
      $this->update_sent($emails['email']);
        }
      else {
        $output = 'error';//assign data to output variable.meaning there was an error.
        }

        //Clear all addresses for the next iteration
        $phpmailer->clearAddresses(); 
      }

      return $output;
    }

    //set is_sent to 1 if email exist and is_sent to 0
    public function update_sent($email)
    {
      global $db;

      $sql = "UPDATE users SET is_sent = 1 WHERE email = ? AND is_sent = 0 AND block = 0 LIMIT 1";
      $stmt = $db->prepare($sql);//prepared statement
      $stmt->bind_param("s", $email);//bind params
      $stmt->execute();//execute query

    }

    //set is_sent to 1 if email exist and is_sent to 0
    public function reset_sent()
    {
      global $db;

      $sql = "UPDATE users SET is_sent = 0 WHERE is_sent = 1 AND block = 0";
      $stmt = $db->prepare($sql);//prepared statement
      $stmt->execute();//execute query

    }

    public static function profile_link($username)
    {
      $profile = "profile?name=".urlencode($username)."";

      return $profile;
    }

    public static function profile_link_admin($username)
    {
      $profile = "../profile?name=".urlencode($username)."";

      return $profile;
    }

    public static function profile_pic($pic)
    {
      $profile_pic = "<img src='{$pic}' alt='...'  class='profile'>";

      return $profile_pic;
    }

    public static function profile_pic_admin($pic)
    {
      $profile_pic = '';

      if (!file_exists('../'.$pic)) {//if pic is not located outside the admin folder and in the upload folder don't add ../
      $profile_pic = "<img src='{$pic}' alt='...'  class='profile'>";
      } 
      else{//add ../
      $profile_pic = "<img src='../{$pic}' alt='...'  class='profile'>";
      }

      return $profile_pic;
    }
}

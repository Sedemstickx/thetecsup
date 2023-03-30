<?php

class Session
{ 
    private $logged_in = false;
    private $admin_logged_in = false;
    public $user_id = 0;
    private $lifetime = 86400 * 365;
    public $active_username = null;

    //initiate auth token verfification properties to false.
    private $is_token_verified = false;
    private $is_expiry_date_verified = false;    

    public function __construct()
    {
      //call this functions once an instance is created.

      session_start();

      $this->check_login();
      $this->check_admin_login();
    }

    public function is_logged_in()
    {//getters
      
      return $this->logged_in;
    }

    public function is_admin_logged_in()
    {//getters
      
      return $this->admin_logged_in;
    }

    public function logout()
    {
        global $cookie_token;
        global $home;

        //clear session
        session_destroy();

        //delete user login data from database.
        Auth::delete($cookie_token);

        //clear cookies
        setcookie("tsp_token", null, time() - 3600);

        redirect_to($home);

    }


    public function message($msg="")
    {
      if(!empty($msg)){
      $_SESSION["message"] = $msg;
      }
    }    


    private function check_login()
    {
      global $cookie_token;

      //check if token matches a user in our database. else unset them.
      if (!empty($cookie_token)) {
        
        $current_date_time = date('Y-m-d H:i:s');

        $auth = Auth::find_username_by_token($cookie_token,0);

        //check cookie expiration date. auth token verfifications to true if conditions are met.
        if ($auth != null && !empty($auth->id) && $auth->expire >= $current_date_time) {
        $this->is_token_verified = true;
        $this->is_expiry_date_verified = true;
        }

        //get user details
        $user = User::find_by_username($auth->username);

        //set $this->logged_in to true and set $this->user_id from given user id if conditions are true
        //else, mark the token as expired and clear cookies.
        if (!empty($auth->id) && $this->is_token_verified && $this->is_expiry_date_verified && $user->block === 0) {
        
        $this->user_id = $user->id;
        $this->logged_in = true;//setters

        }
        elseif (!empty($auth->id)){       
          //mark is_expired as true (1).
          $auth->mark_as_expired($auth->id);

          //unset userid and set $this->user_id to false.
          unset($this->user_id);
          $this->logged_in = false;//setters        
          } 
      } 

    }



    private function check_admin_login()
    { 
        global $cookie_token;

        //check if token matches a user in our database. else unset them.
        if (!empty($cookie_token)) {
        
        $current_date_time = date('Y-m-d H:i:s');

        $auth = Auth::find_username_by_token($cookie_token,0);

        //check cookie expiration date. auth token verfifications to true if conditions are met.
        if (!empty($auth->id) && $auth->expire >= $current_date_time) {
        $this->is_token_verified = true;
        $this->is_expiry_date_verified = true;
        }

        //get user details
        $user = User::find_by_username($auth->username);

        //set $this->admin_logged_in to true if conditions are true
        //else, mark the token as expired and prevent user from accessing page by setting $this->admin_logged_in and to false and clear cookies.
        if ($auth != null && !empty($auth->id) && $this->is_token_verified && $this->is_expiry_date_verified && $user->block === 0 && $user->admin === 1) {
        
        $this->admin_logged_in = true;//setters
        $this->active_username = $user->username;

        }
        else{
        //set $this->user_id to false.
        $this->admin_logged_in = false;//setters
        } 
      } 

    }
}

//create instance and use the construct function to call some functions inside it automatically.
$session = new Session(); 
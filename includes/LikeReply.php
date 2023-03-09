<?php 

class LikeReply
{

    public $id;
    public $userid;
    public $like_id;


    public function create()
    {
      global $db;
      global $session;

      $this->userid = $session->user_id;
      //must be post. get for testing.
      //ensure that arguement is an int value.
      $this->like_id = (int) $_POST['like_id'];
      
      //insert data into database. Single quotes all around values.
      $sql = "INSERT INTO like_reply (userid,like_id) VALUES (?, ?)";

      $stmt = $db->prepare($sql);//prepared statement
      $stmt->bind_param('ii',$this->userid,$this->like_id);//bind params
      $result = $stmt->execute();//execute query
      $stmt->close();//close statement

      return $result;
    }

    public function delete()
    {
      global $db;
      global $session;

      //must be post. get for testing.
        //ensure that arguement is an int value.
      $this->like_id = (int) $_POST['unlike_id'];

      //delete data from db.
      $sql = "DELETE FROM like_reply WHERE userid = ? AND like_id = ? LIMIT 1";
      $stmt = $db->prepare($sql);//prepared statement
      $stmt->bind_param("ii", $session->user_id,$this->like_id);//bind params
      $result = $stmt->execute();//execute query

      return $result;
    }

    public static function find_all_by_like_id($reply_id,$per_page,$pagination_offset)
    {
      global $db;

      //select all rows from database.
      $sql = "SELECT like_reply.id,like_reply.userid,like_reply.like_id,users.username,users.pic,users.points FROM like_reply JOIN users ON like_reply.userid = users.id WHERE like_id = ? ORDER BY id DESC LIMIT {$per_page} OFFSET {$pagination_offset}";
      $stmt = $db->prepare($sql);//prepared statement
      $stmt->bind_param("i", $reply_id);//bind params
      $stmt->execute();//execute query
      $result = $stmt->get_result();//return results

      return $result;
    }

    public static function find_by_like_id($reply_id=0)
    {
      global $db;
      global $session;

      //ensure that arguement is an int value.
      $reply_id = (int) $reply_id;

      //select all questions rows from database.
      $sql = "SELECT id FROM like_reply WHERE userid = ? AND like_id = ?";
      $stmt = $db->prepare($sql);//prepared statement
      $stmt->bind_param("ii", $session->user_id,$reply_id);//bind params
      $stmt->execute();//execute query
      $result = $stmt->get_result();//return results

      return $result->num_rows;
    }

    public static function count_by_like_id($reply_id=0)
    {
      global $db;

        //sanitize strings.
      $reply_id =  (int) $reply_id;

      $sql = "SELECT id FROM like_reply WHERE like_id = ?";
      $stmt = $db->prepare($sql);//prepared statement
      $stmt->bind_param("i", $reply_id);//bind params
      $stmt->execute();//execute query
      $result = $stmt->get_result();//return results
      
      return $result->num_rows;
    }

    public function like_button($reply_id=0)
    {
      global $db;
      global $session;

      //check if user has liked the post or not and provide options. Check if a user is logged in to display the lik/unlike buttons.
      $like_result_num = self::find_by_like_id($reply_id); 

      if(!$session->is_logged_in()){ $like_button = "";}

      elseif ($like_result_num >= 1) {
      
      $like_button = "&nbsp;<button id='unlike' value='".urlencode($reply_id)."' class='like_button'>Unlike</button>";

      } 
      else{

      $like_button = "&nbsp;<button id='like' value='".urlencode($reply_id)."' class='like_button'>Like</button>";
      
      }

      return $like_button;
    }

    public function like_unlike_field($reply_result_id)
    {
      $like_unlike_feild =  '<span id="like_buttons_'. $reply_result_id . '">
    
        '.$like_button = $this->like_button($reply_result_id).'

          </span>';

      return $like_unlike_feild;
    }

    public function num_of_likes($reply_id=0)
    {
      $reply_likes_num = self::count_by_like_id($reply_id);

      $break_line = '';

      //if $reply_likes_num is equal to zero do not display any reply likes
      if($reply_likes_num == 0) { 
        $reply_likes_num = null; 
      }
      else{ 
      $reply_likes_num = number_format($reply_likes_num);  
      $break_line= '<br>';
      }

      $number_of_likes = "<a class='likes-link' href='reply_likes?lid=".urlencode($reply_id)."'><span id='like_num_{$reply_id}' class='no-margin'>{$reply_likes_num}</span> <span id='like_num_grammar_{$reply_id}' class='no-margin'>{$this->num_grammar($reply_likes_num)}{$break_line}</span></a>"; 

      return $number_of_likes;
    }

    public function num_of_likes_admin($reply_id=0)
    {
      $reply_likes_num = self::count_by_like_id($reply_id);

      //if $reply_likes_num is equal to zero do not display any reply likes
      if($reply_likes_num == 0) { 
        $reply_likes_num = null; 
      }
      else{ 
      $reply_likes_num = number_format($reply_likes_num);
      }

      $number_of_likes = "<span>{$reply_likes_num}</span> <span>{$this->num_grammar($reply_likes_num)}</span>"; 

      return $number_of_likes;
    }

    public function num_grammar($records=0,$singular_string="person liked this",$first_plural_string="",$plural_string="people liked this")
    {
      if($records == 0){
      $grammer = $first_plural_string;
      }
      else if($records == 1){
      $grammer =  $singular_string;
      }  
      else {
      $grammer = $plural_string;
      }

      return $grammer;

    }

    public function delete_all_related_likes($reply_id=0)
    {
      global $db;

        //sanitize strings.
      $reply_id =  (int) $reply_id;

      //delete all replies to the given question.
      $sql = "DELETE FROM like_reply WHERE like_id = ?";
      $stmt = $db->prepare($sql);//prepared statement
      $stmt->bind_param("i", $reply_id);//bind params
      $result = $stmt->execute();//execute query

      return $result;
    }
}
<?php

class Auth
{
    public $id;
    public $username;
    public $token;
    public $expire;
    public $is_expired;

    public function create_token($username,$token,$expire)
    {
      global $db;

      //insert data into database. Single quotes all around values.
      $sql = "INSERT INTO login_auth (username,token,expire) VALUES (?, ?, ?)";

      $stmt = $db->prepare($sql);//prepared statement
      $stmt->bind_param('sss',$username,$token,$expire);//bind params
      $result = $stmt->execute();//execute query
      $stmt->close();//close statement

      return $result;
    }

    public static function find_username_by_token($token,$expired)
    {
      global $db;

      $expired = (int)$expired;

      $sql = "SELECT * FROM login_auth WHERE token= ? AND is_expired= ? LIMIT 1";
      $stmt = $db->prepare($sql);//prepared statement
      $stmt->bind_param("si", $token,$expired);//bind params
      $stmt->execute();//execute query
      $result = $stmt->get_result();//return results

      return $result->fetch_object('Auth');
    }

    public function mark_as_expired($token_id)
    {
      global $db;

      $sql = "UPDATE login_auth SET is_expired = 1 WHERE id= ? LIMIT 1";
      $stmt = $db->prepare($sql);//prepared statement
      $stmt->bind_param("i", $token_id);//bind params
      $result = $stmt->execute();//execute query

        return $result;
    }

    public function update_name($username,$auth_username)
    {
      global $db;
      global $session;

      $sql = "UPDATE login_auth SET username = ? WHERE username = ?";
      $stmt = $db->prepare($sql);//prepared statement
      $stmt->bind_param("ss", $username,$auth_username);//bind params
      $stmt->execute();//execute query
    }

    //delete auth data from the given token. 
    public static function delete($token)
    {
      global $db;

      $sql = "DELETE FROM login_auth WHERE token= ? LIMIT 1";
      $stmt = $db->prepare($sql);//prepared statement
      $stmt->bind_param("s", $token);//bind params
      $result = $stmt->execute();//execute query

      return $result;
    }
}
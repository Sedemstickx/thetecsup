<?php

class Pwdreset
{
    public $id;
    public $email;
    public $token;
    public $expire;

    public function create_token()
    {
      global $db;
      global $site_title;

      $this->email = $_POST["email"];
      $this->token = bin2hex(random_bytes(12));
      $this->expire = date('Y-m-d H:i:s', strtotime("+1day"));

      //insert data into database. Single quotes all around values.
      $sql = "INSERT INTO pwdreset (email,token,expire) VALUES (?, ?, ?)";

      $stmt = $db->prepare($sql);//prepared statement
      $stmt->bind_param('sss',$this->email,$this->token,$this->expire);//bind params
      $result = $stmt->execute();//execute query
      $stmt->close();//close statement

      //link should be changed when site goes online.
      $message = "Dear user,<br>

      <p>A password reset was requested on the account with this email from the IP address: {$_SERVER['REMOTE_ADDR']}</p> 

          <p>Please click on the link below or copy and paste it in your browser to visit and reset your forgotten password. </p>
      <a href=".(isset($_SERVER['HTTPS']) ? "https" : "http") ."://{$_SERVER['HTTP_HOST']}/password_reset?token=". urlencode($this->token) ."&email=". urlencode($this->email) .">".(isset($_SERVER['HTTPS']) ? "https" : "http") ."://{$_SERVER['HTTP_HOST']}/password_reset?token={$this->token}&email=".htmlentities($this->email)."</a>
          <p>Please note that this link will expire after 24 hours for your security. If you did not request to reset this forgotten password
          or your IP address doesn't match the one above no action is needed. You can <a href='mailto:{$site_title}@gmail.com'>Contact us</a> if there is an issue.</p>
          <p>You can delete this message if you don't need it anymore.</p>";

      //Send password reset link to user's email address.
      send_email($this->email,"Password Reset",$message); 

      return $result;
    }

    public static function find_by_token_email($token='',$email='')
    {
      global $db;

      //select a particular row based on given id.
      $sql = "SELECT * FROM pwdreset WHERE token= ? AND email= ? LIMIT 1";
      $stmt = $db->prepare($sql);//prepared statement
      $stmt->bind_param("ss", $token,$email);//bind params
      $stmt->execute();//execute query
      $result = $stmt->get_result();//return results

      return $result->fetch_object('Pwdreset');
    }

    public function delete($email='')
    {
      global $db;

      $sql = "DELETE FROM pwdreset WHERE email = ?";
      $stmt = $db->prepare($sql);//prepared statement
      $stmt->bind_param("s", $email);//bind params
      $result = $stmt->execute();//execute query

      return $result;
    }
}
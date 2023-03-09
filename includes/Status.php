<?php

class Status
{ 
    public $session_id;
    public $time;


    public function __construct()
    {
      //call these functions once an instance is created.
      //Check if user visiting the website has a session_id already available in the database.
      $count = $this->count_sessions();

      //if count is equal to 0 insert a new session_id of the user visiting the site else update the time
      //of the given session_id
      if($count == "0"){
      $this->create();
      }
      else {
      $this->update();
      }

      //delete session_ids that are dormant and haven't beeen updated after sometime.
      $this->delete();
    }

    public function create()
    {  
      global $db;

      $this->session_id = session_id();
      $this->time = time();

      //Insert PHPSESSID and given time into status db table.
      $sql="INSERT INTO status (session_id, time) VALUES (?, ?)";

      $stmt = $db->prepare($sql);//prepared statement
      $stmt->bind_param('si',$this->session_id,$this->time);//bind params
      $result = $stmt->execute();//execute query
      $stmt->close();//close statement

      return $result;
    }

    public function count_sessions()
    {
      global $db;

      $this->session_id = session_id();

      //check if user session_id matches a record in the database and count it to be updated by the update method.
      $sql = "SELECT session_id FROM status WHERE session_id = ?";
      $stmt = $db->prepare($sql);//prepared statement
      $stmt->bind_param("s", $this->session_id);//bind params
      $stmt->execute();//execute query
      $result = $stmt->get_result();//return results
      
      return $result->num_rows;
    }

    public function count_all()
    {
      global $db;

      //count all available session_id in the db table.
      $sql = "SELECT session_id FROM status";
      $stmt = $db->prepare($sql);//prepared statement
      $stmt->execute();//execute query
      $result = $stmt->get_result();//return results
      
      return $result->num_rows;
    }

    public function update()
    {
      global $db;

      $this->session_id = session_id();
      $this->time = time();

      //Update time to current time to prevent the delete function
      //from deleting session_id with db time that are less than the current time.
      $sql="UPDATE status SET time = ? WHERE session_id = ?";
      $stmt = $db->prepare($sql);//prepared statement
      $stmt->bind_param("is", $this->time,$this->session_id);//bind params
      $result = $stmt->execute();//execute query

      return $result;
    }

    public function delete()
    {
      global $db;

      $this->time = time();

      //Reduce given current time() by subtracting some mins from it.This gives the session_id
      //sometime before delete query deletes a session_id that hasn't updated it's time.
      $time_check = $this->time - 600;

      //delete all data where database time is less than the current time. 
      $sql="DELETE FROM status WHERE time < ?";
      $stmt = $db->prepare($sql);//prepared statement
      $stmt->bind_param("i", $time_check);//bind params
      $result = $stmt->execute();//execute query

      return $result;
    }
}

//create instance and use the construct function to call some functions inside it automatically.
$online_status = new Status(); 
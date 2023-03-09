<?php

class Announcement
{
    public $id;
    public $title;
    public $message;
    public $date;


    public function create()
    {
      global $db;

      $this->title = $_POST["title"];
      $this->message = $_POST["message"];
      $this->date = date('Y-m-d');

      //remove some harmful html tag inputs in text from user.  
      $this->message = strip_tags($this->message,'<b><a>');

      //insert data into database. Single quotes all around values.
      $sql = "INSERT INTO announcements (title, message, date) VALUES (?, ?, ?)";

      $stmt = $db->prepare($sql);//prepared statement
      $stmt->bind_param('sss',$this->title,$this->message,$this->date);//bind params
      $result = $stmt->execute();//execute query
      $stmt->close();//close statement

        return $result;
    }

    public static function count_all()
    {
      global $db;

      $sql = "SELECT id FROM announcements";
      $stmt = $db->prepare($sql);//prepared statement
      $stmt->execute();//execute query
      $result = $stmt->get_result();//return results
      
      return $result->num_rows;
    }

    public function count_duplicate($title)
    {
      global $db;

      $sql = "SELECT id FROM announcements WHERE title = ? LIMIT 1";
      $stmt = $db->prepare($sql);//prepared statement
      $stmt->bind_param("s", $title);//bind params
      $stmt->execute();//execute query
      $result = $stmt->get_result();//return results
      
      return $result->num_rows;
    }

    public static function find_all($per_page,$pagination_offset)
    {
      global $db;

      //select all rows from database.
      $sql = "SELECT * FROM announcements ORDER BY id DESC LIMIT {$per_page} OFFSET {$pagination_offset}";
      $stmt = $db->prepare($sql);//prepared statement
      $stmt->execute();//execute query
      $result = $stmt->get_result();//return results

      return $result;
    }

    public static function find_latest()
    {
      global $db;

      //select all rows from database.
      $sql = "SELECT * FROM announcements ORDER BY id DESC LIMIT 1";
      $stmt = $db->prepare($sql);//prepared statement
      $stmt->execute();//execute query
      $result = $stmt->get_result();//return results

      return $result->fetch_object();
    }

    public static function find_by_id($id)
    {
      global $db;

      $sql = "SELECT * FROM announcements WHERE id= ? LIMIT 1";
      $stmt = $db->prepare($sql);//prepared statement
      $stmt->bind_param("i", $id);//bind params
      $stmt->execute();//execute query
      $result = $stmt->get_result();//return results

      return $result->fetch_object('Announcement');
    }


    public function delete()
    {
      global $db;

      $this->id = $_GET['id'];

      $sql = "DELETE FROM announcements WHERE id = ? LIMIT 1";
      $stmt = $db->prepare($sql);//prepared statement
      $stmt->bind_param("i", $this->id);//bind params
      $result = $stmt->execute();//execute query

      return $result;
    }
}
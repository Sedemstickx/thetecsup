<?php

class topic
{
    public $id;
    public $topic;
    public $icon;
    public $about;

    
    public function create()
    {
      global $db;

      $this->topic = $_POST["topic"];
      $this->icon = basename($_FILES["image_upload"]["name"]);
      $this->about = $_POST["about"];

      //upload picture and return picture name.
      $icon_name = image_upload($this->icon,'../uploads/');

      //insert data into database. Single quotes all around values.
      $sql = "INSERT INTO topics (topic,icon,about) VALUES (?, ?, ?)";

      $stmt = $db->prepare($sql);//prepared statement
      $stmt->bind_param('sss',$this->topic,$icon_name,$this->about);//bind params
      $result = $stmt->execute();//execute query
      $stmt->close();//close statement

      return $result;
    }

    public static function count_all()
    {
      global $db;

      $sql = "SELECT id FROM topics";
      $stmt = $db->prepare($sql);//prepared statement
      $stmt->execute();//execute query
      $result = $stmt->get_result();//return results
      
      return $result->num_rows;
    }

    public static function find_by_topic($topic="")
    {
      global $db;

      //select a particular row based on given id.
      $sql = "SELECT * FROM topics WHERE topic = ? LIMIT 1";
      $stmt = $db->prepare($sql);//prepared statement
      $stmt->bind_param("s", $topic);//bind params
      $stmt->execute();//execute query
      $result = $stmt->get_result();//return results

      return $result->fetch_object();
    }

    public static function find_by_id($id=0)
    {
      global $db;

      //select a particular row based on given id.
      $sql = "SELECT * FROM topics WHERE id = ? LIMIT 1";
      $stmt = $db->prepare($sql);//prepared statement
      $stmt->bind_param("i", $id);//bind params
      $stmt->execute();//execute query
      $result = $stmt->get_result();//return results

      return $result->fetch_object('topic');
    }

    public static function find_all($per_page,$pagination_offset)
    {
      global $db;

      //select all topic rows from database.
      $sql = "SELECT * FROM topics ORDER BY topic ASC LIMIT {$per_page} OFFSET {$pagination_offset}";
      $stmt = $db->prepare($sql);//prepared statement
      $stmt->execute();//execute query
      $result = $stmt->get_result();//return results

      return $result;
    }

    public static function find_topic_list()
    {
      global $db;

      
      if (isset($_GET['topic'])) {
      
      $topic = $_GET['topic'];  
      
      //select all topic rows from database where a topic is set.
      $sql = "SELECT topic FROM topics WHERE topic = ? ORDER BY topic ASC";
      $stmt = $db->prepare($sql);//prepared statement
      $stmt->bind_param("s", $topic);//bind params
      $stmt->execute();//execute query
      $result = $stmt->get_result();//return results

      return $result;
      } 
      else{
        //select all topic rows from database.
      $sql = "SELECT topic FROM topics ORDER BY topic ASC";
      $stmt = $db->prepare($sql);//prepared statement
      $stmt->execute();//execute query
      $result = $stmt->get_result();//return results

      return $result;
      }
    }

    public static function refactored_find_all()
    {
      $topics = "";

      $topic_result = self::find_topic_list();
      while ($topic = $topic_result->fetch_object()){ 

      $topics .= "<option value='{$topic->topic}'>" . htmlentities($topic->topic) . "</option>";   
      }
        
        return $topics;
    }

    public function update($topic_id=0,$upload_folder = '../uploads/')
    {
        global $db;

        $this->topic = $_POST["topic"];
        $this->about = $_POST["about"];
        $this->icon = basename($_FILES["image_upload"]["name"]);

        //get some topic details from database.
        $topic = self::find_by_id($topic_id);

        //if user uploads a picture delete the user's previous picture from the uploads folder. check if file exist to prevent errors.
        if($this->icon != null && file_exists($upload_folder . $topic->icon) && !empty($topic->icon)) { unlink($upload_folder . $topic->icon);}
        
        //upload picture and return picture name.
        $basename = image_upload($this->icon,$upload_folder);

        //Update Pic column in the database if a new picture name is available else use the current pic name.
        $icon_name = $basename != null ? $basename : $topic->icon;

        //single quotes all around values. mysqli syntax quotes already inside the $icon_name variable.
        $sql = "UPDATE topics SET topic = ?, about = ?, icon = ? WHERE id = ? LIMIT 1";
        $stmt = $db->prepare($sql);//prepared statement
        $stmt->bind_param("sssi", $this->topic,$this->about,$icon_name,$topic_id);//bind params
        $result = $stmt->execute();//execute query

        if($db->affected_rows > 0){
        return $result;
        }
    }

    public static function link($topic)
    {
      $topic = trim($topic);//trip white spaces in topic

      $topic = "topic?topic=".urlencode(strtok($topic, ","))."";

      return $topic;
    }

    public static function link_admin($topic)
    {
      $topic = trim($topic);//trip white spaces in topic
      
      $topic = "../topic?topic=".urlencode(strtok($topic, ","))."";

      return $topic;
    }

    public static function topic_tags($post_topics)
    {
        //List topics
      $topic = "";
      
      if (!empty($post_topics)) {
      
      $tags = explode(",", $post_topics);

      foreach ($tags as $key => $tag) {
      
        $tag = trim($tag);//trip white spaces in topic tags

        $topic .= "<span class='tagify'>".htmlentities($tag)." &times;</span> ";//to be used when adding or editing topics

        }

      return $topic;
      } 
    }

    public static function topics_list($post_topics)
    {
        //List topics
      $topic = "";
      
      if (!empty($post_topics)) {
      
      $tags = explode(",", $post_topics);

      foreach ($tags as $key => $tag) {
      
        $tag = trim($tag);//trip white spaces in topic tags

        $topic .= "<span class='tags'><a href=".self::link($tag).">".htmlentities($tag)."</a></span> ";

        }

      return $topic;
      } 

    }
}
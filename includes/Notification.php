<?php

class Notification
{
    public $id;
    public $userid;
    public $type;
    public $source_id;
    public $creator_id;
    public $activity_id;
    public $is_viewed;
    public $is_read;
    public $date;
    public $time;

    //$userid is used to get the notification recievers id to show notifs to the actual person it's intended for.
    //$type is used to get the type of notif if notification is created. Used to order notifs by grouping.
    //$source_id is collected and used to get source link queries for page referrals.
    //$creator_id is used to get the user id of the person creating the notif.
    //$activity_id is used the get the id of the content the notif originated from e.g replyid. Mainly used to delete the notif when reply is deleted.
    //users cannot be deleted so data could be zero.

    public function create_notif($userid,$type,$source_id,$creator_id,$activity_id)
    {
      global $db;

      $this->date = date('Y-m-d');
      $this->time = date('H:i:s');

        //insert data into database. Single quotes all around values.
      $sql = "INSERT INTO notifications (userid,type,source_id,creator_id,activity_id,date,time) VALUES (?, ?, ?, ?, ?, ?, ?)";

      $stmt = $db->prepare($sql);//prepared statement
      $stmt->bind_param('isiiiss',$userid,$type,$source_id,$creator_id,$activity_id,$this->date,$this->time);//bind params
      $stmt->execute();//execute query
      $stmt->close();//close statement
    }

    public static function count_not_viewed($userid=0)
    {
      global $db;

      $sql = "SELECT id FROM notifications WHERE userid = ? AND creator_id <> ? AND is_viewed = 0";
      $stmt = $db->prepare($sql);//prepared statement
      $stmt->bind_param("ii", $userid,$userid);//bind params
      $stmt->execute();//execute query
      $result = $stmt->get_result();//return results

      $total_count = $result->num_rows;

      return $total_count;
    }

    public static function find_by_userid($userid=0)
    {
      global $db;

      //select all notifs from database for the given notif reciever.
      $sql = "SELECT notifications.id,notifications.userid,notifications.type,notifications.source_id,notifications.creator_id,notifications.activity_id,notifications.date,notifications.time,notifications.is_read,users.username,users.pic FROM notifications JOIN users ON notifications.creator_id = users.id WHERE notifications.userid= ? AND creator_id <> ? ORDER BY id DESC LIMIT 50";
      $stmt = $db->prepare($sql);//prepared statement
      $stmt->bind_param("ii", $userid,$userid);//bind params
      $stmt->execute();//execute query
      $result = $stmt->get_result();//return results

      return $result;
    }

    public function check_read($is_read)
    {
      if ($is_read == 0) {
        $read = "1";
      }
      else{
        $read = "0";
      }

      if($is_read == 0){
      $bgcolor = "background-color:#f5f7fa;";
      }
      else{ 
      $bgcolor = "background-color:white;";
      }

      return array($read,$bgcolor);
    }

    public function update_viewed_status($notif_id=0)
    {
     global $db;

      if(isset($_GET['view']) && $_GET['view'] == "1"){

      $sql = "UPDATE notifications SET is_viewed = 1 WHERE is_viewed = 0 AND userid = ?";
      $stmt = $db->prepare($sql);//prepared statement
      $stmt->bind_param("i", $notif_id);//bind params
      $result = $stmt->execute();//execute query
      }
    }

    public function update_read_status($notif_id=0)
    {
      global $db;

      if(isset($_GET['read']) && $_GET['read'] == "1"){

      $sql = "UPDATE notifications SET is_read = 1 WHERE is_read = 0 AND id = ? LIMIT 1";
      $stmt = $db->prepare($sql);//prepared statement
      $stmt->bind_param("i", $notif_id);//bind params
      $result = $stmt->execute();//execute query
      }

    }

    public function delete($activity_id=0)
    {
      global $db;
      
      $sql = "DELETE FROM notifications WHERE activity_id = ?";
      $stmt = $db->prepare($sql);//prepared statement
      $stmt->bind_param("i", $activity_id);//bind params
      $result = $stmt->execute();//execute query

      return $result;
    }

    public function reply_num_grammar($reply_result_num)
    {
      if($reply_result_num == 0){
      $reply_grammar = '';
      }
      else if($reply_result_num == 1){
      $reply_grammar = 'and ' . $reply_result_num . ' other';
      }  
      else {
      $reply_grammar = 'and ' . number_format($reply_result_num) . ' others';
      }

      return $reply_grammar;
    }

    public function reply_notifs($post_userid,$post_id,$reply_userid,$activity_id)
    {
      global $session;

      //if user posting the reply is not the originator of the quesion create question reply notification.
        if ($post_userid != $session->user_id) {
          $this->create_notif($post_userid,"Question reply",$post_id,$session->user_id,$activity_id);
        }

      //if reply id is not empty and the user is not posting the reply to his/her self and 
      //the question originator id and reply to id is not the same create replies that mainly reply a reply.  
      if (!empty($reply_userid) && $reply_userid != $session->user_id && $post_userid != $reply_userid) {
        $this->create_notif($reply_userid,"Replies",$post_id,$session->user_id,$activity_id);
        }

    }

    public function edit_reply_notifs($post_userid,$reply_userid)
    {
      global $session;

      //if user editing the reply is not the originator of the quesion Edited reply notification.
      if ($post_userid != $session->user_id) {   
      $this->create_notif($post_userid,"Edited reply",$_GET['qid'],$session->user_id,$_GET['id']);
      }

      //if reply id is not empty and the user is not posting the reply to his/her self and 
      //the question originator id and reply to id is not the same create replies that mainly reply a reply.  
      if (!empty($reply_userid) && $reply_userid != $session->user_id && $post_userid != $reply_userid) {
      $this->create_notif($reply_userid,"Edited reply",$_GET['qid'],$session->user_id,$_GET['id']);
      }

    }
}
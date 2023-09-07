<?php

class Report
{
    public $id;
    public $userid;
    public $subject;
    public $message;
    public $activity_id;
    public $type;
    public $date;
    public $time;
    public $viewed;


    public function post_report($type,$activity_id)
    {
      global $db;
      global $session;

      $this->userid = $session->user_id;
      $this->subject = $_POST["subject"];
      $this->message = $_POST["message"];
      $this->activity_id = $activity_id;
      $this->type = $type;
      $this->date = date('Y-m-d');
      $this->time = date('H:i:s');

      //remove harmful html tag inputs in text from user.  
      $this->message = strip_tags($this->message);

      //insert data into database. Single quotes all around values.
      $sql = "INSERT INTO reports(userid,subject,message,activity_id,type,date,time) VALUES (?, ?, ?, ?, ?, ?, ?)";

      $stmt = $db->prepare($sql);//prepared statement
      $stmt->bind_param('ississs',$this->userid,$this->subject,$this->message,$this->activity_id,$this->type,$this->date,$this->time);//bind params
      $result = $stmt->execute();//execute query
      $stmt->close();//close statement

      return $result;
    }

    public static function count_all()
    {
      global $db;

      $sql = "SELECT id FROM reports";
      $stmt = $db->prepare($sql);//prepared statement
      $stmt->execute();//execute query
      $result = $stmt->get_result();//return results
      
      return $result->num_rows;
    }

    public static function count_not_viewed()
    {
      global $db;

      $sql = "SELECT id FROM reports WHERE viewed = 0";
      $stmt = $db->prepare($sql);//prepared statement
      $stmt->execute();//execute query
      $result = $stmt->get_result();//return results

      $total_count = $result->num_rows;

      if($total_count == 0){
      $record = "";
      }
      else{ 
      $record = "<span class='report-alert'>". number_format($total_count) ."</span>";
      }

      return $record;
    }

    public static function find_all($per_page,$pagination_offset)
    {
      global $db;

      //select all replies rows from database.
      $sql = "SELECT reports.id,reports.subject,reports.message,reports.viewed,reports.type,reports.activity_id,reports.date,reports.time,users.username FROM reports JOIN users ON reports.userid = users.id ORDER BY id DESC LIMIT {$per_page} OFFSET {$pagination_offset}";
      $stmt = $db->prepare($sql);//prepared statement
      $stmt->execute();//execute query
      $result = $stmt->get_result();//return results

      return $result;
    }

    // for ccsv export
    public static function all()
    {
      global $db;

      //select all replies rows from database.
      $sql = "SELECT reports.id,reports.subject,reports.message,reports.type,reports.date,reports.time,users.username FROM reports JOIN users ON reports.userid = users.id ORDER BY id DESC";
      $stmt = $db->prepare($sql);//prepared statement
      $stmt->execute();//execute query
      $result = $stmt->get_result();//return results

      return $result;
    }

    public static function find_by_id($id=0)
    {
      global $db;

      //select a particular row based on given id.
      $sql = "SELECT reports.id,reports.subject,reports.message,users.username,users.pic,users.email FROM reports JOIN users ON reports.userid = users.id WHERE reports.id= ? LIMIT 1";
      $stmt = $db->prepare($sql);//prepared statement
      $stmt->bind_param("i", $id);//bind params
      $stmt->execute();//execute query
      $result = $stmt->get_result();//return results

      return $result->fetch_object('Report');
    }

    public function delete()
    {
      global $db;

      $this->id = $_GET['id'];

      $sql = "DELETE FROM reports WHERE id = ? LIMIT 1";
      $stmt = $db->prepare($sql);//prepared statement
      $stmt->bind_param("i", $this->id);//bind params
      $result = $stmt->execute();//execute query

      return $result;
    }

    public function check_view($view)
    {
      if ($view == 0) {
        $viewed = "1";
      }
      else{
        $viewed = "0";
      }

      if($view == 0){
      $read = "<span style='color:orange;font-weight:bold;'> Unread </span>";
      }
      else{ 
      $read = "<span style='color:Green;font-weight:bold;'> Read </span>";
      }


      return array($viewed,$read);
    }

    public function update_view_status($id=0)
    {
      global $db;

      if($_GET['view'] == "1"){

      $sql = "UPDATE reports SET viewed = 1 WHERE viewed = 0 AND id = ? LIMIT 1";
      $stmt = $db->prepare($sql);//prepared statement
      $stmt->bind_param("i", $id);//bind params
      $stmt->execute();//execute query
      }

    }

    public static function post_report_link($post_id)
    {
      $post_report_link = "report?pid=".urlencode($post_id)."&type=Post";

      return $post_report_link;
    }

    public static function reply_report_link($reply_id)
    {
      $reply_report_link = "report?rid=".urlencode($reply_id)."&type=Reply";

      return $reply_report_link;
    }

    public static function user_report_link($user_id)
    {
      $user_report_link = "report?uid=".urlencode($user_id)."&type=user";

      return $user_report_link;
    }

    public function export_csv()
    {
      $filename = "thetecsup-reports.csv";

        // send response headers to the browser
        header( 'Content-Type: text/csv' );
        header( 'Content-Disposition: attachment;filename='.$filename);

      $fp = fopen('php://output', 'w');

        //for columns
        $header_list = array('ID','Subject','Message','Type','Date','time','Report-by');
        fputcsv($fp, $header_list);

          $list = self::all();

          foreach ($list as $fields) {
            fputcsv($fp, $fields);
          }

          //output total to csv
          $total = array('Total','count' => self::count_all());
          fputcsv($fp, $total);

          fclose($fp);
    }
}
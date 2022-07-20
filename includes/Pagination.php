<?php

class Pagination
{
    public $current_page;
    public $per_page;
    public $total_count;


    //can add class properties as arguments in construct functions to be readily available if instance is created.
    public function __construct($per_page=0,$total_count=0)
    {
      $this->current_page = !empty($_GET['page']) ? (int)$_GET['page'] : 1;//return page number if set or use 1 as default page number
      $this->per_page = (int)$per_page;
      $this->total_count = (int)$total_count;
    }


    //return offset.
    public function offset()
    {
        $offset = ($this->current_page-1) * $this->per_page;
        return $offset;
    }


    private function query_string()
    {
      //get url and return components as associative arrays.
      $parse_url = parse_url($_SERVER['REQUEST_URI']);
      
      if(!empty($parse_url['query'])){

      //return all url queries.
      $query = $parse_url['query'];

      //parse query string into a new variable (associative arrays).
      parse_str($query, $params);

      //find and unset the associative array params with the key named 'page'.
      unset($params['page']);
      
      //return unremoved query params.
      $new_query = http_build_query($params);

      return htmlspecialchars($new_query);
      }

    }


    private function total_pages()
    {
      //divide total count of returned results by given per page and return nearest number to be an integer.
      return ceil($this->total_count / $this->per_page);
    }


    public function first_page($php_self,$sinqle_query_string)
    {
      return "<a class='page' href='" . $php_self ."$sinqle_query_string'>".'First '."</a>&nbsp; ";
    } 
   

    public function next_page($php_self,$extra_query_string)
    {
      return "<a class='page' href='" . $php_self ."?page=" . $this->current_page+1 . "$extra_query_string'> Next </a>&nbsp; ";
    }


    public function previous_page($php_self,$extra_query_string)
    {
      return "<a class='page' href='" . $php_self ."?page=" . $this->current_page-1 . "$extra_query_string'> Previous </a>&nbsp; ";
    }


    public function last_page($php_self,$total_pages,$extra_query_string)
    {
      return "<a class='page' href='" . $php_self ."?page=$total_pages$extra_query_string'> Last </a> ";
    }


    //provide page links based on page conditions.to be used on the admin side only soon.
    public function page_links()
    {
        $extra_query_string =""; //for queries that come after the page query.
        $sinqle_query_string = ""; //for queries that come without the page query.

        if(!empty($this->query_string())){
          $extra_query_string = "&" . $this->query_string();
          $sinqle_query_string = "?" . $this->query_string();
        }

        $total_pages = $this->total_pages();
        
        $php_self = basename($_SERVER['PHP_SELF'],".php");//find basename removing file extension.

        if($total_pages > 1) {//if total number of pages are more than 1 show below buttons.
      
        if($this->current_page > 1) {//if page number is greater than one show first page
         echo $this->first_page($php_self,$sinqle_query_string);
        }
      
        //if page number is equal to one show next page link or 
        //if page number is not equal to total number of pages and page number is greater or equal to 2 show next button
        if($this->current_page == 1 || $this->current_page != $total_pages && $this->current_page >= 2) {
         echo  $this->next_page($php_self,$extra_query_string);
        }
      
        //if page number is not equal to 1 and page number is greater than 2 show previous page link
        if ($this->current_page != 1 && $this->current_page >= 3){
         echo $this->previous_page($php_self,$extra_query_string);
        }
      
        if($this->current_page != $total_pages) {//if page number is not equal to total page number showlast page link.
         echo $this->last_page($php_self,$total_pages,$extra_query_string);
        }

        }
    }


    //provide page links based on page conditions.$ajax_function uses given load page js function.
    public function page_load_ajax($per_page,$total_count,$ajax_function)
    {

        $total_pages = $this->total_pages();


        if($total_pages > 1) {//if pages are more than 1 show below button.
      
        //if page number is not equal to total number of pages and page number is greater than 2 show below button
        if($this->current_page == 1 || $this->current_page != $total_pages && $this->current_page >= 2) {
        echo '<div class="show_more_div">
        <span onclick="'.$ajax_function.'(' . $this->current_page+1 . ')" class="load_more">Show more</span>
        </div>';
          }
        }
    }


    public function page_number()
    {  

      $current_number = !empty($_GET['page']) ? $_GET['page'] : 1;//return current page number
      
      $total_num_of_pages = $this->total_pages() == null ? 1 : $this->total_pages();//return total number of pages

      return array($current_number,$total_num_of_pages);
    }

}
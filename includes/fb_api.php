<?php

//fb creds: this is for live site.
// define('FB_APP_ID', '815008239169727');//app id number for this app
// define('FB_APP_SECRET', 'f5a57bd2e9c5b10ba715352cc40fca0b');//sort of like a password for the app
// define('FB_REDIRECT_URL', 'https://www.thetecsup.com/login');//Url fb will redirect to after processing requests
// define('FB_GRAPH_VERSION', 'v11.0');//fb api verion
// define('FB_GRAPH_DOMAIN', 'https://graph.facebook.com/');//refactored fb graph link
// define('FB_APP_STATE', 'thetecsup');//state the name of the app to be returned when fb redirects back to this app

//fb creds
define('FB_APP_ID', '184560943623481');//app id number for this app
define('FB_APP_SECRET', '5a4573d9651560d4483932532ee20248');//sort of like a password for the app
define('FB_REDIRECT_URL', 'https://localhost/techsup/login');//Url fb will redirect to after processing requests
define('FB_GRAPH_VERSION', 'v11.0');//fb api verion
define('FB_GRAPH_DOMAIN', 'https://graph.facebook.com/');//refactored fb graph link
define('FB_APP_STATE', 'thetecsup');//state the name of the app to be returned when fb redirects back to this app


//send a get request to fb with the app credentials(in url queries) for authentication
function get_fb_login_url(){

   $endpoint = 'https://www.facebook.com/'.FB_GRAPH_VERSION.'/dialog/oauth';

   $params = array(
   	'client_id' => FB_APP_ID,
    'redirect_uri' => FB_REDIRECT_URL,
    'state' => FB_APP_STATE,
    'scope' => 'email',
    'auth_type' => 'rerequest',    
    );

   return $endpoint .'?'. http_build_query($params);
}


//send a get request to fb with the app credentials(and fb code) using client url(curl) to get the fb access token
function get_access_token($code){

    $endpoint = 'https://graph.facebook.com/'.FB_GRAPH_VERSION.'/oauth/access_token';

    $params = array(
    'client_id' => FB_APP_ID,
    'client_secret' => FB_APP_SECRET,
    'redirect_uri' => FB_REDIRECT_URL,
    'code' => $code   
    );

   return make_fb_api_call($endpoint,$params);
}


//Use the Client url(curl) library to send a get request to fb without loading a page and return data from fb
function make_fb_api_call($endpoint,$params){

   $ch = curl_init();//initialize function
   curl_setopt($ch, CURLOPT_URL, $endpoint .'?'. http_build_query($params));//get request to fb
   curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);//more options
   curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);//more

   $fbresponse = curl_exec($ch);//executes the call and get our response
   $fbresponse = json_decode($fbresponse, TRUE);//convert json objects to php associative arrays
   curl_close($ch);//close request

   return $fbresponse;

}


// function login_with_fb($get){

//    $status = 'fail';
//    $message = '';
//    $access_token = '';

//    if (isset($get['error'])) {
//        $message = $get['error_description'];
//     }
//      else{
//       $access_token = get_access_token($get['code']);

//       $_SESSION['fb_access_token'] = $access_token['access_token'];

//       $fb_user_info = get_fb_user_info($_SESSION['fb_access_token']);

//     echo "<pre>";
//       print_r($fb_user_info);
//       die();
//    } 

  
//    return array(
//    'status' => $status,
//    'message' => $message
//    );
// }


//send a get request to fb with the field and access token queries using client url(curl) to get the fb user info.
function get_fb_user_info($fb_access_token){

  $endpoint = FB_GRAPH_DOMAIN.'me';

      $params = array(
    'fields' => 'first_name,last_name,email,picture',
    'access_token' => $fb_access_token  
    );

   return make_fb_api_call($endpoint,$params);
}
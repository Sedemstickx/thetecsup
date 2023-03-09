<?php 
require_once "includes/initialize.php"; 

$post = new Post();

//get draft if available.
$post_result = Post::find_by_draft(); 

   $msg="";

//Refactored to post question.
$post->create($post_result);//an instance is already called in the find_by_draft method so this variable can be used with the parameter.
?>
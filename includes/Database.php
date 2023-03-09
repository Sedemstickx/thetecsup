<?php
// Create connection
$db = new mysqli("localhost","root","","techsup");

// Check connection
if ($db->connect_error) {
  die("Connection failed.");
}
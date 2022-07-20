<?php //closing div for container. // this </div> right below to be used in a main body file if the footer is not included. ?>
<?php 
$file_name = basename($_SERVER['PHP_SELF']);
if($file_name != "index.php"){
?>
</div>
<?php
}
?>

<footer>
<a href="#top" class="top">&uarr; Back to top</a>
  <br>
    <br>
    <span class="social-media">Contact us</span>
    <br>
    <br>
    <i class="fa-solid fa-envelope" class="mail-to"></i> <a href="mailto:<?php echo $site_title; ?>@gmail.com" class="mail-to-link"
   ><?php echo $site_title; ?>@gmail.com</a>
   <br>
    <br> 
   <a href="#" target=""><i class="fa-brands fa-facebook social-media-icons" title="Like us on facebook"></i></a>&nbsp;
   <a href="#" target=""><i class="fa-brands fa-twitter social-media-icons" title="Follow us on twitter"></i></a>&nbsp;
   <a href="#" target=""><i class="fa-brands fa-instagram social-media-icons" title="Follow us on Instagram"></i></a>
<br>
 <br>
<div class="nav-div">
  <a class="nav-links" href="<?php echo $home; ?>">Home</a> |
   <a class="nav-links" href="about">About us</a> |
     <!--<a class="nav-links" href="contact">Contact us</a> |-->
     <a class="nav-links" href="donate">Donate</a> | 
	    <a class="nav-links" href="privacy">Privacy policy</a> |
       <a class="nav-links" href="terms">Terms of use</a> <!-- |
	      <a class="nav-links" href="advertise">Advertise with us</a> -->
<?php 
if ($session->is_logged_in()){
?>
 | <a class="logout" href="logout" onclick="return confirm('Confirm logout')">Logout</a>
<?php
 }
?>       
</div>
   <h4>Copyright &copy; <?php echo date("Y");?> <?php echo $site_title; ?></h4>
</footer>

<?php //javascript files must also be included in the main body file if the footer is not included. ?> 

<script type="text/javascript" src="scripts/jquery.min.js"></script>
<script type="text/javascript" src="scripts/site_scripts.js?v=<?php echo filemtime("scripts/site_scripts.js"); ?>"></script>
<script src="scripts/clipboard.min.js"></script>
  </body>
</html>
<?php $db->close(); ?>
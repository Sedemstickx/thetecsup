<?php require_once "includes/initialize.php"; ?>

<?php $page_title = "".$site_title." - An easy to use tech support community for all."; ?>
<?php include("layouts/header.php"); ?>
<?php if($session->is_logged_in()){redirect_to($home);} ?>

<div class="wrap">

<!-- Welcome part -->
<div class="site-intro">
  
  <h1>Welcome to <?php echo $site_title; ?>.com</h1>

  <p>An easy to use tech support platform.</p>

<br>

  <a href="home" class="green-button"><i class="fa-brands fa-wpexplorer"></i> Explore <?php echo $site_title; ?></a>

</div>

<!-- brief description of the site -->
<div class="column-wrap"> 

<div class="container intro-center">

  <h1>What is <?php echo $site_title; ?>?</h1>
  <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Maecenas sit amet pretium urna.</p> 

</div>

</div>

<!-- ask,tip part -->
<div class="column-wrap"> 

<div class="container">

  <div class="column-2">
  
  <h1>Solve your tech issues for free, share your tech knowledge.</h1>
  <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Maecenas sit amet pretium urna.</p> 

  </div>

  <div class="column-2">
  
  <img src="images/tecsup.jpg">

  </div>

</div>

</div>

<!-- hire part -->
<div class="column-wrap"> 

<div class="container">

  <div class="column-2">
  
  <img src="images/tecsup.jpg">

  </div>

  <div class="column-2">
  
  <h1>Can't solve the issue on your own? hire IT freelancers.</h1>
  <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Maecenas sit amet pretium urna.</p> 

  </div>

</div>

</div>

<!-- 3 columns -->
<div class="column-wrap wrap-padding"> 

<h1 style="text-align:center;">So to summarize...</h1>

<div class="column-flex">

  <div class="column-3">
  
  <h2>Get free tech support(DIY)</h2>
  <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Maecenas sit amet pretium urna. 
    Vivamus venenatis velit nec neque ultricies, eget elementum magna tristique. Quisque vehicula,
   risus eget aliquam placerat,purus leo tincidunt eros, eget luctus quam orci in velit. 
   Praesent scelerisque tortor sed accumsan convallis.</p>
 </div>

 <div class="column-3">
  
  <h2>Share a tip</h2>
  <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Maecenas sit amet pretium urna. 
    Vivamus venenatis velit nec neque ultricies, eget elementum magna tristique. Quisque vehicula,
   risus eget aliquam placerat,purus leo tincidunt eros, eget luctus quam orci in velit. 
   Praesent scelerisque tortor sed accumsan convallis.</p>
 </div>

 <div class="column-3">
  
  <h2>Hire an IT freelancer</h2>
  <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Maecenas sit amet pretium urna. 
    Vivamus venenatis velit nec neque ultricies, eget elementum magna tristique. Quisque vehicula,
   risus eget aliquam placerat,purus leo tincidunt eros, eget luctus quam orci in velit. 
   Praesent scelerisque tortor sed accumsan convallis.</p>
 </div>

</div>

</div>

  <!-- end of block wrap -->
</div>

<!-- join us part -->
<div class="column-wrap faded-blue-bg">

<div class="ending-center">

<p>Experience it for yourself. Why don't you just try us?</p>

<p>
<a href="signup" class="green-button"> Signup</a> &nbsp;&nbsp; <a href="login" class="blue-button"> Login</a>
</p>

  </div>

</div>

</div>

<?php include("layouts/footer.php"); ?> 

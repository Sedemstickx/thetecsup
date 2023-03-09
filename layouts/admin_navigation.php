<div id="admin-left"> 

<center><a href="admindash"><h1><?php echo $site_title; ?></h1></a>
<br>
<h2>Menu</h2></center>


<div style="margin-top: 30px;">

<ul>

<a class="admin-options-link" href="admindash"><div class="side_menu" style="<?php echo $h_bold; ?>"> 
<li> Dashboard
  </li>   
</div></a>

<a class="admin-options-link" href="posts"><div class="side_menu" style="<?php echo $q_bold; ?>">  		
<li> Posts
 </li>
 </div></a>

<a class="admin-options-link" href="replies"><div class="side_menu" style="<?php echo $r_bold; ?>">
<li> Replies
</li>
</div></a>

<a class="admin-options-link" href="users"><div class="side_menu" style="<?php echo $u_bold; ?>">
<li> Users
</li>
</div></a>

<a class="admin-options-link" href="manage_topics"><div class="side_menu" style="<?php echo $c_bold; ?>">
<li> Topics
</li>
</div></a>
	
<a class="admin-options-link" href="reports_list"><div class="side_menu" style="<?php echo $rep_bold; ?>">    
<li>Manage reports <?php echo Report::count_not_viewed(); ?>
</li>
</div></a>

<a class="admin-options-link" href="announcements"><div class="side_menu" style="<?php echo $announce_bold; ?>">	
<li> Announcements
</li>
</div></a>

<a class="admin-options-link" href="admins"><div class="side_menu" style="<?php echo $admins_bold; ?>">    
<li> Admins
</li>
</div></a>

<a class="admin-options-link" href="logfile"><div class="side_menu" style="<?php echo $l_bold; ?>">
<li> View log file
</li>
</div></a> 

</ul>

	</div>
  </div>
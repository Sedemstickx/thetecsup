<?php
function freelancer_list($username,$pic,$freelancer,$specialties,$location,$points)
{

    $freelancer_list = '
    <div class="freelance_list">

    <div class="left-right-items no-margin">
    <div class="flex">
    <div class="profile-div"><a class="profile-link" href="'.User::profile_link($username).'"><img src="'.$pic.'" alt="image" class="profile-pic-small"></a></div> 
    <div class="name-div"><a class="profile-link" href="'.User::profile_link($username).'"><b>'.htmlentities($username).'</b></a></div>
    </div>
    <span class="points-right">'.$freelancer.' 
    </span>
    </div> 
    <br>
    <span style="color:gray;">Specialties: '.htmlentities($specialties).'</span>
    <br>
    <span style="color:gray;">Location: '.htmlentities($location).'</span> &nbsp; <span style="color:gray;">Points: '.htmlentities(number_format($points)).'</span>

    </div>';

    return  $freelancer_list;
}
?>
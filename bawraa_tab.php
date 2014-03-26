<?php

include_once('Smarty.class.php');
$main_smarty = new Smarty;

include_once('config.php');
include(mnminclude.'html1.php');
include(mnminclude.'link.php');
include(mnminclude.'smartyvariables.php');
include_once(mnminclude.'interest_member.php');
include_once(mnminclude.'interest.php');
include_once(mnminclude.'location.php');
include_once(mnminclude.'group.php');
include_once(mnminclude.'groups.php');
include_once(mnminclude.'user.php');
include_once(mnminclude.'news.php');
include_once(mnminclude.'test_brand.php');
include_once('global_variable.php');
include_once(mnminclude.'photo.php');
global $current_user,$db;

//set to true if this page has mobile template
$mobOpt=false;
//og-title
$main_smarty->assign('og_posttitle', '\'Tu Bawraa hai toh chal\' Contest');
//Site Title
$main_smarty->assign('posttitle', '\'Tu Bawraa hai toh chal\' Contest - by Shaukk - Bawraas with Mood Indigo\'13\'');
//met description
$main_smarty->assign('description', 'Calling all you passionate souls out there - \'Tu Bawraa hai toh chal\' Contest: In search of the next Bawraa - Someone who passionately pursues unconventional interests. 

Apply on http://shaukk.com/bawraas/ 

By Shaukk-Bawraas in association with Mood Indigo\'13.');
//og-description
$main_smarty->assign('og_content', ' ');
//og-image
$main_smarty->assign('postImage', 'http://shaukk.com/images/logos.png');

$vars = '';

check_actions('index_top', $vars);


//$main_smarty->assign('Avatar_ImgLarge', get_avatar('250', "", "", "", $user->id));
$main_smarty->assign('bawraa_user', $bawraa);
$main_smarty->assign('tpl_header', $the_template . '/header');
            $main_smarty->assign('tpl_center', $the_template . '/bawraa_tab');

            $main_smarty->display($the_template . '/pligg.tpl');
			


?>
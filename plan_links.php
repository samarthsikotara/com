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
include_once('global_variable.php');

//echo $current_user->user_id;
//set to true if this page has mobile template
$mobOpt=false;
//og-title
$main_smarty->assign('og_posttitle', 'Shaukk Plan Links');
//Site Title
$main_smarty->assign('posttitle', 'Shaukk Plan Links');
//met description
$main_smarty->assign('description', '');
//og-description
$main_smarty->assign('og_content', '');
//og-image
$main_smarty->assign('postImage', 'http://shaukk.com/avatars/user_uploaded/1_original.jpg');

$i=0;
//location-wise plan fetch
$results1= getMumbaiLocationDet();
//print_r($results1);
$i=0;
foreach($results1 as $plan1){
	$plans1[$i]['title']=$plan1['location_name'];
	$plans1[$i]['url']=getmyurl('locations', $plan1['location_url_title'], "events");
	$i++;
}

//print_r($plans1);
$main_smarty->assign('locations', $plans1);
$main_smarty->assign('loc1', $ll);
$main_smarty->assign('result', $plans);
$main_smarty->assign('tpl_header', $the_template . '/header');
$main_smarty->assign('tpl_center', $the_template . '/events_location');
$main_smarty->display($the_template . '/pligg.tpl');


?>
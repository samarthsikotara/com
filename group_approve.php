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
include_once(mnminclude.'facebook.php');

global $current_user,$db;


//set to true if this page has mobile template
$mobOpt=false;
//og-title
$main_smarty->assign('og_posttitle', 'Shaukk blog');
//Site Title
$main_smarty->assign('posttitle', 'blog');
//met description
$main_smarty->assign('description', '');
//og-description
$main_smarty->assign('og_content', '');
//og-image
$main_smarty->assign('postImage', 'http://shaukk.com/avatars/user_uploaded/1_original.jpg');

$sql="SELECT * FROM shgroup_approve WHERE status!='2'";
$result=$db->get_results($sql, ARRAY_A);

//print_r($result);

$group_creator=array();
$i=0;

	foreach($result as $user){
	
		$group_creator[$i]['id'] = $user['id'];
		$group_creator[$i]['user_id'] = $user['user_id'];
		$group_creator[$i]['interest_id'] = $user['interest_id'];
		$group_creator[$i]['name'] = $user['name'];
		$group_creator[$i]['desc'] = $user['description'];
		$group_creator[$i]['date'] = $user['date'];
		$group_creator[$i]['lat'] = $user['latitude'];
		$group_creator[$i]['lng'] = $user['longitude'];
		$i++;
		
	}
	
$main_smarty->assign('creator', $group_creator);
$main_smarty->assign('tpl_center', $the_template . '/group_approve');
$main_smarty->display($the_template . '/pligg3.tpl');
?>
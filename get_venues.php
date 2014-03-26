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
global $current_user,$db;


$sql="SELECT * FROM `shvenues` ORDER BY `shvenues`.`name`  ASC";
$result=$db->get_results($sql, ARRAY_A);
//print_r($result);

$event=array();
$i=0;

foreach($result as $user){

	$event[$i]['id']=$user['id'];
	$event[$i]['name']=$user['name'];
	$event[$i]['address']=$user['address'];
	$event[$i]['desc']=$user['description'];
	$event[$i]['lat']=$user['latitude'];
	$event[$i]['lon']=$user['longitude'];
	$event[$i]['source']=$user['source'];
	$event[$i]['status']=$user['status'];
	$i++;
}
//print_r($bawraa);



$main_smarty->assign('event', $event);
$main_smarty->assign('tpl_center', $the_template . '/get_venue');
$main_smarty->display($the_template . '/pligg3.tpl');

?>
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


$id = $_GET['id'];

$sql="SELECT * FROM `shgroup_approve` WHERE id = ".$id." ";
$result=$db->get_results($sql, ARRAY_A);
//print_r($result);
$group_creator=array();
$grp=array();
$i=0;
$j=0;
	foreach($result as $user){
	
		$group_creator[$i]['id'] = $user['id'];
		$group_creator[$i]['user_id'] = $user['user_id'];
		$sql1="SELECT user_names FROM `shusers` WHERE user_id = ".$group_creator[$i]['user_id']."";
		//echo $sql1;
		$name=$db->get_var($sql1);
		$group_creator[$i]['interest_id'] = $user['interest_id'];
		$sql2="SELECT interest_name FROM `shinterests` WHERE interest_id = ".$group_creator[$i]['interest_id']."";
		$interest=$db->get_var($sql2);
		$group_creator[$i]['interest_grp_name'] = $user['interest_name'];
		$group_creator[$i]['name'] = $user['name'];
		$group_creator[$i]['desc'] = $user['description'];
		$group_creator[$i]['date'] = $user['date'];
		$group_creator[$i]['lat'] = $user['latitude'];
		$group_creator[$i]['lng'] = $user['longitude'];
		$group_creator[$i]['loc'] = $user['location'];
		$group_creator[$i]['user_loc'] = $user['user_location'];
		$group_creator[$i]['status'] = $user['status'];
		$sql2 = "SELECT (location_lat1 + location_lat2)/2 as lat,(location_lon1 + location_lon2)/2 as lng FROM `shlocations` WHERE  (location_lat1 + location_lat2)/2 BETWEEN (".$group_creator[$i]['lat']." - 0.05) AND (".$group_creator[$i]['lat']." + 0.05) ";	
//echo $sql2;
$result1=$db->get_results($sql2, ARRAY_A);
//print_r($result1);

		foreach($result1 as $res){
		
			$grp[$j]['lat1'] = $res['lat'];
			$grp[$j]['lng1'] = $res['lng'];
			$j++;
			
		}
		$main_smarty->assign('latlng', json_encode($grp));
		$i++;

	}
	
		
	




if(isset($_POST['save']) && $_POST['save']=='Submit'){

	
	$id=sanitize(strip_tags($_POST['id']));
	
	$sql = "SELECT * FROM `shgroup_approve` WHERE id = ".$id."";
    $results = $db->get_row($sql);
	
	$interest_grp_name = sanitize(strip_tags($_POST['interest_grp_name']));
	$name=sanitize(strip_tags($_POST['name']));
	$group_url=makeGroupUrlFriendly($name);
	$desc=sanitize(strip_tags($_POST['desc']));
	$remove_notif=sanitize(strip_tags($_POST['remove_notif']));
	$location=sanitize(strip_tags($_POST['details']));
	$location_id=sanitize(strip_tags($_POST['location_id']));
	$interest_id=sanitize(strip_tags($_POST['interest_id']));
	$location_name=sanitize(strip_tags($_POST['user_loc']));
	$loc_lat=sanitize(strip_tags($_POST['user_loc1']));
	$loc_lng=sanitize(strip_tags($_POST['user_loc2']));
	$status = sanitize(strip_tags($_POST['status']));
		
	if(!is_null($results->id)){
		
		/*$sql1=" REPLACE INTO `shgroups`(group_creator,group_status,group_date,group_name,group_description,group_privacy,group_field3,group_field4,group_field5,group_field6,group_notify_email)VALUES(".$val.",'disable',NOW(),'".$name."','".$desc."','public',".$interest_id.",".$location_id.",".$id.",'".$group_url."',0) ";
		*/
		$group = new groups();
		$group->group_name = $name;
		$group->group_title_url = $group_url;
		$group->group_status = 'disable';
		$group->group_desc = $desc;
		$group->group_privacy = 'public';
		$group->group_interest = $interest_id;
		$group->group_location = $location_id;
		$group->group_photo = $_FILES['image_file11'];
		//if($group->group_photo['size']>((1024*1024)*10)){$group->group_photo ="";}
		$group->createGroup();
		//$group->uploadAvatar();
		
		
		$sql4="UPDATE `shgroup_approve` SET status = ".$status." WHERE id = ".$id." ";
		echo $sql4;
		$db->query($sql4);
		}
	
}

$main_smarty->assign('name', $name);
$main_smarty->assign('interest', $interest);
$main_smarty->assign('creator', $group_creator);
$main_smarty->assign('tpl_center', $the_template . '/group_approve_link');
$main_smarty->display($the_template . '/pligg.tpl');




?>
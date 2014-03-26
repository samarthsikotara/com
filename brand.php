<?php
// The source code packaged with this file is Free Software, Copyright (C) 2005 by
// Ricardo Galli <gallir at uib dot es>.
// It's licensed under the AFFERO GENERAL PUBLIC LICENSE unless stated otherwise.
// You can get copies of the licenses here:
// 		http://www.affero.org/oagpl.html
// AFFERO GENERAL PUBLIC LICENSE is also included in the file called "COPYING".

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


//set to true if this page has mobile template
$mobOpt=false;
//og-title
$main_smarty->assign('og_posttitle', 'Shaukk Brands');
//Site Title
$main_smarty->assign('posttitle', 'Brands');
//met description
$main_smarty->assign('description', '');
//og-description
$main_smarty->assign('og_content', '');
//og-image
$main_smarty->assign('postImage', 'http://shaukk.com/avatars/user_uploaded/1_original.jpg');

//when form is submitted


if(isset($_POST['submit']) && $_POST['submit']=='enter'){
	//print_r($_POST);
	
	$plan_type=$_POST['plan_type'];
	
	$comments=$_POST['comments'];
	$sql="INSERT INTO ".table_brands." (user_id,interest_id,Plan_Type) VALUES"; 
	foreach($plan_type as $key=>$value){
		foreach ($value as $type){
			$sql.=" (3, ".$key.", '".$type."'),";
		}
	}
	$sql=substr($sql, 0, -1);
	//echo $sql;

    $results=$db->query($sql);
	
	//die();
	/*
	$comments=$_POST['comments'];
	$sql1="INSERT INTO ".table_brands." (user_id,interest_id,Comments) VALUES"; 
	foreach($comments as $key=>$value){
		foreach ($value as $comments){
			$sql1.=" (5, ".$key.", '".$comments."'),";
		}
	}
	$sql1=substr($sql1, 0, -1);
	echo $sql1;
	$res=$db->query($sql1); */
}



//sql queries here 
$sql="SELECT `interest_type`, `interest_name`, ".table_interests.".`interest_id` FROM ".table_interests." INNER JOIN ".table_interests_type." ON ".table_interests.".interest_id=".table_interests_type.".interest_id INNER JOIN ".table_interest_member." ON ".table_interests.".interest_id=".table_interest_member.".interest_id  WHERE `user_id`=3 AND interest_flag= 'Type'";


$interests = $db->get_results($sql, ARRAY_A);
$i=-1;$j=-1;
$b_interest=array();
foreach($interests as $interest){
	if(!in_array($interest['interest_id'], $b_interest[$i])){
		$i++;
		$j=0;
	}else{
		$j++;
	}
		$b_interest[$i]['id']=$interest['interest_id'];
		$b_interest[$i]['name']=$interest['interest_name'];
	
		$b_interest[$i]['type'][$j]=$interest['interest_type'];
}
//print_r($b_interest);
//die;
$main_smarty->assign('interests', $b_interest);


$main_smarty->assign('tpl_header', $the_template . '/header');
$main_smarty->assign('tpl_center', $the_template . '/brand');

$main_smarty->display($the_template . '/pligg.tpl');

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

//get the latitude and Longitudes
$url=$_GET['location'];
$sql1="SELECT * FROM shlocations WHERE location_url_title='$url'";
$location=$db->get_results($sql1,ARRAY_A);

$i=0;
$loc=array();
foreach($location as $loc){

$loct[$i]['id']=$loc['location_id'];
$loct[$i]['lat1']=$loc['location_lat1'];
$loct[$i]['lat2']=$loc['location_lat2'];
$loct[$i]['lon1']=$loc['location_lon1'];
$loct[$i]['lon2']=$loc['location_lon2'];
$i++;

}
$locc=$loct[0]['id'];
$loccc1=$loc['location_lat1'];
$loccc2=$loc['location_lat2'];
$loccc3=$loc['location_lon1'];
$loccc4=$loc['location_lon2'];

$sql="SELECT * FROM ".table_links." WHERE (link_field4 BETWEEN '$loccc2' AND '$loccc1') AND (link_field5 BETWEEN '$loccc4' AND '$loccc3') ORDER BY link_title ASC";
$results=$db->get_results($sql,ARRAY_A);



//plan (Events) display with links
$i=0;
$plans=array();
foreach($results as $plan){
	$plans[$i]['link_title']=$plan['link_title'];
	$plans[$i]['url']=getmyurl('events', $plan['link_title_url']);
	$i++;

}
$main_smarty->assign('result', $plans);

$main_smarty->assign('tpl_header', $the_template . '/header');
$main_smarty->assign('tpl_center', $the_template . '/events');
$main_smarty->display($the_template . '/pligg.tpl');
?>
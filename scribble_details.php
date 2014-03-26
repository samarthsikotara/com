<?php
// The source code packaged with this file is Free Software, Copyright (C) 2005 by
// Ricardo Galli <gallir at uib dot es>.
// It's licensed under the AFFERO GENERAL PUBLIC LICENSE unless stated otherwise.
// You can get copies of the licenses here:
// 		http://www.affero.org/oagpl.html
// AFFERO GENERAL PUBLIC LICENSE is also included in the file called "COPYING".

include_once('Smarty.class.php');
$main_smarty = new Smarty;

include('config.php');
include(mnminclude.'html1.php');
include(mnminclude.'scribble.php');
include(mnminclude.'group.php');
include(mnminclude.'smartyvariables.php');
include_once(mnminclude.'user.php');
include_once(mnminclude.'user_fetch.php');
include_once(mnminclude.'bookmark.php');

$requestID = isset($_GET['id']) && is_numeric($_GET['id']) ? $_GET['id'] : 0; 

if(isset($_GET['title']) && sanitize($_GET['title'], 3) != ''){$requestTitle = sanitize($_GET['title'], 3);}
// if we're using "Friendly URL's for categories"
if(isset($_GET['category']) && sanitize($_GET['category'], 3) != ''){$thecat = $db->get_var("SELECT category_id FROM " . table_categories . " WHERE `category_safe_name` = '".$db->escape(urlencode(sanitize($_GET['category'], 3)))."';");}
 /*
if($requestID > 0 && enable_friendly_urls == true){
	// if we're using friendly urls, don't call /story.php?id=XX  or /story/XX/
	// this is to prevent google from thinking it's spam
	// more work needs to be done on this

	$link = new Link;
	$link->id=$requestID;
	if($link->read() == false || ($thecat>0 && $link->category!=$thecat)){
		header("Location: $my_pligg_base/404error.php");
//		$main_smarty->assign('tpl_center', '404error');
//		$main_smarty->display($the_template . '/pligg.tpl');		
		die();
	}

	$url = getmyurl("storyURL", $link->category_safe_name($link->category), urlencode($link->title_url), $link->id);

	Header( "HTTP/1.1 301 Moved Permanently" );
	Header( "Location: " . $url );
	
	die();
}

  */

// if we're using "Friendly URL's for stories"

if(is_numeric($requestID)) {
	$id = $requestID;
	$scribble = new Scribble();
	$scribble->id=$requestID;
	if(!$scribble->read() || ($thecat>0 && $scribble->category!=$thecat) && !checklevel('god') && !checklevel('admin')){

		// check for redirects
		include(mnminclude.'redirector.php');
		$x = new redirector($_SERVER['REQUEST_URI']);

		header("Location: $my_pligg_base/404error.php");
//		$main_smarty->assign('tpl_center', '404error');
//		$main_smarty->display($the_template . '/pligg.tpl');		
		die();
	}

	// Hide private group stories
    $scribble->details=true;
    $scribble->print_summary('full', false, 'scribble_details_center.tpl');




} else {

	// check for redirects
	include(mnminclude.'redirector.php');
	$x = new redirector($_SERVER['REQUEST_URI']);
	
	header("Location: $my_pligg_base/404error.php");
//	$main_smarty->assign('tpl_center', '404error');
//	$main_smarty->display($the_template . '/pligg.tpl');		
	die();
}




function count_all_votes($id, $value="> 0"){
    require_once(mnminclude.'votes.php');

    $vote = new Vote;
    $vote->type='scribble';
    $vote->link=$id;
    return $vote->count_all($value);
}

?>


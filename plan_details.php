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
include(mnminclude.'group.php');
include(mnminclude.'smartyvariables.php');
include_once(mnminclude.'user.php');
include_once(mnminclude.'user_fetch.php');
include_once(mnminclude.'plan_members.php');
include_once(mnminclude.'bookmark.php');
if($_REQUEST['page']=="plan")
{
$main_smarty->assign('cross', "true");
}else{
$main_smarty->assign('cross',"false");
}

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
if(isset($requestTitle)){$requestID = $db->get_var($sql="SELECT link_id FROM " . table_links . " WHERE `link_title_url` = '".$db->escape(sanitize($requestTitle,4))."';");}

if(is_numeric($requestID)) {
	$id = $requestID;
	$link = new Link;
	$link->id=$requestID;
	if(!$link->read() || ($thecat>0 && $link->category!=$thecat) || (($link->status=='spam' || $link->status=='discard') && !checklevel('god') && !checklevel('admin'))){

		// check for redirects
		include(mnminclude.'redirector.php');
		$x = new redirector($_SERVER['REQUEST_URI']);

		header("Location: $my_pligg_base/404error.php");
        //$main_smarty->assign('tpl_center', '404error');
        //$main_smarty->display($the_template . '/pligg.tpl');
		die();
	}

	// Hide private group stories
	if ($link->link_group_id)
	{
	    $privacy = $db->get_var("SELECT group_privacy FROM " . table_groups . " WHERE group_id = {$link->link_group_id}");
	    if ($privacy == 'private' && !isMember($link->link_group_id))
	    {
		die('Access denied');
	    }
	}

	if(isset($_POST['process']) && sanitize($_POST['process'], 3) != ''){
		if (sanitize($_POST['process'], 3)=='newcomment') {
			check_referrer();
		
			$vars = array('user_id' => $link->author,'link_id' => $link->id);
			check_actions('comment_subscription', $vars);
			insert_comment();
		}
	}

	require_once(mnminclude.'check_behind_proxy.php');

	// Set globals
	$globals['link_id']=$link->id;

	$globals['category_id']=$link->category;
	$globals['category_name']=$link->category_name();
	$globals['category_url']=$link->category_safe_name();
	$vars = '';
	check_actions('story_top', $vars);
    $main_smarty->assign('vote_count', count_all_votes($link->id));
	$main_smarty->assign('link_submitter', $link->username());
	$main_smarty->assign('interest_id', $link->link_field1);
	$main_smarty->assign('content', $link->link_summary);

	// setup breadcrumbs and page title
	$main_smarty->assign('posttitle', $link->title);
	$navwhere['text1'] = $globals['category_name'];
	$navwhere['link1'] = getmyurl('maincategory', $globals['category_url']);
	$navwhere['text2'] = $link->title;
	$navwhere['link2'] = getmyurl('storycattitle', $globals['category_url'], urlencode($link->title_url));
	$main_smarty->assign('navbar_where', $navwhere);
	$main_smarty->assign('request_category', $globals['category_url']);
	$main_smarty->assign('request_category_name', $globals['category_name']);

	// for the comment form
	$randkey = rand(1000000,100000000);
	$main_smarty->assign('randkey', $randkey);
	$main_smarty->assign('link_id', $link->id);
	$main_smarty->assign('user_id', $current_user->user_id);
	$main_smarty->assign('randmd5', md5($current_user->user_id.$randkey));

    $main_smarty->assign('status', $link->canJoin());

	
	if(!$current_user->authenticated){
		$vars = '';
		check_actions('anonymous_user_id', $vars);
	}
    if(hasJoined($link->id)){ $hasjoined=1;}
		else $hasjoined=0;
	$main_smarty->assign('hasjoined', $hasjoined);
	// for login to comment
	$main_smarty->assign('register_url', getmyurl("register", ''));
	$main_smarty->assign('login_url', getmyurl("login", $_SERVER['REQUEST_URI']));

	// for show who voted
	$main_smarty->assign('user_url', getmyurl('userblank', ""));
	$main_smarty->assign('voter', who_voted($id, 'small'));

	// misc smarty
	$main_smarty->assign('Enable_Comment_Voting', Enable_Comment_Voting);
	$main_smarty->assign('enable_show_last_visit', enable_show_last_visit);
	$main_smarty->assign('Spell_Checker',Spell_Checker);
	$main_smarty->assign('UseAvatars', do_we_use_avatars());
	$main_smarty->assign('related_title_url', getmyurl('storytitle', ""));
	$main_smarty->assign('related_story', related_stories($id, $link->tags, $link->category));
	$main_smarty->assign('interest_image',getInterestImage($link->link_field1, "85"));

	// meta tags
	$main_smarty->assign('meta_description', strip_tags($link->truncate_content()));
	$main_smarty->assign('meta_keywords', $link->tags);
	
	//sidebar
	$main_smarty = do_sidebar($main_smarty);	
    if(isBookmarked($link->id, 'plans')>0) $isbookmarked=1;
        else $isbookmarked=0;
	// pagename
	define('pagename', 'story'); 
	$main_smarty->assign('pagename', pagename);

	$main_smarty->assign('the_story', $link->print_summary('full', true));
	$main_smarty->assign('the_comments', $link->get_comments(true));
    $main_smarty->assign('plan_members', getPlanMembersCount($link->id));
    $main_smarty->assign('isbookmarked', $isbookmarked);
    $main_smarty->assign('plan_seat_remaining', (($link->link_field8)-(getPlanMembersCount($link->id))));
	$main_smarty->assign('member', get_members());
	$main_smarty->assign('liker', $link->get_likers());
    if($link->link_field12!="" && $link->link_field12!=null){
	    $main_smarty->assign('prom_link',urldecode($link->link_field12));
    }
    if($link->link_field13!="" && $link->link_field13!=null){
	    $main_smarty->assign('prom_banner',my_pligg_base.'/images/banner/'.urldecode($link->link_field13));
    }
	$main_smarty->assign('comments_count', $link->comments);
	$main_smarty->assign('plan_closing_time', $link->link_field6);
	$main_smarty->assign('plan_privacy', $link->link_field11);
	$main_smarty->assign('url', $link->url);
	$main_smarty->assign('enc_url', urlencode($link->url));

	if($current_user->authenticated != TRUE){
		$vars = '';
		check_actions('register_showform', $vars);
	}

	$main_smarty->assign('story_comment_count', $link->comments());
	$main_smarty->assign('URL_rss_page', getmyurl('storyrss', isset($requestTitle) ? $requestTitle : urlencode($link->title_url), $link->category_safe_name($link->category)));

	$main_smarty->assign('tpl_center', $the_template . '/plan_details_center');
	$main_smarty->display($the_template . '/pligg_content.tpl');
} else {

	// check for redirects
	include(mnminclude.'redirector.php');
	$x = new redirector($_SERVER['REQUEST_URI']);
	
	header("Location: $my_pligg_base/404error.php");
//	$main_smarty->assign('tpl_center', '404error');
//	$main_smarty->display($the_template . '/pligg.tpl');		
	die();
}

function get_members(){
    Global $db, $main_smarty, $current_user,$link;
    $sql="SELECT * FROM ".table_plan_members." WHERE `plan_id`=".$link->id." AND `status`='Joined'";
    // check privacy here
    $sql=$sql." AND `privacy`='public'";

    $members=$db->get_results($sql);
    $i=0;
    foreach($members as $member){
        $array[$i]['user_id']=$member->user_id;
        $array[$i]['name']=getUserDetailsSmall($member->user_id)->user_names;
        $array[$i]['avatar']=get_avatar('small', "", getUserDetailsSmall($member->user_id)->user_login, "");
        $array[$i]['url']=getmyFullurl('profileId', $member->user_id, '', '');
        $i++;
    }
    return $array;
}





function count_all_votes($id, $value="> 0"){
    require_once(mnminclude.'votes.php');

    $vote = new Vote;
    $vote->type='links';
    $vote->link=$id;
    return $vote->count_all($value);
}

?>


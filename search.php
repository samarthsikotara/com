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
include_once(mnminclude.'html1.php');
include_once(mnminclude.'link.php');
include(mnminclude.'interest.php');
include(mnminclude.'bookmark.php');
include(mnminclude.'interest_member.php');
include(mnminclude.'friends.php');
include(mnminclude.'location.php');
include_once(mnminclude.'photo.php');
include_once(mnminclude.'groups.php');
include_once(mnminclude.'group.php');
include('global_variable.php');
$mobOpt=true;
include(mnminclude.'smartyvariables.php');
include(mnminclude.'plan_members.php');
include_once(mnminclude.'user_fetch.php');
define('pagename', 'search');

if(!isset($_REQUEST['searchQ']) || $_REQUEST['searchQ']==""){
    header("Location: ".getmyurl('error'));
}
$query=sanitize($_REQUEST['searchQ'], 2);

$mobOpt=true;
//og-title
$main_smarty->assign('og_posttitle', 'Search Results for '.$query);
$main_smarty->assign('search_query', $query);
//Site Title
$main_smarty->assign('posttitle', 'Search Results for '.$query);
//met description
$main_smarty->assign('description', 'Search Results for '.$query);
//og-description
$main_smarty->assign('og_content', 'Search Results for '.$query);
//og-image
$main_smarty->assign('postImage', 'http://shaukk.com/avatars/user_uploaded/1_original.jpg');

// show the template
$main_smarty->assign('tpl_center', $the_template . '/search_center');
$main_smarty->display($the_template . '/pligg.tpl');
?>

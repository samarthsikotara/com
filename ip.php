<?php
// The source code packaged with this file is Free Software, Copyright (C) 2005 by
// Ricardo Galli <gallir at uib dot es>.
// It's licensed under the AFFERO GENERAL PUBLIC LICENSE unless stated otherwise.
// You can get copies of the licenses here:
// 		http://www.affero.org/oagpl.html
// AFFERO GENERAL PUBLIC LICENSE is also included in the file called "COPYING".
error_reporting();
include_once('Smarty.class.php');
$main_smarty = new Smarty;

include('config.php');
include(mnminclude.'html1.php');
include(mnminclude.'link.php');

include(mnminclude.'tags.php');
include(mnminclude.'user.php');
include(mnminclude.'csrf.php');
include(mnminclude.'friend.php');
include(mnminclude.'smartyvariables.php');
// sidebar
$main_smarty = do_sidebar($main_smarty);

include('global_variable.php');
// module system hook



$vars = '';
check_actions('index_top', $vars);


$main_smarty = do_sidebar($main_smarty);

$canIhaveAccess = 0;
$canIhaveAccess = $canIhaveAccess + checklevel('god');
$canIhaveAccess = $canIhaveAccess + checklevel('admin');

// If not logged in, redirect to the index page
if($current_user->user_id && $current_user->authenticated){
    $main_smarty->assign('isGuest','false');
}



  define('pagename', 'terms');
$main_smarty->assign('pagename',pagename);
$main_smarty->assign('tpl_center', $the_template . '/ip_center');
$main_smarty->display($the_template . '/pligg3.tpl');

 

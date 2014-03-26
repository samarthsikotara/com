<?php
// The source code packaged with this file is Free Software, Copyright (C) 2005 by
// Ricardo Galli <gallir at uib dot es>.
// It's licensed under the AFFERO GENERAL PUBLIC LICENSE unless stated otherwise.
// You can get copies of the licenses here:
// http://www.affero.org/oagpl.html
// AFFERO GENERAL PUBLIC LICENSE is also included in the file called "COPYING".

include_once('Smarty.class.php');
$main_smarty = new Smarty;

include_once('config.php');
include_once(mnminclude.'html1.php');
include_once(mnminclude.'link.php');
include_once(mnminclude.'location.php');
include_once(mnminclude.'interest_member.php');
include_once(mnminclude.'group.php');
include_once(mnminclude.'groups.php');
include_once('global_variable.php');
$mobOpt=true;
include_once(mnminclude.'smartyvariables.php');


$vars = '';
check_actions('404Error', $vars);

define('pagename', '404'); 
$main_smarty->assign('pagename', pagename);
$main_smarty->assign('guestPage', 'true');
// sidebar
$main_smarty = do_sidebar($main_smarty);
// show the template
header( "HTTP/1.1 404 Not Found" );
$main_smarty->assign('og_posttitle', 'Error - 404');
//Site Title
$main_smarty->assign('posttitle', 'Error - 404');
//met description
$main_smarty->assign('description', "The page you are looking for doesn't exist! You may have clicked an expired link or you have not enough permission to access this page or mistyped the address.");
$main_smarty->assign('metaDescription', "The page you are looking for doesn't exist! You may have clicked an expired link or you have not enough permission to access this page or mistyped the address.");
//og-description
//og-image
$main_smarty->assign('postImage', 'http://shaukk.com/avatars/user_uploaded/1_original.jpg');


$main_smarty->assign('tpl_center', '404error');

if(isset($_REQUEST['error'])){
    $main_smarty->assign('error', "custom");
    $main_smarty->assign('errorMsg', $main_smarty->get_config_vars('PLIGG_Visual_'.$_REQUEST['error']));
}
$main_smarty->display($the_template . '/pligg.tpl');
exit;
?>

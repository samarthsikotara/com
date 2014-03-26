<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Swaroop
 * Date: 2/23/13
 * Time: 2:51 PM
 * To change this template use File | Settings | File Templates.
 */
 

include_once('Smarty.class.php');
$main_smarty = new Smarty;

include_once('config.php');
include(mnminclude.'html1.php');
include(mnminclude.'link.php');
include(mnminclude.'tags.php');
include(mnminclude.'user.php');
include(mnminclude.'user_fetch.php');
include(mnminclude.'csrf.php');
include(mnminclude.'interest.php');
include(mnminclude.'location.php');
include(mnminclude.'interest_member.php');
include(mnminclude.'smartyvariables.php');
include_once(mnminclude."friends.php");
include(mnminclude."group.php");
include(mnminclude."photo.php");
include('global_variable.php');

define('pagename', "creategroup");


$main_smarty->assign('tpl_center', $the_template . '/creategroup_center');
$main_smarty->display($the_template . '/pligg.tpl');
?>
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
include_once('global_variable.php');
include_once(mnminclude.'generateHtml.php');


$sql="SELECT * FROM `shlinks` WHERE link_field6 BETWEEN '2013-12-25 00:00:00' AND '2014-01-01 00:00:00' AND link_field14 BETWEEN '2013-12-25 00:00:00' AND '2014-01-01 00:00:00' ORDER BY link_id";

//$result=$db->get_results($sql, ARRAY_A);
$result=$db->get_results($sql, ARRAY_A);
print_r($result);

echo $sql;

$main_smarty->assign('tpl_header', $the_template . '/header');
$main_smarty->assign('tpl_center', $the_template . '/events_tab_content');
$main_smarty->display($the_template . '/pligg.tpl');

?>
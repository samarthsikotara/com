<?php

include_once('Smarty.class.php');
$main_smarty = new Smarty;

include('config.php');
include(mnminclude.'html1.php');
include(mnminclude.'link.php');
include(mnminclude.'tags.php');
include(mnminclude.'search.php');
include(mnminclude.'plan_fetch.php');
include(mnminclude.'user_fetch.php');
include(mnminclude.'smartyvariables.php');
include(mnminclude.'bookmark.php');

// sidebar
$main_smarty = do_sidebar($main_smarty);

//the user must be logged in to view this page
//force_authentication();
// pagename
define('pagename', 'interest');
$main_smarty->assign('pagename', pagename);

$bookmark=get_bookmarks("plans");

$i=0;
foreach($bookmark as $plan_id){
   $plans=getPlansById($plan_id->bookmark_type_id,"abc");
    //print_r($plans);
    foreach($plans as $item){
        $array[$i]['author']=$item->user_names;
        $array[$i]['date']=$item->link_field2;
        $array[$i]['place']=$item->link_field3;
        $array[$i]['members_count']=5;
        $array[$i]['title']=$item->link_title;
    }
    $i++;
}
//print_r($array);
$main_smarty->assign('bookmarks', $array);

// show the template
$main_smarty->assign('tpl_center', $the_template . '/bookmark_center');
$main_smarty->display($the_template . '/pligg.tpl');

?>


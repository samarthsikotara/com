<?php

include_once('Smarty.class.php');
$main_smarty = new Smarty;

include('config.php');
include(mnminclude.'html1.php');
include(mnminclude.'link.php');
include(mnminclude.'tags.php');
include(mnminclude.'search.php');
include(mnminclude.'interest.php');
include(mnminclude.'plan_fetch.php');
include(mnminclude.'user_fetch.php');
include_once('global_variable.php');
$mobOpt=true;
include(mnminclude.'smartyvariables.php');
include(mnminclude.'bookmark.php');
include(mnminclude.'plan_members.php');

// sidebar
$main_smarty = do_sidebar($main_smarty);

//the user must be logged in to view this page
//force_authentication();
// pagename
define('pagename', 'notifications');
$main_smarty->assign('pagename', pagename);

if(isset($_GET['type']) && $_GET['type']){
    if($_GET['type']=="plans"){
        $bookmark=get_bookmarks("plans");
        $i=0;
        foreach($bookmark as $plan_id){
            $item=getPlansById($plan_id->bookmark_type_id,"abc");
            if(hasJoined($plan_id->bookmark_type_id)){ $hasjoined=1;}
            else{ $hasjoined=0;}
           // print_r($item);

                $array[$i]['author']=$item->user_names;
                $array[$i]['date']=date('d-m-Y', strtotime($item->link_field2));
                $array[$i]['time']=date('h:i A', strtotime($item->link_field2));
                $array[$i]['place']=$item->link_field3;
                $array[$i]['members_count']=5;
                $array[$i]['title']=$item->link_title;
                $array[$i]['hasjoined']=$hasjoined;

            $i++;
        }
    }

   else if($_GET['type']=="interest"){
        $bookmark=get_bookmarks("interest");
         $i=0;

        foreach($bookmark as $interest_id){
            $array[$i]=getInterestOtherDetails($interest_id->bookmark_type_id,"abc");
            //print_r($array);
            $i++;

        }
    }
}
//print_r($array);

$main_smarty->assign('array', $array);
$main_smarty->assign('type', $_GET['type']);


// show the template
$main_smarty->assign('tpl_center', $the_template . '/notification_page_center');
$main_smarty->display($the_template . '/pligg.tpl');

?>


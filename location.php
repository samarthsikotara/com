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
include(mnminclude.'location.php');
include(mnminclude.'interest_member.php');

// sidebar
$main_smarty = do_sidebar($main_smarty);

//the user must be logged in to view this page
//force_authentication();

// pagename
define('pagename', 'location');
$main_smarty->assign('pagename', pagename);


if(isset($_GET['locationId'])){
    if(!is_numeric($_GET['locationId'])) showError('undefined');
        if(!locationExists($_GET['locationId'])) showError('notExists');
    else $main_smarty->assign('locationId', $_GET['locationId']);
}

// check if the author of the plan is enabled
//check if the nothing is passed through GET

if(isset($_GET['view'])){
    if($_GET['view']=='plans'){
    $main_smarty->assign('view', "plans");
    $plans=getPlansByLocation($_GET['locationId'], "abc");
          $i=0;
        foreach($plans as $item){
            $plan[$i]['id']=$item->link_id;
            $plan[$i]['title']=$item->link_title;
            $plan[$i]['authorName']=getUserDetails($item->link_author)->user_names;
            $plan[$i]['authorPic']=get_avatar("large", "","","", $item->link_author);
            $plan[$i]['votes']=$item->link_votes;
            $plan[$i]['date']=$item->link_date;
            $plan[$i]['category']=get_category_name($item->link_category);
            $plan[$i]['content']=$item->link_content;
            $plan[$i]['plan_date']=$item->link_field2;
            $plan[$i]['address']=$item->link_field3;
            $i++;
        }
        //print_r($plan);


    $main_smarty->assign('plans', $plan);






    }
    if($_GET['view']=='info'){
        $main_smarty->assign('view', "info");
        $location=getLocationDetails($_GET['locationId']);
        $info['name']=$location->location_name;
        $info['photo']=my_base_url . my_pligg_base . '/avatars/location_uploaded/'.$location->location_id.".jpg";
        if(!file_exists($info['photo'])) $info['photo']=my_base_url . my_pligg_base . '/avatars/location_uploaded/default.gif';
        $info['lat1']=$location->lat1;
        $info['lat2']=$location->lat2;
        $info['lng1']=$location->lng1;
        $info['lng2']=$location->lng2;
       // print_r($info);
        $main_smarty->assign('locations', $info);

    }
}


// show the template
$main_smarty->assign('tpl_center', $the_template . '/location_center');
$main_smarty->display($the_template . '/pligg.tpl');

?>
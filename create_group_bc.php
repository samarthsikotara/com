<?php
include_once('Smarty.class.php');
$main_smarty = new Smarty;

include_once('config.php');
include(mnminclude.'html1.php');
include(mnminclude.'group.php');
include(mnminclude.'groups.php');
include(mnminclude.'tags.php');
include_once(mnminclude.'interest_member.php');
include_once(mnminclude.'interest.php');
include(mnminclude.'location.php');
include('global_variable.php');
$mobOpt=true;
include(mnminclude.'smartyvariables.php');

if($isMobile){$the_template="mobile";}

define('pagename', 'create_group');
$main_smarty->assign('pagename', pagename);

if(!$current_user->authenticated) header("Location: ".my_pligg_base."/login.php?return=/create_group.php");

if(isset($_POST) && !empty($_POST)){
	
	$main_smarty->assign('form_submitted','true');
    $groups= new groups();
    $groups->group_name = sanitize(trim($_POST['group_nam']), 3);
    $groups->group_photo = $_FILES['image_file'];
    if($groups->group_photo['size']>((1024*1024)*10)){$groups->group_photo ="";}
    $groups->group_interest = $_POST['grpInterest'];
    $groups->group_desc= sanitize(trim($_POST['group_desc']), 3);
    $groups->group_privacy = sanitize(trim($_POST['create_group_privacy']), 3);
    $groups->group_locations = $_POST['cl_location'];

    if($groups->createGroup()){
        foreach($groups->group_interest as $interest_id){
                 if(is_numeric($interest_id)){
                     $groups->addInterest($interest_id);
                 }
        }
        $invitees= $_POST['grpEmail'];
        foreach($invitees as $invitee){
            if(is_numeric($invitee) || check_email($invitee)){joinGroup($groups->group_id, $invitee);}
        }
        if(sizeof($groups->group_locations!=0)){
            $groups->insertLocations();
        }

        header('Location: '.getmyurl('group_page', $groups->group_id));

    }else{
        echo 'something wrong';
    }
    $main_smarty->assign('posttitle', 'Create Group');
    $main_smarty->assign('info', '');
    $main_smarty->assign('tpl_center', $the_template . '/info_center');
    $main_smarty->display($the_template . '/pligg.tpl');

}
else{

    $main_smarty->assign('validationEngine','true');
    $main_smarty->assign('posttitle', 'Create Group');
    $main_smarty->assign('all_location',getMumbaiLocation());
    $main_smarty->assign('tpl_center', $the_template . '/create_group_center');
    $main_smarty->display($the_template . '/pligg.tpl');

}



?>
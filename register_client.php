<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Swaroop
 * Date: 12/12/12
 * Time: 12:21 AM
 * To change this template use File | Settings | File Templates.
 */

include_once('Smarty.class.php');
$main_smarty = new Smarty;

include_once('config.php');
include_once(mnminclude.'html1.php');
include_once(mnminclude.'link.php');
include_once(mnminclude.'interest_member.php');
include_once(mnminclude.'interest.php');
include_once(mnminclude.'location.php');
include_once(mnminclude.'user.php');
include_once(mnminclude.'user_fetch.php');
include_once(mnminclude.'group.php');
include_once(mnminclude.'groups.php');
include_once('global_variable.php');
$mobOpt=true;
include_once(mnminclude.'smartyvariables.php');

global $current_user;
if($current_user->authenticated){
    header('Location: '.my_base_url);
}


$navwhere['text1'] = $main_smarty->get_config_vars('PLIGG_Visual_Breadcrumb_Register');
$navwhere['link1'] = getmyurl('register', '');
$main_smarty->assign('navbar_where', $navwhere);
$main_smarty->assign('posttitle', "Register with facebook");


// pagename
define('pagename', 'register_client');


$main_smarty->assign('pagename', pagename);
$main_smarty->assign('validationEngine', 'true');
$main_smarty->assign('privacyUrl', my_base_url.my_pligg_base.'/privacy.php');
$main_smarty->assign('termUrl', my_base_url.my_pligg_base.'/terms.php');
$main_smarty->assign('guestPage', "true");

if(!isset($_REQUEST['data']) || $_REQUEST['data']!="true"){
    // check fb id
    $main_smarty->assign('tpl_center', $the_template . '/registerfb');
}
else{
    if(isset($_REQUEST['client'])){
        $client=sanitize($_REQUEST['client'], 3);
        $main_smarty->assign('client', $client);
    }
    if($client=="facebook"){
        if(!isset($_REQUEST['fb_id']) || !is_numeric($_REQUEST['fb_id'])){
            $main_smarty->assign('tpl_center', $the_template . '/register_client_error');
            die();
        }else{
            $fb_id=$_REQUEST['fb_id'];
            $info= getFBInfo($fb_id);
            $accessToken=$info->accessToken;
            //print_r($info);
            //die();
            if(!check_email($info->email)){
                header("Location: 404error.php?error=FB_Register_Error_EmailInvalid");
            }

            elseif(email_exists($info->email)){
                $user =new User();
                $user->id=getUserDetailsFromEmail($info->email)->user_id;
                $user->read();
                if($user->fbid==0){
                    $user->fbid=$fb_id;
                    $user->enabled=1;
                    $user->store();
                }
                //die('checkinh');
                header("Location: ".my_base_url."/login.php?referrer=facebook");
            }
            //die('checkinh1');
            //echo count($info);
            if(count($info)==0){
                $main_smarty->assign('tpl_center', $the_template . '/register_client_error');

            }else{
                $main_smarty->assign('fbid',$fb_id);
                $name=$info->name;
                $email=$info->email;
                if(!check_email($email)){$hasEmail=false;}
                else {$hasEmail=true;}
                $dob=$info->dob;
                $sex=$info->sex;
                $profilePic=$info->imageurl;

                $cl_interest=$info->interests;


                $i=0;
                $j=1;
                $fb_interest=match_interest($cl_interest);

                //print_r($fb_interest);
                foreach($fb_interest as $item){
                    $interest[$j]= $item['id'];
                    $interest_list[$i]['id']=$item['id'];
                    $interest_list[$i]['image']=getInterestImage($item['id'], 50);
                    $interest_list[$i]['name']=$item['name'];
                    $i++;$j++;
                }

                if(isset($_REQUEST['client_form']) && $_REQUEST['client_form']==1){
                    $password=randomPassword();
                    $password2=$password;
                    $location = implode(',',$_REQUEST['cl_location']);
                    if($name=="" || $name==null || empty($name)){
                        $name=sanitize($_REQUEST['cl_name'],3);
                    }
                    if($dob!="" && $dob!="0000-00-00"){
                        $date=date('d', strtotime($dob));
                        $month=date('m', strtotime($dob));
                        $year=date('Y', strtotime($dob));
                    }else{
                        $date=$_REQUEST['reg_date'];
                        $month=$_REQUEST['reg_month'];
                        $year=$_REeQUEST['reg_year'];
                    }
                    $phone="91-".sanitize($_POST["reg_phone_prefix"], 3);

                    if($sex=="female") $sex="F";
                    else  $sex="M";

                    $fm_interest=$_REQUEST['interest'];
                    //print_r($fm_interest);echo "<br>";
                    //echo count($fm_interest);
                    //print_r($interest);
                    if(count($fm_interest)>0){
                        for($k=0;$k<count($fm_interest); $k++ ){
                            //echo "....".$k.",,,,".$fm_interest[$k];
                            $interest[$j]=$fm_interest[$k];
                            $j++;
                        }
                    }
                    $dob=$year."-".$month."-".$date;
                    $error=register_check_errors($email, $password, $password2, $date, $month, $year, $sex, $phone, $name, $interest);

                    if($error==false){
                        //die('passed');
                        if(register_add_user($email, $password,$name, $sex, $phone, $dob, $password2, $interest, $profilePic, $fb_id,$location)){
                            //expireRegkey($key);
                            header('Location: '.my_base_url.my_pligg_base.'/gettingstarted.php?start=1');

                        }
                        else{
                            header("Location: ". my_pligg_base."/login.php?error=We couldn't register you at this time. Please try again later");
                        }
                    }

                }

                if($email!=$info->email)$sec_email=$info->email;

                if($name!=""){
                    $main_smarty->assign('cl_name', "true");
                    $main_smarty->assign('cl_name_val', $name);
                }else{
                    $main_smarty->assign('cl_name', "false");
                }
                if($dob!="" && $dob!="0000-00-00"){
                    $main_smarty->assign('cl_dob', "true");
                    $main_smarty->assign('cl_dob_date_val', date('d', strtotime($dob)));
                    $main_smarty->assign('cl_dob_month_val', date('m', strtotime($dob)));
                    $main_smarty->assign('cl_dob_year_val', date('Y', strtotime($dob)));
                }else{
                    $main_smarty->assign('cl_dob', "false");
                }
                $main_smarty->assign('cl_mobile', "false");
                //$main_smarty->assign('interest_list',match_interest($cl_interest));
                $main_smarty->assign('interest_list', $interest_list);
                $i=0;

                foreach(getInterestListForRegistration(100) as $item){
                    if($i>97){ break;}

                    foreach($interest_list as $interest_one){
                        if($interest_one['id']==$item['interest_id'])
                        {
                            continue 2;
                        }
                    }
                    //echo $item['id'];
                    $interest_rg[$i]['id']=$item['interest_id'];
                    $interest_rg[$i]['image']=getInterestImage($item['interest_id'], 50);
                    $interest_rg[$i]['name']=$item['interest_name'];
                    $i++;
                }

                $main_smarty->assign('interest_rg', $interest_rg);
                $main_smarty->assign('all_location',getMumbaiLocation());
                $main_smarty->assign('tpl_center', $the_template . '/register_client_center');
            }


        }

    }

}




$main_smarty->assign('tpl_header', $the_template . '/header_guest');
$main_smarty->display($the_template . '/pligg.tpl');

function getFBInfo($fb_id){
    global $db;
    if(!is_numeric($fb_id)) return false;
    $sql="SELECT `name`, `email`, `dob`, `hometown`, `interests`,`imageurl`, `gender`, `accessToken` FROM ".table_fb_user." WHERE `fbid`=".$fb_id;
    return $db->get_row($sql);
}

function match_interest($int_param){
    global $db;
    //echo $int_param;
    $flexarray=explode(",",$int_param);
    if(count($flexarray)!=0){
        $sql="SELECT `interest_id` AS id, `interest_name` AS name FROM ".table_interests." WHERE `interest_id`=75 OR `interest_id`=78 OR `interest_id`=71 ";
        foreach ($flexarray as $interest){
            if(trim($interest)!=""){
                $sql.=" OR LCASE(`interest_meta`) LIKE '%".strtolower($interest)."%'";
            }
        }
    }
    //die($sql);
    //echo $sql;
    $interests=$db->get_results($sql, ARRAY_A);
    //print_r($interests);
    //die();

    return $interests;

}


function register_check_errors($email, $password, $password2, $date, $month, $year, $sex, $phone, $name, $interest){

    global $main_smarty;

    require_once(mnminclude.'check_behind_proxy.php');
    $userip=check_ip_behind_proxy();
    if(is_ip_banned($userip)) {
        $form_username_error[] = $main_smarty->get_config_vars('PLIGG_Visual_Register_Error_YourIpIsBanned');
        print_r($form_phone_error);
        $error = true;
    }

    if($sex!='M' && $sex!='F'){
        $form_sex_error=$main_smarty->get_config_vars('PLIGG_Visual_Register_Error_Sex');
        print_r($form_sex_error);
        $error=true;
    }
    if(!checkdate($month, $date, $year)){
        $form_dob_error[]= $main_smarty->get_config_vars('PLIGG_Visual_Register_Error_Dob');
        print_r($form_dob_error);
        $error=true;
    }
    if(!is_array($interest)){
        $form_interest_error[]=$main_smarty->get_config_vars('PLIGG_Visual_Register_Error_NoInterest');
        print_r($form_interest_error);
        $error=true;
    }

    if(!isValidPhone($phone)){
        $form_phone_error[]=$main_smarty->get_config_vars('PLIGG_Visual_Register_Error_Phone');
        print_r($form_phone_error);
        $error=true;
    }
    if(!check_email(trim($email))) { // if email is not valid
        $form_email_error[] = $main_smarty->get_config_vars('PLIGG_Visual_Register_Error_BadEmail');
        print_r($form_email_error);
        $error = true;
    }

    if(email_exists(trim($email)) ) { // if email already exists
        $form_email_error[] = $main_smarty->get_config_vars('PLIGG_Visual_Register_Error_EmailExists');
        print_r($form_email_error);
        $error = true;
    }
    if(strlen($password) < 8 ) { // if password is less than 5 characters
        $form_password_error[] = $main_smarty->get_config_vars('PLIGG_Visual_Register_Error_FiveCharPass');
        print_r($form_password_error);
        $error = true;
    }
    if($password !== $password2) { // if both passwords do not match
        $form_password_error[] = $main_smarty->get_config_vars('PLIGG_Visual_Register_Error_NoPassMatch');
        $error = true;
    }

    $vars = array('email' => $email, 'password' => $password);
    check_actions('register_check_errors', $vars);

    if($vars['error'] == true){
        $error = true;
        if ($vars['email_error'])
            $form_email_error[] = $vars['email_error'];
        if ($vars['password_error'])
            $form_password_error[] = $vars['password_error'];
    }

    $main_smarty->assign('form_email_error', $form_email_error);
    $main_smarty->assign('form_password_error', $form_password_error);
    $main_smarty->assign('form_sex_error', $form_sex_error);
    $main_smarty->assign('form_dob_error', $form_dob_error);
    $main_smarty->assign('form_phone_error', $form_phone_error);
    $main_smarty->assign('form_interest_error', $form_interest_error);
    $main_smarty->assign('form_location_error', $form_location_error);

    return $error;
}

function register_add_user($email, $password,$name, $sex, $phone, $dob, $password2, $interest, $profilePic, $fb_id,$location){

    $username=$email;
    global $current_user, $main_smarty, $accessToken;
    //print_r($_POST);
    $user = new User();
    $user->names=$name;
    $user->phone=$phone;
    $user->dob=$dob;
    $user->sex=$sex;
    $user->username = $username;
    $user->pass = $password;
    $user->email = $email;
    $user->enabled = 1;
    $user->fbid=$fb_id;
    $user->location=$location;
    $user->accessToken=$accessToken;
    //print_r($user);
    if($user->Create()){





        $user->read('short');

        $registration_details = array(
            'username' => $username,
            'password' => $password,
            'email' => $email,
            'id' => $user->id
        );
        $subject = $main_smarty->get_config_vars("PLIGG_Visual_Fb_Register_Subject");
        /*$body = sprintf($main_smarty->get_config_vars("PLIGG_Visual_Register_FB_Thankyou"),
						$name,
						$username,
						$password
						);
        */
        $array['user_name']=$name;
        $array['user_login']=$username;
        $array['user_password']=$password;
        include_once(mnminclude."generateHtml.php");
        $body=generateHTMLBody($array, 'register_fb_mail');
        send_emailSmtp($email, $name, $subject, $body, 'NONE');
        check_actions('register_success_pre_redirect', $registration_details);

        if($current_user->Authenticate($username, $password, false)){
            registerInterest($interest);
            registerLocations($location);
            $user->uploadAvatars($profilePic);
        }



        return true;
    }
    else{ return false;}

}
function registerInterest($interest){
    foreach($interest as $id){
        if(interestExists($id)) addinterest($id, "public");
    }
}

function registerLocations($location){

    global $db;

    $sql = "SELECT * FROM ".table_locations_mumbai." WHERE location_name IN (".$location.")";

    $results = $db->get_results($sql);

    foreach ($results as $value){
        $array[0] = $value->location_name;
        $array[1] = $value->location_lat1;
        $array[2] = $value->location_lat2;
        $array[3] = $value->location_lon1;
        $array[4] = $value->location_lon2;

        addLocation($array);
    }


}


function randomPassword() {
    $alphabet = "abcdefghijklmnopqrstuwxyzABCDEFGHIJKLMNOPQRSTUWXYZ0123456789";
    $pass = array(); //remember to declare $pass as an array
    $alphaLength = strlen($alphabet) - 1; //put the length -1 in cache
    for ($i = 0; $i < 8; $i++) {
        $n = rand(0, $alphaLength);
        $pass[] = $alphabet[$n];
    }
    return implode($pass); //turn the array into a string
}

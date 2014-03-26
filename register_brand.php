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
include(mnminclude.'html1.php');
include(mnminclude.'link.php');
include(mnminclude.'smartyvariables.php');
include(mnminclude.'interest_member.php');
include(mnminclude.'interest.php');
include(mnminclude.'location.php');
include(mnminclude.'group.php');
include(mnminclude.'groups.php');
include_once(mnminclude.'user.php');
include_once('global_variable.php');
include_once(mnminclude.'register_fn.php');
global $current_user;
if($current_user->authenticated){
    header('Location: '.my_base_url);
}

$vars = '';
check_actions('register_top', $vars);
$navwhere['text1'] = $main_smarty->get_config_vars('PLIGG_Visual_Breadcrumb_Register');
$navwhere['link1'] = getmyurl('register', '');
$main_smarty->assign('navbar_where', $navwhere);
$main_smarty->assign('sidebuttons', "false");
$main_smarty->assign('posttitle', $main_smarty->get_config_vars('PLIGG_Visual_Breadcrumb_Register_Brand'));

$main_smarty->assign('privacyUrl', my_base_url.my_pligg_base.'/privacy.php');
$main_smarty->assign('termUrl', my_base_url.my_pligg_base.'/terms.php');
$main_smarty->assign('guestPage', "true");

// pagename
define('pagename', 'register_brand');

$main_smarty->assign('pagename', pagename);
$main_smarty->assign('validationEngine', 'true');

// sidebar

$pligg_regfrom = isset($_POST["regfrom"]) && sanitize($_POST['regfrom'], 3) != '' ? sanitize($_POST['regfrom'], 3) : '';


if($pligg_regfrom != ''){

	$error = false;
	switch($pligg_regfrom){
		case 'full':
			$email = sanitize($_POST["reg_email"], 3);
			$password = sanitize($_POST["reg_password"], 3);
			$password2 = sanitize($_POST["reg_password2"], 3);
			$sex = "B";
            $name = onlyAlphaNumeric(sanitize($_POST["reg_nam"], 3)) ." ".onlyAlphaNumeric(sanitize($_POST["reg_surnam"], 3));
            $date = sanitize($_POST["reg_date"], 3);
			$month = sanitize($_POST["reg_month"], 3);
			$year = sanitize($_POST["reg_year"], 3);
			$phone = "91-".sanitize($_POST["reg_phone_prefix"], 3);
            //echo $phone;
            $location = $_POST['location'];
            $dob=$year."-".$month."-".$date;
            $interest=$_POST['interest'];
            $profilePic=0;
            $ext=$_POST['profilePicExt'];

            if(is_numeric($_POST['profilePic'])){$profilePic=$_POST['profilePic'];}

			break;

		case 'sidebar':
			$email = sanitize($_POST["email"], 3);
			$password = sanitize($_POST["password"], 3);
			$password2 = sanitize($_POST["password2"], 3);	
			break;

	}

	if(isset($email)){$main_smarty->assign('reg_email', htmlspecialchars($email,ENT_QUOTES));}
	if(isset($password)){$main_smarty->assign('reg_password', htmlspecialchars($password,ENT_QUOTES));}
	if(isset($password2)){$main_smarty->assign('reg_password2', htmlspecialchars($password2,ENT_QUOTES));}
    //sanitize the interest and the location data
    /*for($i=1; $i<=5; $i++){
        if(isset($_POST['gid'.$i]) && is_numeric($_POST['gid'.$i])){
            $gid[$i]=$_POST['gid'.$i];
        }
    }
    // checking latitude and longitude
    for ($i=1; $i<=5; $i++){
        if(isset($_POST['lat1_'.$i])) $location[$i]['lat1']=$_POST['lat1_'.$i];
        if(isset($_POST['lat2_'.$i])) $location[$i]['lat2']=$_POST['lat2_'.$i];
        if(isset($_POST['lng1_'.$i])) $location[$i]['lng1']=$_POST['lng1_'.$i];
        if(isset($_POST['lng2_'.$i])) $location[$i]['lng2']=$_POST['lng2_'.$i];
        if(isset($_POST['circle'.$i])) $location[$i]['name']=sanitize($_POST['circle'.$i], 3);


    }
      print_r($location);


          // die();


     */







   //echo $email;
    $errorMssg="";
    $error = register_check_errors($email, $password, $password2,  $date, $month, $year, $sex, $phone,$name, $interest, $location);
    if($error == false){
        if(isset($_REQUEST['profilePic'])){

        }


		register_add_user($email, $password,$name, $sex, $phone, $dob, $password2, $interest, $location, $profilePic, $ext);

    } else {
        $main_smarty->assign('errorMssg', $main_smarty->get_config_vars('PLIGG_Visual_'.$errorMssg));
        $main_smarty->assign('type', "Registration Error");
        $main_smarty->assign('positive', "true");
        $main_smarty->assign('okayText', "Register Again");
        $main_smarty->assign('cancelText', "Go Home");
        $main_smarty->assign('url', getmyurl('index'));
        $main_smarty->assign('mergeUrl', getmyurl('register'));
        $main_smarty->assign('tpl_center', $the_template . '/fb_come');
        $main_smarty->assign('tpl_header', $the_template . '/header_guest');
        $main_smarty->display($the_template . '/pligg.tpl');
        die();
        //die($error);
//		print "Error";


    }


} else {

	$testing = false; // changing to true will populate the form with random variables for testing.
	if($testing == true){
		$main_smarty->assign('reg_username', mt_rand(1111111, 9999999));
		$main_smarty->assign('reg_email', mt_rand(1111111, 9999999) . '@test.com');
		$main_smarty->assign('reg_password', '12345');
		$main_smarty->assign('reg_password2', '12345');
	}

}

$vars = '';
check_actions('register_showform', $vars);
      $i=0;
      foreach(getInterestListForRegistration(150) as $item){
          if($item['interest_category']==$item['interest_id'])continue;
          $interest_list[$i]['id']=$item['interest_id'];
          $interest_list[$i]['image']=getInterestImage($item['interest_id'], 50);
          $interest_list[$i]['name']=$item['interest_name'];
          $i++;
      }
$main_smarty->assign('interest_list', $interest_list);
$main_smarty->assign('name_hint', "Please reveal your true name and surname.");
$main_smarty->assign('name_error', "Please reveal your name and surname.");
$main_smarty->assign('email_hint', "Don't worry! We will NOT spam your inbox NOR share it with everyone");
$main_smarty->assign('email_error', "Please enter a Valid Email ID");
$main_smarty->assign('password_hint', "Your password should contain at least 8 characters");
$main_smarty->assign('password_error', "Unsafe Password! Your password should contain at least 8 characters");
$main_smarty->assign('verify_password_hint', "Please re-type the password here");
$main_smarty->assign('verify_password_error', "Your passwords do not match! ");
$main_smarty->assign('phone_hint', "Please enter your mobile number.");
$main_smarty->assign('phone_error', "Please enter a correct mobile number.");
$main_smarty->assign('dob_hint', "Company Since");
$main_smarty->assign('dob_error', "Please enter a valid date");
$main_smarty->assign('tpl_center', $the_template . '/register_brand_center');
$main_smarty->assign('tpl_header', $the_template . '/header_guest');
$main_smarty->display($the_template . '/pligg.tpl');

die();




function register_add_user($email, $password,$name, $sex, $phone, $dob, $password2, $interest, $location, $profilePic, $ext){
    $username=$email;
	global $current_user, $main_smarty, $key;
    //print_r($_POST);
	$user = new User();
    $user->names=$name;
    $user->phone=$phone;
    $user->dob=$dob;
    $user->sex=$sex;
	$user->username = $username;
	$user->pass = $password;
	$user->email = $email;
    $user->enabled = 0;
    //print_r($user);

	if($user->Create()){
        $user->read('short');

		$registration_details = array(
			'username' => $username,
			'password' => $password,
			'email' => $email,
			'id' => $user->id,
            'redirect'=> my_base_url.'/verifyaccount.php?verify=true'
		);
        $url=getmyFullurl("verifyaccount", md5($email."shaukk-verify"), $email);
        $subject = $main_smarty->get_config_vars("PLIGG_Visual_Register_Subject");
        /*$body = sprintf($main_smarty->get_config_vars("PLIGG_Visual_Register_Thankyou"),
						$name,
                        $url
						);


         */
        $array['user_name']=$name;
        $array['confirmLink']=$url;
        $array['username']=$username;
        $array['password']=$password;
        include_once(mnminclude."generateHtml.php");
        $body=generateHTMLBody($array, 'register_mail');
        send_emailSmtp($email, $name, $subject, $body, 'NONE');
		check_actions('register_success_pre_redirect', $registration_details);
        $current_user->firstlogin=true;
		$current_user->Authenticate($username, $password, false);
           registerInterest($interest);
           registerLocation($location);

        if(is_numeric($profilePic)&& $profilePic!=0){
        //SAVING AVATAR
                           $user_image_path = "avatars/user_uploaded" . "/";
                           $user_image_apath = "/" . $user_image_path;
                           $allowedFileTypes = array("jpeg","jpg","gif","png",'pjpeg');
                           //unset($imagename);
                           $myfile="images/temporary/".$profilePic.".".$ext;
                           if(!in_array($ext,$allowedFileTypes)){
                             $errors['Type'] = 'Only these file types are allowed : jpeg, gif, png';
                           }
                           print_r($errors);
                           $imagename = basename($myfile);
                           if(empty($errors)){
                               $imagename = $user->id . "_original.jpg";
                               $newimage = $user_image_path . $imagename ;
                               $result = copy($myfile, $newimage);
                               if(empty($result))
                                   echo "xyz";
                                   $error["result"] = "There was an error moving the uploaded file.";
                                    unlink($myfile);
                           }

                           // create large avatar
                           include mnminclude . "class.pThumb.php";
                           $img=new pThumb();
                           $img->pSetSize(Avatar_Large, Avatar_Large);
                           $img->pSetQuality(100);
                           $img->pCreate($newimage);
                           $img->pSave($user_image_path . $user->id . "_".Avatar_Large.".jpg");
                           $img = "";

                           // create small avatar
                           $img=new pThumb();
                           $img->pSetSize(Avatar_Small, Avatar_Small);
                           $img->pSetQuality(100);
                           $img->pCreate($newimage);
                           $img->pSave($user_image_path .$user->id. "_".Avatar_Small.".jpg");
                           $img = "";

                           $img=new pThumb();
                           $img->pSetSize(15, 15);
                           $img->pSetQuality(100);
                           $img->pCreate($newimage);
                           $img->pSave($user_image_path .$user->id. "_"."15.jpg");
                           $img = "";

                           $img=new pThumb();
                           $img->pSetSize(50, 50);
                           $img->pSetQuality(100);
                           $img->pCreate($newimage);
                           $img->pSave($user_image_path .$user->id. "_50.jpg");
                           $img = "";

                           $img=new pThumb();
                           $img->pSetSize(85, 85);
                           $img->pSetQuality(100);
                           $img->pCreate($newimage);
                           $img->pSave($user_image_path .$user->id. "_85.jpg");
                           $img = "";



        }

		if ($registration_details['redirect'])
		    header('Location: '.$registration_details['redirect']);
		else {
		    header('Location: '.my_base_url.my_pligg_base.'/bubble.php?forced=true');
		}
		die();
	}
    else{ die('not registered');}

}
function registerInterest($interest){
    foreach($interest as $id){
        if(interestExists($id)) addinterest($id, "public");
    }

}
function registerLocation($location){
    foreach($location as $item){
         addLocation($item);
    }
}

?>

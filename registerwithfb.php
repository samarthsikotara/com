<?php
include_once('Smarty.class.php');
$main_smarty = new Smarty;




include('config.php');
include(mnminclude.'html1.php');
include(mnminclude.'link.php');
include(mnminclude.'smartyvariables.php');
include(mnminclude.'interest_member.php');
include(mnminclude.'interest.php');
include(mnminclude.'location.php');
include_once(mnminclude.'user.php');
global $current_user;
if($current_user->authenticated){
    header('Location: '.my_pligg_base."/index.php");
}

$vars = '';
check_actions('register_top', $vars);
$navwhere['text1'] = $main_smarty->get_config_vars('PLIGG_Visual_Breadcrumb_Register');
$navwhere['link1'] = getmyurl('register', '');
$main_smarty->assign('navbar_where', $navwhere);
$main_smarty->assign('posttitle', $main_smarty->get_config_vars('PLIGG_Visual_Breadcrumb_Register'));

// pagename
define('pagename', 'register2'); 

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
			$sex = sanitize($_POST["reg_sex"], 3);
			$name = sanitize($_POST["reg_nam"], 3)." ".sanitize($_POST["reg_surnam"], 3);
			$date = sanitize($_POST["reg_date"], 3);
			$month = sanitize($_POST["reg_month"], 3);
			$year = sanitize($_POST["reg_year"], 3);
			$phone = "91-".sanitize($_POST["reg_phone_prefix"], 3);
            //echo $phone;
            $dob=$year."-".$month."-".$date;
            $interest=$_POST['interest'];
            $location=$_POST['location'];

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









    $error = register_check_errors($email, $password, $password2,  $date, $month, $year, $sex, $phone,$name, $interest, $location);
    if($error == false){

		register_add_user($email, $password,$name, $sex, $phone, $dob, $password2, $interest, $location);
	} else {

        die($error);
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
$main_smarty->assign('phone_hint', "Please enter your mobile number. Don't worry! We will NOT share this information with everyone");
$main_smarty->assign('phone_error', "Please enter a correct mobile number.");
$main_smarty->assign('dob_hint', "Please Provide your date of birth");
$main_smarty->assign('dob_error', "Please enter a valid date");
$main_smarty->assign('dob_error_child', "Children less than 13 years of age are not allowed to register. Sorry!");

$main_smarty->assign('tpl_center', $the_template . '/registerfb');
$main_smarty->assign('tpl_header', $the_template . '/header_guest');
$main_smarty->display($the_template . '/pligg.tpl');

die();


function register_check_errors($email, $password, $password2, $date, $month, $year, $sex, $phone, $name, $interest, $location){

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
    if(!is_array($location)){
        $form_location_error[]=$main_smarty->get_config_vars('PLIGG_Visual_Register_Error_NoLocation');
        print_r($form_location_error);
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

function register_add_user($email, $password,$name, $sex, $phone, $dob, $password2, $interest, $location){
    $username=$email;
	global $current_user;
    //print_r($_POST);
	$user = new User();
    $user->names=$name;
    $user->phone=$phone;
    $user->dob=$dob;
    $user->sex=$sex;
	$user->username = $username;
	$user->pass = $password;
	$user->email = $email;
    //print_r($user);
	if($user->Create()){





		$user->read('short');

		$registration_details = array(
			'username' => $username,
			'password' => $password,
			'email' => $email,
			'id' => $user->id
		);
	
		check_actions('register_success_pre_redirect', $registration_details);

		$current_user->Authenticate($username, $password, false);
           registerInterest($interest);
           registerLocation($location);

        //SAVING AVATAR

                           $user_image_path = "avatars/user_uploaded" . "/";
                           $user_image_apath = "/" . $user_image_path;
                           $allowedFileTypes = array("image/jpeg","image/gif","image/png",'image/x-png','image/pjpeg');
                           //unset($imagename);

                           if(isset($_POST['profile-photo-uploaded'])){
                               $myfile =$_POST['profile-photo-uploaded'];

                               $mytmpfile = $_POST['profile-photo-uploaded'];
                               $imagename = basename($myfile);
                               if(empty($errors)){
                                   $imagename =$user->id. "_original.jpg";
                                   $newimage = $user_image_path . $imagename ;
                                   $result = copy($mytmpfile, $newimage);
                                   if(empty($result))
                                       //echo "xyz";
                                       $error["result"] = "There was an error moving the uploaded file.";
                               }
                           }else{
                               $myfile = $_FILES['profile-photo']['name'];
                               $mytmpfile = $_FILES['profile-photo']['tmp_name'];
                               if(!in_array($_FILES['profile-photo']['type'],$allowedFileTypes)){
                                 $errors['Type'] = 'Only these file types are allowed : jpeg, gif, png';
                               }

                               $imagename = basename($myfile);
                               if(empty($errors)){
                                   $imagename = $user->id . "_original.jpg";
                                   $newimage = $user_image_path . $imagename ;
                                   $result = @move_uploaded_file($mytmpfile, $newimage);
                                   if(empty($result))
                                       echo "xyz";
                                       $error["result"] = "There was an error moving the uploaded file.";
                               }
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






		if ($registration_details['redirect'])
		    header('Location: '.$registration_details['redirect']);
		elseif(pligg_validate()){
		    header('Location: '.my_base_url.my_pligg_base.'/register_complete.php?user='.$username);
		} else {
		    header('Location: ' . getmyurl('profile', $username));
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

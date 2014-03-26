<?php
// The source code packaged with this file is Free Software, Copyright (C) 2005 by
// Ricardo Galli <gallir at uib dot es>.
// It's licensed under the AFFERO GENERAL PUBLIC LICENSE unless stated otherwise.
// You can get copies of the licenses here:
// 		http://www.affero.org/oagpl.html
// AFFERO GENERAL PUBLIC LICENSE is also included in the file called "COPYING".

//phpinfo();
include_once('Smarty.class.php');
$main_smarty = new Smarty;

include('config.php');
include(mnminclude.'html1.php');
include(mnminclude.'link.php');
include(mnminclude.'smartyvariables.php');

// breadcrumbs and page title
$navwhere['text1'] = $main_smarty->get_config_vars('PLIGG_Visual_Breadcrumb_Login');
$navwhere['link1'] = getmyurl('loginNoVar', '');
$main_smarty->assign('navbar_where', $navwhere);
$main_smarty->assign('posttitle', $main_smarty->get_config_vars('PLIGG_Visual_Breadcrumb_Login'));

// sidebar
$main_smarty = do_sidebar($main_smarty);

// initialize error message variable
$errorMsg="";

// if user requests to logout
if($my_pligg_base){
	if (strpos($_GET['return'],$my_pligg_base)!==0) $_GET['return']=$my_pligg_base . '/';
	if (strpos($_POST['return'],$my_pligg_base)!==0) $_POST['return']=$my_pligg_base . '/';
}
if(isset($_GET["op"])){
	if(sanitize($_GET["op"], 3) == 'logout') {
		$current_user->Logout(sanitize($_GET['return'], 3));
	}
}
if(isset($_REQUEST['error'])){
    $errorMsg=$_REQUEST['error'];
}

// if user tries to log in
if( (isset($_POST["processlogin"]) && is_numeric($_POST["processlogin"])) || (isset($_GET["processlogin"]) && is_numeric($_GET["processlogin"])) ){
	if($_POST["processlogin"] == 1) { // users logs in with username and password
		$username = sanitize(trim($_POST['username']), 3);
		$password = sanitize(trim($_POST['password']), 3);
		if(isset($_POST['persistent'])){$persistent = sanitize($_POST['persistent'], 3);}else{$persistent = '';}

		$dbusername=sanitize($db->escape($username),4);
		require_once(mnminclude.'check_behind_proxy.php');
		$lastip=check_ip_behind_proxy();
		$login=$db->get_row("SELECT *, UNIX_TIMESTAMP()-UNIX_TIMESTAMP(login_time) AS time FROM " . table_login_attempts . " WHERE login_ip='$lastip'");
        $db->query("INSERT IGNORE INTO ".table_all_attempts." SET login_username = '$dbusername', login_time=NOW(), status=0, login_ip='$lastip'");
        $login_time = time();
        $login_id = $login->login_id;
        if ($login->login_id)
		{
		    if ($login->time < 3){echo "first";$errorMsg=sprintf($main_smarty->get_config_vars('PLIGG_Visual_Login_Error'),3); }
		    elseif ($login->login_count>=3)
		    {
			if ($login->time < min(60*pow(2,$login->login_count-3),3600))
			    $errorMsg=sprintf($main_smarty->get_config_vars('PLIGG_Login_Incorrect_Attempts'),$login->login_count,min(60*pow(2,$login->login_count-3),3600)-$login->time);
		    }
		}
		elseif (!is_ip_approved($lastip))
		{
            $ipsql="INSERT INTO ".table_login_attempts." SET login_username = '$dbusername', login_time=NOW(), login_ip='$lastip'";
		    $db->query($ipsql);

		}

		if (!$errorMsg)
		{
		    if($current_user->Authenticate($username, $password, $persistent) == false) {
                //die('in-correct');
                if ($login->login_id){
                $incorrSql="UPDATE ".table_login_attempts." SET login_username='$dbusername', login_count=login_count+1, login_time=NOW() WHERE login_id=".$login_id;
		    	$db->query($incorrSql);
			    }
                $errorMsg=$main_smarty->get_config_vars('PLIGG_Visual_Login_Error');


		    } else {
                //die('correct');
                $db->query("UPDATE ".table_all_attempts." SET status=1 WHERE login_ip='".$lastip."' AND login_username ='".$dbusername."' AND UNIX_TIMESTAMP(login_time) > '".($login_time-3)."' ");
                //die("UPDATE ".table_all_attempts." SET status=1 WHERE login_ip='".$lastip."' AND login_username ='".$dbusername."' AND login_time > '".($login_time-3)."' ");
                $sql = "DELETE FROM " . table_login_attempts . " WHERE login_ip='$lastip' ";
                $db->query($sql);

                if(strlen(sanitize($_REQUEST['return'], 3)) > 1) {
                    $return = sanitize($_REQUEST['return'], 3);
                } else {
                    $return =  my_pligg_base.'/';
                }

                define('logindetails', $username . ";" . $password . ";" . $return);

                $vars = '';
                check_actions('login_success_pre_redirect', $vars);

                if(strpos($_SERVER['SERVER_SOFTWARE'], "IIS") && strpos(php_sapi_name(), "cgi") >= 0){
                    echo '<SCRIPT LANGUAGE="JavaScript">window.location="' . $return . '";</script>';
                    echo $main_smarty->get_config_vars('PLIGG_Visual_IIS_Logged_In') . '<a href = "'.$return.'">' . $main_smarty->get_config_vars('PLIGG_Visual_IIS_Continue') . '</a>';
                } else {
                    header('Location: '.$return);
                }
                die;
		    }
		}
	}

	if($_POST["processlogin"] == 3) { // if user requests forgotten password
	    $email = sanitize($db->escape(trim($_POST['email'])),4);
	    if (check_email($email)){
			$user = $db->get_row("SELECT * FROM `" . table_users . "` where `user_email` = '".$email."' AND user_level!='Spammer'");
			if($user){
				$username = $user->user_login;
				$salt = substr(md5(uniqid(rand(), true)), 0, SALT_LENGTH);
				$saltedlogin = generateHash($user->user_login);
	
				$to = $user->user_email;
				$subject = $main_smarty->get_config_vars("PLIGG_PassEmail_Subject");
	
				$password = substr(md5(uniqid(rand(), true)),0,8);
				$saltedPass = md5(generateHash($password));

				$db->query('UPDATE `' . table_users . "` SET `last_reset_code` = '$saltedPass' , `last_reset_request` = FROM_UNIXTIME(".time().")  WHERE `user_login` = '$username'");


				$body = sprintf($main_smarty->get_config_vars("PLIGG_PassEmail_PassBody"),
						$user->user_names,
						$my_base_url . $my_pligg_base . '/login.php?processlogin=4&username='.$username.'&confirmationcode='.$saltedPass,
						$my_base_url . $my_pligg_base . '/login.php?processlogin=4&username='.$username.'&confirmationcode='.$saltedPass
						);
				//$body = $main_smarty->get_config_vars("PLIGG_PassEmail_Body") . $my_base_url . $my_pligg_base . '/login.php?processlogin=4&username=' . $username . '&confirmationcode=' . $saltedlogin;
                require_once("phpmailer/class.phpmailer.php");
                $mail  = new PHPMailer();
                $mail->IsSMTP();                            // telling the class to use SMTP
                $mail->Host       = "smtp.gmail.com";       // SMTP server
                $mail->SMTPDebug  = 0;                      // enables SMTP debug information (for testing)
                $mail->SMTPAuth   = true;
                $mail->SMTPSecure = "ssl";                  // enable SMTP authentication
                $mail->Port       = 465;                    // set the SMTP port for the GMAIL server
                $mail->Username   = "noreply@shaukk.com";   // SMTP account username
                $mail->Password   = "shaukk_no-reply";      // SMTP account password
                $mail->SetFrom('noreply@shaukk.com', 'Shaukk Team');
                $mail->Subject    = $subject;
                $mail->MsgHTML($body);
                $mail->AddAddress($to, $user->user_names);
   				if(time() - strtotime($user->last_reset_request) > $main_smarty->get_config_vars("PLIGG_PassEmail_LimitPerSecond")){
					if ($mail->Send()){
                        //die($body);
						$main_smarty->assign('user_login', $user->user_login);
						$main_smarty->assign('profile_url', getmyurl('profile'));
						$main_smarty->assign('login_url', getmyurl('loginNoVar'));
	                    $generrorMsg = $main_smarty->get_config_vars("PLIGG_PassEmail_SendSuccess");
	                    $db->query('UPDATE `' . table_users . '` SET `last_reset_request` = FROM_UNIXTIME('.time().') WHERE `user_login` = "'.$username.'"');
						define('pagename', 'login');
						$main_smarty->assign('pagename', pagename);
						$generrorMsg = $main_smarty->get_config_vars('PLIGG_Visual_Password_Sent');
					}else{
						$errorMsg = $main_smarty->get_config_vars('PLIGG_Visual_Login_Delivery_Failed');
					}
				}else{
					$errorMsg = $main_smarty->get_config_vars("PLIGG_PassEmail_LimitPerSecond_Message");
				}
			}else{
				$errorMsg = $main_smarty->get_config_vars('PLIGG_Visual_Password_Sent');
			}
		}else{
		$errorMsg = $main_smarty->get_config_vars('PLIGG_Visual_Register_Error_BadEmail');
	    }
	}

	if($_GET["processlogin"] == 4) { // if user clicks on the forgotten password confirmation code
		$username = $db->escape(sanitize(sanitize(trim($_GET['username']), 3), 4));
		if(strlen($username) == 0){
			$errorMsg = $main_smarty->get_config_vars("PLIGG_Visual_Login_Forgot_Error");
		}
		else {
			$confirmationcode = sanitize($_GET["confirmationcode"], 3);
			$DBconf = $db->get_var("SELECT `last_reset_code` FROM `" . table_users . "` where `user_login` = '".$username."'");

            if($DBconf){
				if($DBconf == $confirmationcode && !empty($confirmationcode)){
                       //$errorMsg="Please enter the new Password";
                       $main_smarty->assign('forgetAskPassword', "true");
                       $main_smarty->assign('username', $username);
                       $main_smarty->assign('resetcode', $confirmationcode);
				}	else {
					$errorMsg = $main_smarty->get_config_vars('PLIGG_Visual_Login_Forgot_ErrorBadCode');
				}
			} else {
				$errorMsg = $main_smarty->get_config_vars('PLIGG_Visual_Login_Forgot_ErrorBadCode');
			} 
		}
	}
    if($_POST["processlogin"] == 5) { // if user clicks on the forgotten password confirmation code
		$username = $db->escape(sanitize(sanitize(trim($_POST['username']), 3), 4));
		$password=$_POST['forgot-password'];
        if(strlen($password) < 8 ) { // if password is less than 5 characters
            $errorMsg = $main_smarty->get_config_vars('PLIGG_Visual_Register_Error_FiveCharPass');
        }else{
            if(strlen($username) == 0){
                $errorMsg = $main_smarty->get_config_vars("PLIGG_Visual_Login_Forgot_Error");
            }
            else {
                $confirmationcode = sanitize($_POST["confirmationcode"], 3);
                $DBconf = $db->get_var("SELECT `last_reset_code` FROM `" . table_users . "` where `user_login` = '".$username."'");
                if($DBconf){
                    if($DBconf == $confirmationcode && !empty($confirmationcode)){
                        $saltedpass=generateHash($password);

                         if($db->query('UPDATE `' . table_users . '` SET `last_reset_code` = "",`last_reset_request`="0", `user_pass`="'.$saltedpass.'" WHERE `user_login` = "'.$username.'"')){
                             $generrorMsg=$main_smarty->get_config_vars('PLIGG_Visual_Login_Forgot_PassReset');
                         }

                    }	else {
                        $errorMsg = $main_smarty->get_config_vars('PLIGG_Visual_Login_Forgot_ErrorBadCode');
                    }
                } else {
                    $errorMsg = $main_smarty->get_config_vars('PLIGG_Visual_Login_Forgot_ErrorBadCode');
                }
            }
        }
	}
}   

if(isset($_REQUEST['request_invite']) && $_REQUEST['request_invite']=='1'){
    global $db;
    if(!check_email(trim($_REQUEST['request_email']))){$main_smarty->assign('request_error', "Please give a valid E-mail");}
    else{
        if(user_exists($_REQUEST['request_email']))$errorMsg="This email is already registered with shaukk.";
        else{
            $findsql="SELECT COUNT(*) FROM ".table_reg_key." WHERE `reg_email`='".$_REQUEST['request_email']."'";
            if($db->get_var($findsql)>0) $generrorMsg="Your request is already queued up. Shaukk is eager to move up the queue and invite you. Please be patient.";
            else{
                $sql="INSERT INTO ".table_reg_key." (`reg_email`, `date`, `active`) VALUES ('".$_REQUEST['request_email']."','".date("Y-m-d H:i:s", time())."', 0)";


                $status=$db->query($sql);
                if($status){
                    $generrorMsg= "Thank you for your interest in Shaukk. We value your request - An invitation to join us is on its way!";
                }
            }
        }
    }
}
if(isset($_REQUEST['login_key']) && isset($_REQUEST['fb_id']) && is_numeric($_REQUEST['fb_id']) && md5($_REQUEST['fb_id']."punisher")==$_REQUEST['login_key']){
    global $db, $current_user;
    if(strlen(sanitize($_REQUEST['return'], 3)) > 1) {
				$return = sanitize($_REQUEST['return'], 3);
    } else {
				$return =  my_pligg_base.'/';
	}
    $sql="SELECT `user_login`, `user_pass` from ".table_users." WHERE `user_fbid`=".$_REQUEST['fb_id']." LIMIT 1";

    $user_info = $db->get_row($sql);
    //print_r($user_info);
    if($current_user->Authenticate($user_info->user_login, $user_info->user_pass,false,  $user_info->user_pass))
    header("Location: ".$return);

}else if(isset($_REQUEST['fb_id'])){
    $errorMsg="Please request an invite to register.";
}
// pagename
define('pagename', 'login'); 
$main_smarty->assign('pagename', pagename);

$main_smarty->assign('errorMsg',$errorMsg);
$main_smarty->assign('generrorMsg',$generrorMsg);
$main_smarty->assign('register_url', getmyurl('register'));

// misc smarty 


// show the template
$main_smarty->assign('tpl_center', $the_template . '/login_new_center');
$main_smarty->assign('tpl_header', $the_template . '/header_guest');
if(isset($_REQUEST['plain']) && $_REQUEST['plain']=="true"){
    $main_smarty->assign('URL_login', 'login.php');
    $main_smarty->display($the_template . '/pligg_content.tpl');
}else{
$main_smarty->display($the_template . '/pligg.tpl');
}
?>
		
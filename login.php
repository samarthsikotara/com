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
include_once('config.php');
include(mnminclude.'html1.php');
include(mnminclude.'link.php');
include_once(mnminclude.'user.php');
include_once(mnminclude.'interest_member.php');
include_once(mnminclude.'location.php');
include_once(mnminclude.'group.php');
include_once(mnminclude.'fb_api_config.php');
if(isset($_REQUEST['source']) && $_REQUEST['source']=="app" && isset($_REQUEST['appId']) && is_numeric($_REQUEST['appId'])){
     $isApp=true;
     $appId=$_REQUEST['appId'];
}
if(!$isApp){
    include('global_variable.php');
}
else{
    header("Content-type: text/json");
}
$mobOpt=true;
include_once(mnminclude.'smartyvariables.php');



// breadcrumbs and page title
$navwhere['text1'] = $main_smarty->get_config_vars('PLIGG_Visual_Breadcrumb_Login');
$navwhere['link1'] = getmyurl('loginNoVar', '');
$main_smarty->assign('navbar_where', $navwhere);
$main_smarty->assign('posttitle', "Login");
$main_smarty->assign('og_posttitle', "Login");
$main_smarty->assign('guestPage', "true");

if(isset($_REQUEST['forgot']) && $_REQUEST['forgot']=="true"){
  $main_smarty->assign('forgot', 'true');
}
// sidebar
$main_smarty = do_sidebar($main_smarty);

function curl_get_file_contents($URL) {
            $c = curl_init();
            curl_setopt($c, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($c, CURLOPT_URL, $URL);
            $contents = curl_exec($c);
            $err  = curl_getinfo($c,CURLINFO_HTTP_CODE);
            curl_close($c);
            if ($contents) return $contents;
            else return FALSE;
}
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
        if($isApp){
            $sessionId=$_REQUEST['session_id'];
            $sessionKey=$_REQUEST['session_key'];
            if(is_numeric($sessionId)){
                $appql="UPDATE ".table_appsession." SET `status`=0 WHERE `sessionId=`".$sessionId." AND `sessionKey`='".$db->escape($sessionKey)."' AND `app_id`=".$db->escape($appId);
                $db->query($newSql);
            }

        }
	}
}
if($current_user->authenticated){
    header('Location: '.my_base_url);
}


if(isset($_REQUEST['error'])){
    $errorMsg=$_REQUEST['error'];
}
  $register_redirect_uri='http://shaukk.com/register_fb_come.php';
  if(!$isApp){
      $facebook = new Facebook($fb_config);
      $fb_user_id = $facebook->getUser();
      if(!$fb_user_id){
         $fb_new_login=$facebook->getLoginUrl($params);
         $main_smarty->assign('fb_login_url', $fb_new_login);
      }


      $registerParams = array(
        'scope' => $fb_perms,
        'redirect_uri' =>  $register_redirect_uri
      );
       $shaukk_register="http://shaukk.com/register.php";
       $main_smarty->assign('shaukk_register', $shaukk_register);

       $registerUrl = $facebook->getLoginUrl($registerParams);

       $main_smarty->assign('fb_register_Url',$registerUrl);
  }
  else{
     $fb_user_id=$_REQUEST['fb_id'];

  }
        //die('<a href="'.$loginUrl.'" >login</a>');
  // die($fb_user_id.','.$accesstoken);
// if user tries to log in
if(isset($_REQUEST['dev'])){
    $main_smarty->assign('dev', 'true');
}
if( (isset($_POST["processlogin"]) && is_numeric($_POST["processlogin"])) || (isset($_GET["processlogin"]) && is_numeric($_GET["processlogin"])) ){
	if($_REQUEST["processlogin"] == 1) { // users logs in with username and password
		$username = sanitize(trim($_REQUEST['username']), 3);
		$password = sanitize(trim($_REQUEST['password']), 3);
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
		    if ($login->time < 3){$errorMsg=sprintf($main_smarty->get_config_vars('PLIGG_Visual_Login_Error'),3); }
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
                if($isApp){
                    $array['status']="failed";
                    $array['code']="Unknown username and password combination";
                    print_r(json_encode($array));
                    die();
                }
                if ($login->login_id){
                $incorrSql="UPDATE ".table_login_attempts." SET login_username='$dbusername', login_count=login_count+1, login_time=NOW() WHERE login_id=".$login_id;
		    	$db->query($incorrSql);
			    }
                if($current_user->error==1){
                   $errorMsg=$main_smarty->get_config_vars('PLIGG_Visual_Login_Disabled');
                }else{
                    $errorMsg=$main_smarty->get_config_vars('PLIGG_Visual_Login_Error');
                }


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
                //die();
                if($isApp){
                    global $current_user;
                    $sessionkey=generateHash('100045458'.$appId);
                    $newSql="INSERT INTO ".table_appsession." (`sessionkey`, `user_id`, `app_id`, `status`) VALUES('".$sessionkey."', $current_user->user_id, ".$appId.", 1)";
                    $db->query($newSql);
                    $array['status']="success";
                    $array['sessionId']=$db->insert_id;
                    $array['sessionKey']=$sessionkey;
                    $array['userId']=$current_user->user_id;
                    print_r(json_encode($array));
                    die();
                }
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
                $mail->Username   = "info@shaukk.com";   // SMTP account username
                $mail->Password   = "shaukk_info";      // SMTP account password
                $mail->SetFrom('info@shaukk.com', 'Shaukk');
                $mail->Subject    = $subject;
                $mail->MsgHTML($body);
                //echo $body;
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
				$errorMsg = $main_smarty->get_config_vars('PLIGG_Visual_Register_Error_NoEmail');
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
        if(user_exists($_REQUEST['request_email']))$errorMsg="You are already a registered member. Please search your mail box for your password or request for a new password to continue.";
        else{
            $findsql="SELECT COUNT(*) FROM ".table_reg_key." WHERE `reg_email`='".$_REQUEST['request_email']."'";
            if($db->get_var($findsql)>0) $generrorMsg="Your request is already queued up. Shaukk is eager to move up the queue and invite you. Please be patient.";
            else{
                include_once(mnminclude."request.php");
                $status=reqInvite($_REQUEST['request_email']);
                sendReqInviteMail($_REQUEST['request_email']);
                if($status){
                    $generrorMsg= "Thank you for your interest in Shaukk. We value your request - An invitation to join us is on its way!";
                }
            }
        }
    }
}
//echo  $fb_user_id;
//Check Access token
if (isset($fb_user_id) && $fb_user_id!=0 && isset($_REQUEST['referrer']) && $_REQUEST['referrer']=='facebook') {
    //echo "checking";
    //echo $fb_user_id;
    global $db, $current_user;

    if(!$isApp){
        $sql="SELECT `user_accessToken`,`user_id`,`user_login`, `user_pass` from ".table_users." WHERE `user_fbid`=".$fb_user_id." LIMIT 1";
    }else{
        $accessToken=$_REQUEST['accessToken'];
        if($accessToken=="")die;
        //$sql="SELECT `user_accessToken`,`user_id`,`user_login`, `user_pass` from ".table_users." WHERE `user_fbid`=".$fb_user_id." AND `user_accessToken`='".$accessToken."' LIMIT 1";
        $sql="SELECT `user_accessToken`,`user_id`,`user_login`, `user_pass` from ".table_users." WHERE `user_fbid`=".$fb_user_id." LIMIT 1";
    }
    $user_info = $db->get_row($sql);

    if(!empty($user_info)){
    //print_r($user_info);
    //die();
        if(!$isApp){
            $code = $_REQUEST["code"];

      // If we get a code, it means that we have re-authed the user
      //and can get a valid access_token.
        //die($user_info->user_accessToken);
            $facebook->setExtendedAccessToken();
            $accessToken=$facebook->getAccessToken();
            //die($accessToken);
            if (isset($code)) {
                $token_url="https://graph.facebook.com/oauth/access_token?client_id="
                  . $config['appId'] . "&redirect_uri=" . urlencode($login_redirect_uri)
                  . "&client_secret=" . $config['secret']
                  . "&code=" . $code . "&display=popup";
                $response = file_get_contents($token_url);
                $params = null;
                parse_str($response, $params);
                $accesstoken = $params['access_token'];
            }

            $graph_url = "https://graph.facebook.com/me?"."access_token=" . $access_token;
            $response = curl_get_file_contents($graph_url);
            $decoded_response = json_decode($response);
            //print_r($decoded_response);
            if ($decoded_response->error) {
                // check to see if this is an oAuth error:
                if ($decoded_response->error->type== "OAuthException") {
                  // Retrieving a valid access token.
                  $dialog_url= "https://www.facebook.com/dialog/oauth?"
                    . "client_id=" . $config['appId']
                    . "&redirect_uri=" . urlencode($login_redirect_uri);
                  echo("<script> top.location.href='" . $dialog_url
                  . "'</script>");
                }
                else {
                  echo "other error has happened";
                }
            }

            if($accessToken!=$user_info->user_accessToken){
                $sql="UPDATE ".table_users." SET `user_accessToken`='$accessToken' WHERE `user_fbid`=".$fb_user_id ;
                $db->query($sql);

            }
        }

      if($current_user->Authenticate($user_info->user_login, $user_info->user_pass,false,  $user_info->user_pass)){
          if($isApp){
              $sessionkey=generateHash('100045458'.$appId);
              $newSql="INSERT INTO ".table_appsession." (`sessionkey`, `user_id`, `app_id`, `status`) VALUES('".$sessionkey."', $current_user->user_id, ".$appId.", 1)";
              $db->query($newSql);
              $array['status']="success";
              $array['sessionId']=$db->insert_id;
              $array['sessionKey']=$sessionkey;
              $array['userId']=$current_user->user_id;
              print_r(json_encode($array));
              die();
          }else{
              if(strlen(sanitize($_REQUEST['fb_return'], 3)) > 1) {
                  $fb_return = sanitize($_REQUEST['fb_return'], 3);
              } else {
                  $fb_return =  my_pligg_base.'/';
              }
            header("Location: ".$fb_return);
          }
      }
    }
    else{
        if($isApp){
            $array['status']="failed";
            $array['code']="Invalid AccessToken. Cannot authenticate User";
            print_r(json_encode($array));
            die();
        }
       header("Location: ".$registerUrl);
    }
} else {
       //not logged-in

}
if(isset($_REQUEST['error']) && $_REQUEST['error']=="access_denied"){
    $main_smarty->assign('type', "Facebook Access Denied");
    $main_smarty->assign('tpl_center', $the_template . '/deauthorize_center');
    $main_smarty->assign('tpl_header', $the_template . '/header_guest');
    $main_smarty->display($the_template . '/pligg.tpl');
    die();
}
// pagename
define('pagename', 'login'); 
$main_smarty->assign('pagename', pagename);

$main_smarty->assign('errorMsg',$errorMsg);
$main_smarty->assign('generrorMsg',$generrorMsg);
$main_smarty->assign('login',$generrorMsg);
$main_smarty->assign('register_url', getmyurl('register'));

// misc smarty 


// show the template
$main_smarty->assign('tpl_center', $the_template . '/login_center');
$main_smarty->assign('meta_description', 'Login to Shaukk. Shaukk is the social utility which helps people to relive their interests near you. You can join local groups to enjoy over a bunch of 100 interests including football, adventure sports, hiking, partying, shopping etc. ');
$main_smarty->assign('tpl_header', $the_template . '/header_guest');
if(isset($_REQUEST['plain']) && $_REQUEST['plain']=="true"){
    $main_smarty->assign('URL_login', getmyurl('login'));
    $main_smarty->display($the_template . '/pligg_content.tpl');
}else{
$main_smarty->display($the_template . '/pligg.tpl');
}
?>
											
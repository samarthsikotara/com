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
include_once(mnminclude.'news.php');
include_once(mnminclude.'test_brand.php');
include_once('global_variable.php');
include_once(mnminclude.'photo.php');
global $current_user,$db;

define('pagename', 'bawraas');

//set to true if this page has mobile template
$mobOpt=false;
//og-title
$main_smarty->assign('og_posttitle', '\'Tu Bawraa hai toh chal\' Contest');
//Site Title
$main_smarty->assign('posttitle', '\'Tu Bawraa hai toh chal\' Contest - by Shaukk - Bawraas with Mood Indigo\'13\'');
//met description
$main_smarty->assign('description', ' Calling all you passionate souls out there - \'Tu Bawraa hai toh chal\' Contest: In search of the next Bawraa - Someone who passionately pursues unconventional interests. 

Apply on http://shaukk.com/bawraas/ 

By Shaukk-Bawraas in association with Mood Indigo\'13.  ');
//og-description
$main_smarty->assign('og_content', 'Calling all you passionate souls out there - \'Tu Bawraa hai toh chal\' Contest: In search of the next Bawraa - Someone who passionately pursues unconventional interests. 

Apply on http://shaukk.com/bawraas/ 

By Shaukk-Bawraas in association with Mood Indigo\'13. ');
//og-image
$main_smarty->assign('postImage', 'http://shaukk.com/avatars/user_uploaded/1_original.jpg');

//echo "hi";
// sidebar
$main_smarty->assign('pagename', pagename);
$main_smarty->assign('bawraas_step', '2');
$main_smarty = do_sidebar($main_smarty);

$canIhaveAccess = 0;
$canIhaveAccess = $canIhaveAccess + checklevel('god');
$canIhaveAccess = $canIhaveAccess + checklevel('admin');

// If not logged in, redirect to the index page
/*if (!$current_user->authenticated)  header('Location: '.$my_base_url.$my_pligg_base."/login.php");
if (isset($_GET['login']))
	$login=$_GET['login'];
elseif(isset($_GET['id'])){
    if(is_numeric($_GET['id'])){$id=$_GET['id'];}
    else{header('Location: '.$my_base_url.$my_pligg_base);
	    die;
    }
}else if(isset($_REQUEST['title'])){
    $requestTitle=$_REQUEST['title'];
    if(isset($requestTitle)){$id = $db->get_var($sql="SELECT user_id FROM " . table_users . " WHERE `user_url` = '".$db->escape(sanitize($requestTitle,4))."';");}
}
elseif ($current_user->user_id > 0 && $current_user->authenticated)
	$login=$current_user->user_login;
else{
	header('Location: '.$my_base_url.$my_pligg_base);
	die;
}
*/
//include_once('create_result.php');

if ($current_user->user_id > 0 && $current_user->authenticated)
    $login=$current_user->user_login;
else{
    header('Location: '.getmyFullurl('bawraas'));
    die;
}

$breadcrumbs[0]['text']='Home »';
$breadcrumbs[0]['url']=getmyFullurl('index');
$breadcrumbs[1]['text']='Bawraas »';
$breadcrumbs[1]['url']=getmyFullurl('bawraas');
$main_smarty->assign('breadcrumbs', $breadcrumbs);
$user=new User();
$photo=new Photo();
if(isset($login))$user->username = $login;
$user->id=$current_user->user_id;
	//$user->id=27;
	if(!$user->read()) {
		echo "invalid user";
		die;
	}

    $sql = "SELECT * FROM `shbawaras` WHERE user_id = ".$user->id."";
    $results = $db->get_row($sql);

        $main_smarty->assign('user_names', $user->names);
        if(!is_null($results->user_id)){
            $main_smarty->assign('user_occupation', $results->occupation);
            $main_smarty->assign('user_place', $results->workplace);
            $main_smarty->assign('user_field1', $results->bawara_field1);
            $main_smarty->assign('user_field2', $results->bawara_field2);
            $main_smarty->assign('user_field3', $results->bawara_field3);
            $main_smarty->assign('user_field4', $results->bawara_field4);
        }else{
            $main_smarty->assign('user_occupation', 'P');
            $main_smarty->assign('user_place', '');
            $main_smarty->assign('user_field1', '');
            $main_smarty->assign('user_field2', '');
            $main_smarty->assign('user_field3', '');
            $main_smarty->assign('user_field4', '');
        }

        if(isset($_POST['reg_occupation'])) {
            $occupation=sanitize($_POST['reg_occupation'],3);
            $place=sanitize($_POST['reg_place'],3);
            $field1=sanitize($_POST['reg_desc1'],3);
            $field2=sanitize($_POST['reg_desc2'],3);
            $field3=sanitize($_POST['reg_desc3'],3);
            $field4=sanitize($_POST['reg_desc4'],3);
            $user_id=$user->id;

            if(!is_null($results->user_id)){
                $sql="UPDATE `shbawaras` SET occupation='".$occupation."', workplace='".$place."' ,bawara_field1='".$field1."' ,bawara_field2='".$field2."',bawara_field3='".$field3."',bawara_field4='".$field4."' WHERE user_id = ".$user_id."";
            }else{
                $sql="INSERT INTO `shbawaras` (user_id,occupation,workplace,bawara_field1,bawara_field2,bawara_field3,bawara_field4) VALUES (".$user_id.",'".$occupation."','".$place."','".$field1."','".$field2."','".$field3."','".$field4."')";
            }
            echo $sql;
            if($db->query($sql)){
                //header('Location: '.$my_base_url.$my_pligg_base.'/bawaras_sample.php');
                echo 'true';
            }else{
                if(!is_null($results->user_id)){
                    echo 'true';
                }else{
                    echo 'false';
                }

            }

        }

        $photos=$photo->getPhotosByBawaras($user->id,100);
        $i=0;
        if($photos){
            $main_smarty->assign('photos_count', count($photos));
            $main_smarty->assign('photos_count_left', count($photos));
            $main_smarty->assign('photos', $photos);
        }else{
            $main_smarty->assign('photos_count', 0);
        }
        $video=$db->get_row("SELECT * FROM ".table_videos." WHERE `user_id`=".$current_user->user_id." AND `active`=1", ARRAY_A);
        //print_r($video);
        if(count($video)>0){
            $main_smarty->assign('photos_count', count($photos)+1);
            $main_smarty->assign('video_upload', "false");
            $count_photos=count($photos);
            $photos[$count_photos]['id']="1584248721156";
            $photos[$count_photos]['type']="video";
            $photos[$count_photos]['url']=my_base_url.my_pligg_base."/images/video_thumn.png";
            $photos[$count_photos]['link_url']=$video['url'];
            $main_smarty->assign('photos', $photos);
        }else{
            $video_description ="Bawraas Entry";
            $video_title ="Bawraas Entry from ".$current_user->user_name ;
            include_once( 'get_youtube_token.php' );
            $videos_action=$response->url."?nexturl=".urlencode(my_base_url.my_pligg_base."/ajax.php?work=60");
            $videos_token=$response->token;
            $main_smarty->assign('token', $videos_token);
            $main_smarty->assign('videos_action', $videos_action);
            $video_title="'Tu bawraa hai to chal' contest entry from ".$current_user->user_name;
            $main_smarty->assign('video_upload', "true");
        }

            $main_smarty->assign('validationEngine','true');
            $main_smarty->assign('tpl_content','bawraas_step2.tpl');
            $main_smarty->assign('tpl_header', $the_template . '/header');
            $main_smarty->assign('tpl_center', $the_template . '/bawaras_main');

            $main_smarty->display($the_template . '/pligg.tpl');



?>
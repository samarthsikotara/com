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
include_once(mnminclude.'scribble.php');
include(mnminclude.'tags.php');
include(mnminclude.'user.php');
include_once(mnminclude.'user_fetch.php');
include(mnminclude.'csrf.php');
include(mnminclude.'interest.php');
include(mnminclude.'location.php');
include_once(mnminclude.'interest_member.php');
include_once(mnminclude.'plan_members.php');
include_once(mnminclude.'bookmark.php');
include_once(mnminclude."groups.php");
include_once(mnminclude."group.php");
include('global_variable.php');
$mobOpt=true;
include(mnminclude.'smartyvariables.php');

include_once(mnminclude."photo.php");


#ini_set('display_errors', 1);

check_referrer();

// sessions used to prevent CSRF
	$CSRF = new csrf();

// sidebar
$main_smarty = do_sidebar($main_smarty);

$canIhaveAccess = 0;
$canIhaveAccess = $canIhaveAccess + checklevel('god');
$canIhaveAccess = $canIhaveAccess + checklevel('admin');
 $canonical=false;
if (isset($_GET['login']))
	$login=$_GET['login'];
elseif(isset($_GET['id'])){
    if(is_numeric($_GET['id'])){$id=$_GET['id'];$canonical=true;}
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


// breadcrumbs and page title
$navwhere['text1'] = $main_smarty->get_config_vars('PLIGG_Visual_Breadcrumb_Profile');
$navwhere['link1'] = getmyurl('user2', $login, 'profile');
$navwhere['text2'] = $login;
$navwhere['link2'] = getmyurl('user2', $login, 'profile');
$navwhere['text3'] = $main_smarty->get_config_vars('PLIGG_Visual_Profile_ModifyProfile');
$navwhere['link3'] = getmyurl('profile', '');
$main_smarty->assign('navbar_where', $navwhere);

// read the users information from the database
$user=new User();
if(isset($login))$user->username = $login;
if(isset($id))$user->id=$id;

if(!$user->read()) {
	echo "invalid user";
	die;
}


	// uploading avatar
        if(isset($_POST["avatar"]) && sanitize($_POST["avatar"], 3) == "uploaded" && Enable_User_Upload_Avatar == true){

               if ($CSRF->check_valid(sanitize($_POST['token'], 3), 'profile_change')){

                $user->uploadAvatar($_FILES['image_file']);

               } else {
			echo 'An error occured while uploading your avatar.';
		}

	}


// display profile
if(isset($_REQUEST['edit_profile']) && $_REQUEST['edit_profile']=="true"){
    if(isset($_REQUEST['error']) && $_REQUEST['error']=="true"){
        $main_smarty->assign('error', "true");
    }
    $main_smarty->assign('name_error', "Please reveal your name and surname.");
    $main_smarty->assign('email_error', "Please enter a Valid Email ID");
    $main_smarty->assign('password_error', "Unsafe Password! Your password should contain at least 8 characters");
    $main_smarty->assign('verify_password_error', "Your passwords do not match! ");
    $main_smarty->assign('phone_error', "Please enter a correct mobile number.");
    $main_smarty->assign('dob_error', "Please enter a valid date");
    $main_smarty->assign('dob_error_child', "Children less than 13 years of age are not allowed to register. Sorry!");
    $main_smarty->assign('user_email', $user->email);
    $main_smarty->assign('user_names', $user->names);
    $main_smarty->assign('user_sex', $user->sex);
    if($user->phone=='0'){$user->phone='';}

    $main_smarty->assign('user_phone', substr($user->phone,3, strlen($user->phone)));
    $main_smarty->assign('user_dob', $user->dob);
    $main_smarty->assign('user_id', $user->id);
    $main_smarty->assign('user_desc', $user->user_desc);
    $main_smarty->assign('user_dob_month', date("m", strtotime($user->dob)));
    $main_smarty->assign('user_dob_date', date("d", strtotime($user->dob)));
    $main_smarty->assign('user_dob_year', date("Y", strtotime($user->dob)));
    $main_smarty->assign('URL_register', $_SERVER["PHP_SELF"]);
    define('pagename', 'profile_edit');
	$main_smarty->assign('pagename', pagename);
	$main_smarty->assign('validationEngine', "true");
    $main_smarty->assign('posttitle', $main_smarty->get_config_vars('PLIGG_Visual_Profile_ModifyProfile'));
    $main_smarty->assign('tpl_center', $the_template . '/profile_edit_center');
	$main_smarty->display($the_template . '/pligg.tpl');
}
else if(isset($_POST['save_profile'])  && sanitize($_POST['user_id'], 3) == $current_user->user_id){
    //die('posted');
    save_profile();
}
else show_profile();

function show_profile() {
	global $user, $main_smarty, $the_template, $CSRF,$current_user,$canonical ;
    //ECHO $user->sex;
    $keywords=$user->names.",";
    if($user->sex=="B")$user_type="brand";
    else $user_type="user";
    if(isset($_REQUEST['tab']) &&( $_REQUEST['tab']=="info" || $_REQUEST['tab']=="connections" || $_REQUEST['tab']=="interests" || $_REQUEST['tab']=="groups" || $_REQUEST['tab']=="locations")){
        $tab=$_REQUEST['tab'];
        if($tab=="info" && $user_type=="user")$tab="activities";
        $canonical=true;
    }else{
        if($user_type=="user"){
            $tab="activities";
        }else $tab="info";
    }

    //echo $user_type;
    $interest=interestedIn($user->id);
    $j=0;
    if(!count($interest)==0){
        foreach($interest as $item){
            $interestDetails[$j]=getInterestOtherDetails($item->interest_id);
            $keywords.=", ".$interestDetails[$j]['name'];
            $j++;
        }
    }
    $locations = getLocationListByUser($user->id);
    $i=0;
    //print_r($locations);
    if(!count($locations)==0){
        foreach($locations as $location){
            $locationDetails[$i]=getLocationDetails($location->location_id);
            $keywords.=", ".$locationDetails[$i]['name'];
            $i++;
        }
    }

    /*$photo=new Photo();
    $photos=$photo->photosByUserId($user->id);
    if($photos){
        $main_smarty->assign('photos', $photos);
        $main_smarty->assign('photos_count', count($photos));
    }else{
        $main_smarty->assign('photos_count', 0);
    }
    */

    //print_r($photos);

    $myGroup=get_grouplist_user($user->id);
    if(count($myGroup)>0){
        $i=0;
        foreach($myGroup as $result){
            $myGroup[$i]['image']=getGroupImage($result['group_id'], '100');
            $myGroup[$i]['image_small']=getGroupImage($result['group_id'], '50');
            $myGroup[$i]['image_large']=getGroupImage($result['group_id'], '250');
            $myGroup[$i]['url']=getmyurl('group_page',$result['group_id'] );
            $i++;
            $keywords.=", ".$result['group_name'];
        }
    }
    $main_smarty->assign('mygroups', $myGroup);
    $main_smarty->assign('groups_count', count($myGroup));
    $main_smarty->assign('stories_html', $user->storiesHtml(0,10));

	$CSRF->create('profile_change', true, true);

	// assign avatar source to smarty
    if($current_user->user_id==$user->id){$main_smarty->assign('isCurrentUser', true);}
	$main_smarty->assign('UseAvatars', do_we_use_avatars());
	$main_smarty->assign('interest_count', count($interest));
	$main_smarty->assign('locations',$locationDetails);
	$main_smarty->assign('location_count', count($locations));
	$main_smarty->assign('interest', $interestDetails);
    if($canonical){
        $main_smarty->assign('canonical_url',getmyFullurl('profiles', $user->url));
    }
	$main_smarty->assign('Avatar_ImgLarge', get_avatar('250', "", "", "", $user->id));
	$main_smarty->assign('Avatar_ImgSmall', get_avatar('big', $user->avatar_source, $user->username, $user->email));
	// module system hook

    $vars = '';
	check_actions('profile_show', $vars);
    //conection
    $isFriend=false;
    $friend=new Friend();
   // print_r($friend->getConnectionDetails($user->id));
    $status=$friend->getConnectionDetails($user->id);
    $isFollowing=$status['follower'];
    $isFriend=$status['friend'];
    //people user is following
    if($user_type=="user"){
        $friends = $friend->get_friends($user->id, 10);
        if(!count($friends)==0){
            $i=0;
            foreach($friends as $frand){
                  $conn_friend[$i]['user_id']=$frand['user_id'];
                  $conn_friend[$i]['avatar']=get_avatar('big', "", "", "", $frand['user_id']);
                  $conn_friend[$i]['url']=getmyFullurl('profileId',$frand['user_id'], '', '');
                  $conn_friend[$i]['name']=$frand['user_names'];
                  $i++;
            }
        }
        $main_smarty->assign('friend_count', count($conn_friend));
        $main_smarty->assign('friend', $conn_friend);
    }elseif($user_type=="brand"){
        $friends = $friend->get_friend_list_2($user->id, 10);
        if(!count($friends)==0){
        $i=0;
            foreach($friends as $frand){
              $conn_friend[$i]['user_id']=$frand['user_id'];
              $conn_friend[$i]['avatar']=get_avatar('big', "", "", "", $frand['user_id']);
              $conn_friend[$i]['url']=getmyFullurl('profileId',$frand['user_id'], '', '');
              $conn_friend[$i]['name']=$frand['user_names'];
              $i++;
            }
        }
        $main_smarty->assign('friend_count', count($conn_friend));
        $main_smarty->assign('friend', $conn_friend);
    }

    $main_smarty->assign('isFriend', $isFriend);
    if($isFollowing==1){
        $main_smarty->assign('isFollowing', "following");
    }else{
        $main_smarty->assign('isFollowing', "not following");
    }

   // print_r($conn_followed);
    // people who is following user
    $main_smarty->assign('friend_count', count($conn_friend));
	// assign profile information to smarty
    if($user->id!=$current_user->user_id){
        if(trim($user->user_desc)=="") $user->user_desc=getUserDefDesc($user->id);
    }else{
        if(trim($user->user_desc)=="") $user->user_desc="Write something about yourself...";
    }
	$main_smarty->assign('user_id', $user->id);
	$main_smarty->assign('user_email', $user->email);
	$main_smarty->assign('user_login', $user->username);
	$main_smarty->assign('user_names', $user->names);
	$main_smarty->assign('tab', $tab);
	$main_smarty->assign('user_sex', $user->sex);
	$main_smarty->assign('user_phone', $user->phone);
	$main_smarty->assign('user_dob', $user->dob);
	$main_smarty->assign('user_username', $user->username);
	$main_smarty->assign('user_type', $user_type);
	$main_smarty->assign('user_url', $user->url);
    if(strpos($user->names, " ")>0 && $user_type="user"){
        $main_smarty->assign('user_first_name', substr($user->names, 0, strpos($user->names, " ")));
    }
    else{
        $main_smarty->assign('user_first_name', $user->names);
    }
    $main_smarty->assign('user_publicemail', $user->public_email);
	$main_smarty->assign('user_location', $user->location);
	$main_smarty->assign('user_occupation', $user->occupation);
	$main_smarty->assign('user_language', !empty($user->language) ? $user->language : 'english');
	$main_smarty->assign('user_aim', $user->aim);
	$main_smarty->assign('user_msn', $user->msn);
	$main_smarty->assign('user_yahoo', $user->yahoo);
	$main_smarty->assign('user_gtalk', $user->gtalk);
	$main_smarty->assign('user_skype', $user->skype);
	$main_smarty->assign('user_irc', $user->irc);
	$main_smarty->assign('user_karma', $user->karma);
	$main_smarty->assign('user_joined', get_date($user->date));
	$main_smarty->assign('user_desc', $user->user_desc);
	$main_smarty->assign('user_avatar_source', $user->avatar_source);
	$user->all_stats();
	$main_smarty->assign('user_total_links', $user->total_links);
	$main_smarty->assign('edit_profile_link', my_pligg_base."/profile.php?edit_profile=true");
	$main_smarty->assign('user_published_links', $user->published_links);
	$main_smarty->assign('user_total_comments', $user->total_comments);
	$main_smarty->assign('user_total_votes', $user->total_votes);
	$main_smarty->assign('user_published_votes', $user->published_votes);


	$languages = array();
	$files = glob("languages/*.conf");
	foreach ($files as $file)
	    if (preg_match('/lang_(.+?)\.conf/',$file,$m))
		$languages[] = $m[1];
	$main_smarty->assign('languages', $languages);

	// pagename
	define('pagename', 'profile');
	$main_smarty->assign('pagename', pagename);

	$main_smarty->assign('form_action', $_SERVER["PHP_SELF"]);

	// show the template
    $main_smarty->assign('posttitle', $user->names." Profile");
    $main_smarty->assign('postImage', get_avatar('250', "", "", "", $user->id));
    $meta_desc = $user->names." · ".strip_tags($user->user_desc) ." · Sign Up for Shaukk to connect and join events with ".$user->names." ";
    if(count($conn_friend)>0)$meta_desc.=" · ".count($conn_friend)." Friends";
    $meta_desc = sanitize($meta_desc, 2);
    $main_smarty->assign('metaDescription', $meta_desc );
    $keywords.="";
    $main_smarty->assign('metaKeywords', sanitize($keywords, 2)  );
    $main_smarty->assign('og_content', $meta_desc );
	$main_smarty->assign('tpl_center', $the_template . '/profile_new_center');
	$main_smarty->display($the_template . '/pligg.tpl');
}

function save_profile() {
	global $user, $current_user, $db, $main_smarty, $CSRF, $canIhaveAccess, $language;
        if($user->sex=="M" || $user->sex=="F"){
            $sex=$_POST['reg_sex'];
            if($sex!='M' && $sex!='F'){
                $error_text=$main_smarty->get_config_vars('PLIGG_Visual_Register_Error_Sex');
                $error=true;
            }
        }else{
            $sex="B";
        }
        $date=$_POST['reg_date'];
        $month=$_POST['reg_month'];
        $year=$_POST['reg_year'];
        $phone="91-".$_POST['reg_phone_prefix'];
        $name=sanitize($_POST['reg_nam'], 3);
        $desc=sanitize($_POST['reg_desc'], 3);
        $error=false;

        if(!checkdate($month, $date, $year)){
            $error_text=$main_smarty->get_config_vars('PLIGG_Visual_Register_Error_Dob');
            $error=true;
        }
        if(!isValidPhone($phone)){
            $error_text=$main_smarty->get_config_vars('PLIGG_Visual_Register_Error_Phone');
            $error=true;
        }

        if($error==false){
                $dob=$year."-".$month."-".$date;
                $user->names=$name;
                $user->dob=$dob;
                $user->sex=$sex;
                $user->phone=$phone;
                $user->user_desc=$desc;

                // module system hook
                $vars = '';
                check_actions('profile_save', $vars);
                $user->store();
                $user->read();
                header("Location: ".my_pligg_base."/profile.php");
        }else{

                header("Location: ".my_pligg_base."/profile.php?edit_profile=true&error=true");

        }


}

?>

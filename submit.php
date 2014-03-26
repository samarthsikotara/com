<?php

include_once('Smarty.class.php');
$main_smarty = new Smarty;
include_once('config.php');
include(mnminclude.'html1.php');
include(mnminclude.'link.php');
include(mnminclude.'tags.php');
include(mnminclude.'user.php');
include_once(mnminclude.'user_fetch.php');
include(mnminclude.'search.php');
include_once(mnminclude.'interest_member.php');
include(mnminclude.'plan_members.php');
include(mnminclude.'interest.php');
include(mnminclude.'location.php');

include('global_variable.php');
$mobOpt=true;
include(mnminclude.'smartyvariables.php');
include_once(mnminclude."facebookapi.php");
include_once(mnminclude.'group.php');
include_once(mnminclude.'groups.php');

if (!$_COOKIE['referrer'])
    check_referrer();

// html tags allowed during submit
if (checklevel('god'))
    $Story_Content_Tags_To_Allow = Story_Content_Tags_To_Allow_God;
elseif (checklevel('admin'))
    $Story_Content_Tags_To_Allow = Story_Content_Tags_To_Allow_Admin;
else
    $Story_Content_Tags_To_Allow = Story_Content_Tags_To_Allow_Normal;
$main_smarty->assign('Story_Content_Tags_To_Allow', htmlspecialchars($Story_Content_Tags_To_Allow));

// breadcrumbs and page titles
$navwhere['text1'] = $main_smarty->get_config_vars('PLIGG_Visual_Breadcrumb_Submit');
$navwhere['link1'] = getmyurl('submit', '');
$main_smarty->assign('navbar_where', $navwhere);
$main_smarty->assign('posttitle', "Create a Plan");
$main_smarty = do_sidebar($main_smarty);

//to check anonymous mode activated

if(!$current_user->authenticated){force_authentication();}

//die('abc');

/*
if ($vars['anonymous_story'] == true)
{
	$anonymous_userid = $db->get_row("SELECT user_id from " . table_users . " where user_login = 'anonymous' ");
	$anonymous_user_id = $anonymous_userid->user_id;
	//echo "val".$anonymous_user_id;
}
*/
// module system hook
$vars = '';
check_actions('submit_post_authentication', $vars);

// this is for direct links from weblogs
if(empty($_POST['phase']) && (!empty($_GET['url']) || is_numeric($_GET['id']))) {
	$_POST['phase'] = 1;
	if(!empty($_GET['url']))
	{
	    $_POST['url'] = $_GET['url'];
	}
	else
	{
	    $row = $db->get_row("SELECT * FROM ".table_links." WHERE link_id='".$db->escape($_GET['id'])."' AND link_author='{$current_user->user_id}'",ARRAY_A);
	    if (!$row['link_id'])
	    {
		define('pagename', 'submit');
		$main_smarty->assign('pagename', pagename);
        $main_smarty->assign('validationEngine', 'true');
		$main_smarty->assign('submit_error', 'badkey');
		$main_smarty->assign('tpl_center', $the_template . '/submit_errors');
		$main_smarty->display($the_template . '/pligg.tpl');
		die();
	    }
	    $_POST['url'] = $row['link_url'];
	}
	    $_POST['randkey'] = rand(10000,10000000);
	if(!empty($_GET['trackback']))
	    $_POST['trackback'] = $_GET['trackback'];
}

// determine which step of the submit process we are on
$phase = isset($_POST["phase"]) && is_numeric($_POST["phase"]) ? $_POST["phase"] : 0;

// If show URL input box is disabled, go straight to step 2
if($phase == 0 && Submit_Show_URL_Input == false) {
	$phase = 1;
}
switch ($phase) {
	case 0:
		do_submit0();
		break;
	case 1:
		do_submit1();
		break;
	case 2:
		do_submit2();
		break;
	case 3:
		do_submit3();
		break;
}

exit;

// enter URL before submit process
function do_submit0() {
	global $main_smarty, $the_template;
	$main_smarty->assign('submit_rand', rand(10000,10000000));
	$main_smarty->assign('Submit_Show_URL_Input', Submit_Show_URL_Input);
	$main_smarty->assign('Submit_Require_A_URL', Submit_Require_A_URL);

	define('pagename', 'submit');
	$main_smarty->assign('pagename', pagename);
    $main_smarty->assign('validationEngine', 'true');

	$main_smarty->assign('tpl_center', $the_template . '/submit_step_1');
	$vars = '';
	check_actions('do_submit0', $vars);
	$main_smarty->display($the_template . '/pligg.tpl');
}

// submit step 1
function do_submit1() {

	global $main_smarty, $db, $dblang, $current_user, $the_template, $page_size;
    $userDetails=getUserDetailsSmall($current_user->user_id);
   // print_r($userDetails);
    $search=new Search();
    $search->offset = (get_current_page()-1)*$page_size;

    // pagesize set in the admin panel
    $search->pagesize = $page_size;

    // since this is index, we only want to view "published" stories
    $search->filterToStatus = "published";

    $search->doSearch();
    $linksum_count = $search->countsql;
    $linksum_sql = $search->sql;
    $fetch_link_summary = true;
    $summary_type="short";
    include('./libs/link_summary.php'); // this is the code that show the links / stories

    $i=0;
    $interests=interestedIn($current_user->user_id);
    if(count($interests)!=0){
      foreach($interests as $item){
          if($i>11)break;
          $interest_list[$i]['id']=$item->interest_id;
          $interest_list[$i]['image']=getInterestImage($item->interest_id, '85');
          $interest_list[$i]['name']=$item->interest_name;
          $interest_list[$i]['interested']=true;
          $check[$item->interest_id]=$item->interest_id;
          //echo $check[$item->interest_id]."<br>";
          $i++;
      }
    }

    $otherInterests=getInterestList(12);
    //print_r($otherInterests);
    if(count($otherInterests)!=0){
        foreach($otherInterests as $item){
            //print_r($item);
            if($i>11){break;}
            if($check[$item->interest_id]!=$item->interest_id){
                  $interest_list[$i]['id']=$item->interest_id;
                  $interest_list[$i]['image']=getInterestImage($item->interest_id, '85');
                  $interest_list[$i]['name']=$item->interest_name;
                  $interest_list[$i]['interested']=false;
                  $i++;
            }
        }
    }

    //print_r($interest_list);

    $locations=preferredLocations();
    $i=0;
    $groups=get_grouplist_user($current_user->user_id);
    $hasGroup="true";
    if(count($groups)<1) $hasGroup="false";
    foreach($locations as $location){
        $locationArray=explode("," ,$location->link_field3);
        $locationItem[$i]['short']=$locationArray[0];
        $locationItem[$i]['full']=$location->link_field3;
        $i++;

    }
    //To do: to add the interest to his like list if its not there already

    $isGroupPlan="false";
    if(isset($_REQUEST['group_id'])  && is_numeric($_REQUEST['group_id'])){
        if(isMemberActive($_REQUEST['group_id'], $current_user->user_id)){
            $isGroupPlan="true";
            $group_id=$_REQUEST['group_id'];
            $main_smarty->assign('group_id', $group_id);
        }
    }
    $main_smarty->assign('isGroupPlan', $isGroupPlan);


    $main_smarty->assign('interest_list', $interest_list);
    $main_smarty->assign('groups', $groups);
    $main_smarty->assign('hasGroup', $hasGroup);
    $main_smarty->assign('user_name', $userDetails->user_names);
    $main_smarty->assign('location_list', $locationItem);
    $main_smarty->assign('plan_entries', 11);

    $url = htmlspecialchars(sanitize($_POST['url'], 3));
	$url = str_replace('&amp;', '&', $url);
	$url = html_entity_decode($url);
    $linkres=new Link;
	$linkres->randkey = sanitize($_POST['randkey'], 3);
    if(Submit_Show_URL_Input == false) {
		$url = "http://";
		$linkres->randkey = rand(10000,10000000);
	}

	$Submit_Show_URL_Input = Submit_Show_URL_Input;
	if($url == "http://" || $url == ""){
		$Submit_Show_URL_Input = false;
	}

	$edit = false;
	if (is_numeric($_GET['id']))
	{
	    $linkres->id = $_GET['id'];
	    $linkres->read(FALSE);
	    $trackback=$_GET['trackback'];
	}

	else
	{
	    $linkres->get($url);
   	    if ($_POST['title'])
	    	$linkres->title = stripslashes(sanitize($_POST['title'], 4, $Story_Content_Tags_To_Allow));
	    if ($_POST['tags'])
	    	$linkres->tags = stripslashes(sanitize($_POST['tags'], 4));
	    if ($_POST['description'])
	    	$linkres->content = stripslashes(sanitize($_POST['description'], 4, $Story_Content_Tags_To_Allow));

	    if ($_POST['category'])
	    {
		$cats = explode(',',$_POST['category']);
		foreach ($cats as $cat)
		    if ($cat_id = $db->get_var("SELECT category_id FROM ".table_categories." WHERE category_name='".$db->escape(trim($cat))."'"))
		    {
			$linkres->category = $cat_id;
			break;
		    }
	    }
	    $trackback=$linkres->trackback;
	}

	$main_smarty->assign('randkey', $linkres->randkey);
	$main_smarty->assign('submit_url', $url);
	$data = parse_url($url);
	$main_smarty->assign('url', $url);
	$main_smarty->assign('url_short', 'http://'.$data['host']);
	$main_smarty->assign('Submit_Show_URL_Input', $Submit_Show_URL_Input);
	$main_smarty->assign('Submit_Require_A_URL', Submit_Require_A_URL);


	if($url == "http://" || $url == ""){
		if(Submit_Require_A_URL == false){
			$linkres->valid = true;}
		else{
			$linkres->valid = false;
		}
		$linkres->url_title = "";
	}

	$vars = array("url" => $url,'linkres'=>$linkres);
	check_actions('submit_validating_url', $vars);
	$linkres = $vars['linkres'];

	if(!$linkres->valid) {
		$main_smarty->assign('submit_error', 'invalidurl');
		$main_smarty->assign('tpl_center', $the_template . '/submit_errors');

		$main_smarty->display($the_template . '/pligg.tpl');
		return;
	}

	if(Submit_Require_A_URL == true || ($url != "http://" && $url != "")){
		if(!is_numeric($_GET['id']) && $linkres->duplicates($url) > 0) {
			$main_smarty->assign('submit_search', getmyurl("search_url", htmlentities($url)));
			$main_smarty->assign('submit_error', 'dupeurl');
			$main_smarty->assign('tpl_center', $the_template . '/submit_errors');

			define('pagename', 'submit');
		    $main_smarty->assign('pagename', pagename);
            $main_smarty->assign('validationEngine', 'true');

			$main_smarty->display($the_template . '/pligg.tpl');
			return;
		}
	}

	$vars = array("url" => $url);
	check_actions('submit_validating_url', $vars);

	totals_adjust_count('discard', 1);
	//echo 'id'.$current_user->user_id;
	$linkres->status='discard';
	$linkres->author=$current_user->user_id;
	//$linkres->store();

    // showing suggested plans

	$main_smarty->assign('StorySummary_ContentTruncate', StorySummary_ContentTruncate);
	$main_smarty->assign('SubmitSummary_Allow_Edit', SubmitSummary_Allow_Edit);
	$main_smarty->assign('enable_tags', Enable_Tags);
	$main_smarty->assign('submit_url_title', str_replace('"',"&#034;",$linkres->url_title));
	$main_smarty->assign('submit_url_description', $linkres->url_description);
	$main_smarty->assign('submit_id', $linkres->id);
	$main_smarty->assign('submit_type', $linkres->type());
	if(isset($link_title)){$main_smarty->assign('submit_title', $link_title);}
	if(isset($link_content)){$main_smarty->assign('submit_content', $link_content);}
	$main_smarty->assign('submit_trackback', $trackback);
	$main_smarty->assign('submit_link_field1', $linkres->link_field1);
	$main_smarty->assign('submit_link_field2', $linkres->link_field2);
	$main_smarty->assign('submit_link_field3', $linkres->link_field3);
	$main_smarty->assign('submit_link_field4', $linkres->link_field4);
	$main_smarty->assign('submit_link_field5', $linkres->link_field5);
	$main_smarty->assign('submit_link_field6', $linkres->link_field6);
	$main_smarty->assign('submit_link_field7', $linkres->link_field7);
	$main_smarty->assign('submit_link_field8', $linkres->link_field8);
	$main_smarty->assign('submit_link_field9', $linkres->link_field9);
	$main_smarty->assign('submit_link_field10', $linkres->link_field10);
	$main_smarty->assign('submit_link_field11', $linkres->link_field11);
	$main_smarty->assign('submit_link_field12', $linkres->link_field12);
	$main_smarty->assign('submit_link_field13', $linkres->link_field13);
	$main_smarty->assign('submit_link_field14', $linkres->link_field14);
	$main_smarty->assign('submit_link_field15', $linkres->link_field15);
	$main_smarty->assign('submit_link_group_id', $linkres->link_group_id);

//	$main_smarty->assign('submit_id', $_GET['id']);
	$main_smarty->assign('submit_title', $linkres->title);
	$main_smarty->assign('submit_content', str_replace("<br />", "\n", $linkres->content));
	$main_smarty->assign('storylen', utf8_strlen(str_replace("<br />", "\n", $linkres->content)));
	$main_smarty->assign('submit_summary', $linkres->link_summary);
	$main_smarty->assign('submit_group', $linkres->link_group_id);
	$main_smarty->assign('submit_category', $linkres->category);
	$main_smarty->assign('tags_words', $linkres->tags);

	include_once(mnminclude.'dbtree.php');
	$array = tree_to_array(0, table_categories, FALSE);

	$array = array_values(array_filter($array, "allowToAuthorCat"));

	$main_smarty->assign('submit_lastspacer', 0);
	$main_smarty->assign('submit_cat_array', $array);

	/*include_once(mnminclude.'group.php');
	$group_arr=array();
	$group_arr = get_groupdetail_user();
	//echo "group".print_r($group_arr);
	$main_smarty->assign('submit_group_array', get_groupdetail_user());*/

	//to display group drop down
	if(enable_group == "true")
	{
		$output = '';
		$group_membered = $db->get_results("SELECT group_id,group_name FROM " . table_groups . "
							LEFT JOIN ".table_group_member." ON member_group_id=group_id
							WHERE member_user_id = $current_user->user_id AND group_status = 'Enable' AND member_status='active'
							ORDER BY group_name ASC");
		if ($group_membered)
		{
			$output .= "<select name='link_group_id'>";
			$output .= "<option value = ''>".$main_smarty->get_config_vars('PLIGG_Visual_Group_Select_Group')."</option>";
			foreach($group_membered as $results)
			{
				$output .= "<option value = ".$results->group_id. ($linkres->link_group_id ? ' selected' : '') . ">".$results->group_name."</option>";
			}
			$output .= "</select>";
		}
		$main_smarty->assign('output', $output);
	}
	if($current_user->authenticated != TRUE){
		$vars = '';
		check_actions('register_showform', $vars);
	}

	$main_smarty->assign('Spell_Checker', Spell_Checker);

	$main_smarty->assign('tpl_extra_fields', $the_template . '/submit_extra_fields');
	$main_smarty->assign('tpl_center', $the_template . '/submit_plan');

	define('pagename', 'submit');
	$main_smarty->assign('pagename', pagename);
    $main_smarty->assign('validationEngine', 'true');

	$vars = '';
	check_actions('do_submit1', $vars);
	$_SESSION['step'] = 1;

	$main_smarty->display($the_template . '/pligg.tpl');
}

// submit step 2
function do_submit2() {

	global $db, $main_smarty, $dblang, $the_template, $linkres, $current_user, $Story_Content_Tags_To_Allow;
	$main_smarty->assign('auto_vote', auto_vote);
	$main_smarty->assign('Submit_Show_URL_Input', Submit_Show_URL_Input);
	$main_smarty->assign('Submit_Require_A_URL', Submit_Require_A_URL);
	$main_smarty->assign('link_id', sanitize($_POST['id'], 3));
	define('pagename', 'submit');
	$main_smarty->assign('pagename', pagename);
    $main_smarty->assign('validationEngine', 'true');

	if($current_user->authenticated != TRUE){
		$vars = array('username' => $current_user->user_login);
		check_actions('register_check_errors', $vars);
	}
	check_actions('submit2_check_errors', $vars);
	if($vars['error'] == true){
		if (link_catcha_errors('captcha_error')) {
			return;
		}
	}else
	{

	$linkres=new Link;
	$linkres->randkey=rand(10000,10000000);
    $linkres->status="published";
    //if($_SESSION['step']!=1)die('Wrong step');

	$linkres->read(FALSE);
	$linkres->category=1;

    //TODO : Sanitize the post Data

	$linkres->title = sanitize(substr($_POST['plan-title'], 0 ,40) , 3) ;
	$linkres->title_url = makeUrlFriendly($linkres->title);
	$linkres->link_summary = close_tags(stripslashes(sanitize($_POST['plan-desc'], 4, $Story_Content_Tags_To_Allow)));
	$linkres->content = str_replace("\n", "<br />", $linkres->content);
	// Steef 2k7-07 security fix start ----------------------------------------------------------
	if(is_numeric($_POST['plan-interest']) && interestExists($_POST['plan-interest'])){$linkres->link_field1=$_POST['plan-interest'];}else die('Invalid Interest Given');
	if(!is_numeric($_POST['plan-time'])) die('error');

    $linkres->link_field2=date('Y-m-d H:i:s', $_POST['plan-time']);
    if(time()>$_POST['plan-time']){
        header('Location: 404error.php?error=Plan_date_invalid');
    }
	$linkres->link_field3=$_POST['plan-place'];
    //echo $_POST['plan-prom-link'];
    //print_r($_FILES);
    if(isset($_FILES['plan-prom-banner']) && $_FILES['plan-prom-banner']['error']==0){
        $path="images/banner/";
        $name = $_FILES['plan-prom-banner']['name'];
        $size = $_FILES['plan-prom-banner']['size'];
        if(strlen($name))
        {
            if($size<(10*1024*1024)) // Image size max 2 MB
            {
                $ext = pathinfo($name, PATHINFO_EXTENSION);
                $random = md5(rand(1000000, 10000000));
                $allowedFileTypes = array("jpeg","jpg","gif","png",'pjpeg');
                if(!in_array($ext,$allowedFileTypes)){
                             die('Only these file types are allowed : jpeg, gif, png');
                }
                $actual_image_name = $random.".".strtolower($ext);
                $tmp = $_FILES['plan-prom-banner']['tmp_name'];
                echo $path.$actual_image_name;
                if(move_uploaded_file($tmp, $path.$actual_image_name))
                {
                    $linkres->link_field13=$actual_image_name;
                }

            }else
                die("Image file size max 10 MB");
        }
    }

    if(isset($_POST['plan-prom-link']) && $_POST['plan-prom-link']!=""){
        $_POST['plan-prom-link']="http://".$_POST['plan-prom-link'];

        if(isvalidUrl($_POST['plan-prom-link'])){
            $linkres->link_field12=urlencode($_POST['plan-prom-link']);
        }
    }
    $linkres->link_field4=floatval($_POST['plan-place-lat']);
	$linkres->link_field5=floatval($_POST['plan-place-lng']);
    if($_POST['plan-closing-time-minute']=="00")$_POST['plan-closing-time-minute']=0;
    if($_POST['plan-closing-time-hour']=="00")$_POST['plan-closing-time-hour']=12;

    if(strtotime($_POST['plan-end-date']) && is_numeric($_POST['plan-end-time-hour']) && ($_POST['plan-end-time-hour']>0 || $_POST['plan-end-time-hour']<13) && is_numeric($_POST['plan-end-time-minute']) && ($_POST['plan-end-time-minute']>=0 || $_POST['plan-end-time-minute']<60) && ($_POST['plan-end-time-am-pm']=="am" || $_POST['plan-end-time-am-pm']=="pm")){
        $linkres->link_field14=date('Y-m-d H:i:s', strtotime($_POST['plan-end-date']." ".$_POST['plan-end-time-hour'].":".$_POST['plan-end-time-minute'].":00 ".$_POST['plan-end-time-am-pm']));
        if(strtotime($linkres->link_field14)<strtotime($linkres->link_field2))$linkres->link_field14=$linkres->link_field2;
    }else{
        $linkres->link_field14=$linkres->link_field2;
    }
    if(strtotime($_POST['plan-closing-date']) && is_numeric($_POST['plan-closing-time-hour']) && ($_POST['plan-closing-time-hour']>0 || $_POST['plan-closing-time-hour']<13) && is_numeric($_POST['plan-closing-time-minute']) && ($_POST['plan-closing-time-minute']>=0 || $_POST['plan-closing-time-minute']<60) && ($_POST['plan-close-time-am-pm']=="am" || $_POST['plan-close-time-am-pm']=="pm")){
        $linkres->link_field6=date('Y-m-d H:i:s', strtotime($_POST['plan-closing-date']." ".$_POST['plan-closing-time-hour'].":".$_POST['plan-closing-time-minute'].":00 ".$_POST['plan-close-time-am-pm']));
        if(strtotime($linkres->link_field6)>strtotime($linkres->link_field14))$linkres->link_field6=$linkres->link_field14;
        elseif(strtotime($linkres->link_field6)<time())$linkres->link_field6=$linkres->link_field2;

    }else{
        $linkres->link_field6=$linkres->link_field14;
    }
	//$linkres->link_field7=$_POST['plan-freq'];
	if(is_numeric($_POST['plan-entries-public']))$linkres->link_field8=$_POST['plan-entries-public'];
    else{$linkres->link_field8=100;}
	if(is_numeric($_POST['plan-entries-private']))$linkres->link_field9=$_POST['plan-entries-private'];
    //$plan_type=array("Friendly", "Skill", "Tournament", "Screening", "Training", "Dream Run", "Half", "Full", "Fun", "Competition", "Fashion Show", "Consultation", "Tips", "Body Relaxation", "Body Art", "Hair Styling", "Parlor Visits", "Course", "Beach", "Culture", "Hill Station", "Monsoon", "Wildlife", "Adventure", "Museums", "Historiacal Monuments", "Cooking Club", "Show", "Birthday", "Festive", "Kitty Party", "Pub Hopping", "Dance Party", "Night-out", "Theme", "Misc", "Karaoke", "Jamming", "Concert", "Performance", "Gallery Exhibition", "Exhibition", "Club", "Workshop", "Programme", "Book Club", "Library", "Mentoring", "Drives", "Networking", "Entrepreneurship", "Event Management", "Conferences", "Politics", "Leadership");
    //echo 'SELECT `interest_type` FROM '.table_interests_type." WHERE `interest_id`=".$linkres->link_field1." AND `interest_flag`='Type'";
    $plan_type=$db->get_results('SELECT `interest_type` FROM '.table_interests_type." WHERE `interest_id`=".$linkres->link_field1, ARRAY_N);
   // print_r($plan_type);
    foreach($plan_type as $type){
        if(trim($type[0])==trim($_POST['plan-type'])){$linkres->link_field10=$_POST['plan-type']; break;}
    }
    if(isset($_POST['plan-privacy']) && ($_POST['plan-privacy']=="public" || $_POST['plan-privacy']=="private" || $_POST['plan-privacy']=="invite" || $_POST['plan-privacy']=="group")){
	    $linkres->link_field11=$_POST['plan-privacy'];
    }
    else{
        $linkres->link_field11="public";
    }
    $linkres->debug=false;

    if($linkres->link_field11=="group"){
        if(is_array($_REQUEST['select-plan-group'])){
            $i=0;
            foreach($_REQUEST['select-plan-group'] as $group){
                $groups[$i]['group_id']=$group;
                $i++;
            }
            $linkres->groups=$groups;
        }


    }
    //print_r($linkres->groups);

    //print_r($linkres);

		//get link_group_id
	//print_r($_POST);
    //die();
        $linkres->author=$current_user->user_id;

        $linkres->store();


    //echo "1";
    try{joinPlan($linkres->id, 'public');}
    catch (Exception $e){
        error_log($e->getMessage());
    }
    postCreated(getmyFullurl('plan', $linkres->id), $linkres->link_field3,date('m/d/Y H:i:s', strtotime($linkres->link_field6)),date('m/d/Y H:i:s', strtotime($linkres->link_field6)));
    //$linkres->id=3000;
       // echo "2";
        if(!isInterested($linkres->link_field1)){
      addinterest($linkres->link_field1, "public");
    }
        //echo "3";
        //print_r($_POST['invFriend']);
    if($linkres->link_field11=="public" || $linkres->link_field11=="private"  || $linkres->link_field11=="invite" ){
        if(!empty($_POST['invFriend'])){
            include_once(mnminclude."notification.php");
            $inv=$_POST['invFriend'];
            for($i=0; $i<count($inv); $i++){
                //echo $inv[$i]."..";
                if(is_numeric($inv[$i])){
                    invite($inv[$i], $linkres->id, false);
                }else if(check_email($inv[$i])){
                    invite_guest($inv[$i], $linkres->id);
                }
            }
        }
        //die('suarv');
       // addPlanInviteNotifiocation($linkres->id, $inv);
    }
        //echo "4";
    //die(getmyurl('plan', $linkres->id));
    $planUrl=getmyFullurl('plan', $linkres->id);
        header("Location: ".$planUrl);
    ?>
<script type="text/javascript">
    window.location= "<?php echo $planUrl; ?>";
</script>
<?php
    //print_r($linkres);
    die();
	tags_insert_string($linkres->id, $dblang, $linkres->tags);

	if (link_errors($linkres)) {
		return;
	}

		//comment subscription
		if(isset($_POST['comment_subscription']))
		{

			$vars = array('link_id' => $linkres->id);
			check_actions('comment_subscription_insert_function', $vars);
		}
		//comment subscription
		if(isset($_POST['timestamp_date_day']))
		{
			//open date
			$timestamp_date_day = $_POST['timestamp_date_day'];
			$timestamp_date_month = $_POST['timestamp_date_month'];
			$timestamp_date_year = $_POST['timestamp_date_year'];
			if (!is_numeric($timestamp_date_day) || !is_numeric($timestamp_date_month) || !is_numeric($timestamp_date_year))
				$timestamp_date = date("m-d-Y");
			else
				$timestamp_date = $timestamp_date_month."-".$timestamp_date_day."-".$timestamp_date_year;

			$vars = array('link_id' => $linkres->id);
			$vars = array('timestamp_date' => $timestamp_date,'link_id' => $linkres->id);
			check_actions('comment_subscription_insert_function', $vars);
		}

	$vars = '';
	check_actions('submit_step_3_after_first_store', $vars);
	$linkres->read(FALSE);
	$edit = true;
	$link_title = $linkres->title;
	$link_content = $linkres->content;
	$link_title = stripslashes(sanitize($_POST['title'], 3));
	$main_smarty->assign('the_story', $linkres->print_summary('full', true));

	$main_smarty->assign('tags', $linkres->tags);
	if (!empty($linkres->tags)) {
		$tags_words = str_replace(",", ", ", $linkres->tags);
		$tags_url = urlencode($linkres->tags);
		$main_smarty->assign('tags_words', $tags_words);
		$main_smarty->assign('tags_url', $tags_url);
	}

	if(isset($url)){
		$main_smarty->assign('submit_url', $url);
	} else {
		$main_smarty->assign('submit_url', '');
	}
	$data = parse_url($linkres->url);
	$main_smarty->assign('url_short', $data['host']);
	$main_smarty->assign('submit_url_title', $linkres->url_title);
	$main_smarty->assign('submit_id', $linkres->id);
	$main_smarty->assign('submit_type', $linkres->type());
	$main_smarty->assign('submit_title', $link_title);
	$main_smarty->assign('submit_content', $link_content);
	if(isset($trackback)){
		$main_smarty->assign('submit_trackback', $trackback);
	} else {
		$main_smarty->assign('submit_trackback', '');
	}

	$main_smarty->assign('tpl_extra_fields', $the_template . '/submit_extra_fields');
	$main_smarty->assign('tpl_center', $the_template . '/submit_step_3');


	$vars = '';
	check_actions('do_submit2', $vars);
	$_SESSION['step'] = 2;
	if (Submit_Complete_Step2)
	    do_submit3();
	else
	    $main_smarty->display($the_template . '/pligg.tpl');
	}
}

// submit step 3
function do_submit3() {
	global $db;

	$linkres=new Link;
	$linkres->id = sanitize($_POST['id'], 3);
	if(!is_numeric($linkres->id))die();

	//if(!Submit_Complete_Step2 && $_SESSION['step']!=2)die('Wrong step');
	$linkres->read();

	totals_adjust_count($linkres->status, -1);
	totals_adjust_count('queued', 1);

	$linkres->status='queued';

	$vars = array('linkres'=>&$linkres);
	check_actions('do_submit3', $vars);

	if ($vars['linkres']->status=='discard')
	{
		$vars = array('link_id' => $linkres->id);
		check_actions('story_discard', $vars);
	}
	elseif ($vars['linkres']->status=='spam')
	{
		$vars = array('link_id' => $linkres->id);
		check_actions('story_spam', $vars);
	}


	$linkres->store_basic();
	$linkres->check_should_publish();

	if(isset($_POST['trackback']) && sanitize($_POST['trackback'], 3) != '') {
		require_once(mnminclude.'trackback.php');
		$trackres = new Trackback;
		$trackres->url=sanitize($_POST['trackback'], 3);
		$trackres->link=$linkres->id;
		$trackres->title=$linkres->title;
		$trackres->author=$linkres->author;
		$trackres->content=$linkres->content;
		$res = $trackres->send();
	}

	$vars = array('linkres'=>$linkres);
	check_actions('submit_pre_redirect', $vars);
	if ($vars['redirect'])
	    header('Location: '.$vars['redirect']);
	elseif($linkres->link_group_id == 0)
		header("Location: " . getmyurl('upcoming'));
	else
	{
		$redirect = getmyurl("group_story", $linkres->link_group_id);
		header("Location: $redirect");
	}
	die;
}

// assign any errors found during submit
function link_errors($linkres)
{
	global $main_smarty, $the_template, $cached_categories;
	$error = false;

	if(sanitize($_POST['randkey'], 3) !== $linkres->randkey) { // random key error
		$main_smarty->assign('submit_error', 'badkey');
		$error = true;
	}
	if($linkres->status != 'discard' && $linkres->status != 'draft') { // if link has already been submitted
		$main_smarty->assign('submit_error', 'hashistory');
		$main_smarty->assign('submit_error_history', $linkres->status);
		$error = true;
	}
	$story = preg_replace('/[\s]+/',' ',strip_tags($linkres->content));
	if(utf8_strlen($linkres->title) < minTitleLength  || utf8_strlen($story) < minStoryLength ) {
		$main_smarty->assign('submit_error', 'incomplete');
		$error = true;
	}
	if(utf8_strlen($linkres->title) > maxTitleLength) {
		$main_smarty->assign('submit_error', 'long_title');
		$error = true;
	}
  	if (utf8_strlen($linkres->content) > maxStoryLength ) {
		$main_smarty->assign('submit_error', 'long_content');
		$error = true;
	}
	if(utf8_strlen($linkres->tags) > maxTagsLength) {
		$main_smarty->assign('submit_error', 'long_tags');
		$error = true;
	}
  	if (utf8_strlen($linkres->summary) > maxSummaryLength ) {
		$main_smarty->assign('submit_error', 'long_summary');
		$error = true;
	}
	if(preg_match('/.*http:\//', $linkres->title)) { // if URL is found in link title
		$main_smarty->assign('submit_error', 'urlintitle');
		$error = true;
	}
	if(!$linkres->category > 0) { // if no category is selected
		$main_smarty->assign('submit_error', 'nocategory');
		$error = true;
	}
	foreach($cached_categories as $cat) {
		if($cat->category__auto_id == $linkres->category && !allowToAuthorCat($cat)) { // category does not allow authors of this level
			$main_smarty->assign('submit_error', 'nocategory');
			$error = true;
		}
	}

	if($error == true){
		$main_smarty->assign('link_id', $linkres->id);
		$main_smarty->assign('tpl_center', $the_template . '/submit_errors');
		$main_smarty->display($the_template . '/pligg.tpl');
		die();
	}

	return $error;
}
// assign any errors found during captch checking
function link_catcha_errors($linkerror)
{
	global $main_smarty, $the_template;
	$error = false;

	if($linkerror == 'captcha_error') { // if no category is selected
		$main_smarty->assign('submit_error', 'register_captcha_error');
		$main_smarty->assign('tpl_center', $the_template . '/submit_errors');
		$main_smarty->display($the_template . '/pligg.tpl');
#		$main_smarty->display($the_template . '/submit_errors.tpl');
		$error = true;
	}
	return $error;
}

function allowToAuthorCat($cat) {
	global $current_user, $db;

	$user = new User($current_user->user_id);
	if($user->level == "god")
		return true;
	else if($user->level == "admin" && ((is_array($cat) && $cat['authorlevel'] != "god") || $cat->category_author_level != "god"))
		return true;
	else if((is_array($cat) && $cat['authorlevel'] == "normal") || $cat->category_author_level == "normal")
	// DB 11/12/08
	{
	    $group = is_array($cat) ? $cat['authorgroup'] : $cat->category_author_group;
	    if (! $group)
		return true;
	    else
	    {
		$group = "'".preg_replace("/\s*(,\s*)+/","','",$group)."'";
		$groups = $db->get_row($sql = "SELECT a.* FROM ".table_groups." a, ".table_group_member." b
							WHERE   a.group_id=b.member_group_id AND
							 	b.member_user_id=$user->id   AND
								a.group_status='Enable' AND
								b.member_status='active' AND
								a.group_name IN ($group)");
		if ($groups->group_id)
		    return true;
	    }
	}
	/////
	return false;
}
?>



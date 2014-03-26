<?php
include_once('Smarty.class.php');
$main_smarty = new Smarty;
include_once('config.php');
include_once(mnminclude.'html1.php');
include_once(mnminclude.'link.php');
include(mnminclude.'interest.php');
include(mnminclude.'bookmark.php');
include(mnminclude.'interest_member.php');
include(mnminclude.'friends.php');
include(mnminclude.'location.php');
include_once(mnminclude.'photo.php');
include_once(mnminclude.'groups.php');
include_once(mnminclude.'group.php');
include('global_variable.php');
$mobOpt=true;
include(mnminclude.'smartyvariables.php');
include(mnminclude.'plan_members.php');
include_once(mnminclude.'user_fetch.php');


$canonical=false;

// module system hook
if($isMobile){$the_template="mobile";}

if(isset($_REQUEST['plan_id']) && is_numeric($_REQUEST['plan_id'])){
    $id=$_REQUEST['plan_id'];
    $canonical=true;

}else if(isset($_REQUEST['title'])){
    $requestTitle=$_REQUEST['title'];
    if(isset($requestTitle)){$id = $db->get_var($sql="SELECT link_id FROM " . table_links . " WHERE `link_title_url` = '".$db->escape(sanitize($requestTitle,4))."';");}
}
if(!isset($id) || !is_numeric($id)){
  //  die('abc');
?>
<script     type="text/javascript">
window.location = "/404error.php";
</script>
<?php

die();
}
?>
<script     type="text/javascript">
var plan_id = <?php echo $id;  ?>;
</script>
<?php
// pagename	
define('pagename', 'plan');
if(isset($_REQUEST['tab']) &&( $_REQUEST['tab']=="info" || $_REQUEST['tab']=="members" || $_REQUEST['tab']=="photos" || $_REQUEST['tab']=="activities" || $_REQUEST['tab']=="locations" || $_REQUEST['tab']=="updates")){
    $tab=$_REQUEST['tab'];
    $canonical=true;
}else{
    $tab="info";
}
$main_smarty->assign('pagename', pagename);
$plan= new link();
$plan->id=$id;
if(!$plan->read()){

    if($current_user->authenticated){
        header("Location: ".my_pligg_base ."/404error.php");
    }else{

        $get['return']=getmyurl('plan',$id );
        $main_smarty->assign('get', $get);
        $main_smarty->assign('og_posttitle', 'Plan');
        $main_smarty->assign('og_content', 'Private Plan');
        $main_smarty->assign('postImage', 'http://shaukk.com/avatars/user_uploaded/1_original.jpg');
        $main_smarty->assign('tpl_center', $the_template . '/login_center');
        $main_smarty->assign('tpl_header', $the_template . '/header_guest');
        $main_smarty->display($the_template . '/pligg.tpl');
       die;
    }
}

    $photo=new Photo();
    $photos=$photo->getPhotosByPlan($plan->id, 50);
    $i=0;
	if($photos){
    	$main_smarty->assign('photos_count', count($photos));
		$main_smarty->assign('photos', $photos);
	}else{
    	$main_smarty->assign('photos_count', 0);
	}
    $author_id=$plan->author;
    if($plan->hasVoted()) $voted=1; else $voted=0;
    $main_smarty->assign('voted', $voted);
    $main_smarty->assign('friend',get_members($plan->id));
    $main_smarty->assign('liker', $plan->get_likers());
    if(hasJoined($plan->id)){ $hasjoined=1;}
		else $hasjoined=0;
    $main_smarty->assign('status', $plan->canJoin());
	$main_smarty->assign('hasjoined', $hasjoined);
    $main_smarty->assign('memberscount',count(get_members($plan->id)));
    $main_smarty->assign('plan_id',$id);
    $main_smarty->assign('submitter_profile_url',getmyFullurl('profileId', $author_id));
    $main_smarty->assign('plan_url',getmyFullurl('plan', $plan->id, "activities"));
    $main_smarty->assign('plan_main_url',getmyFullurl('events', $plan->title_url, "info"));
    if($canonical){
        $main_smarty->assign('canonical_url',getmyFullurl('events', $plan->title_url));
    }
    $main_smarty->assign('rem_check',planAuthor($id));
    if(planAuthor($id)==$current_user->user_id){
        $main_smarty->assign('isAuthor','true');
    }
    $main_smarty->assign('content', save_text_to_html($plan->link_summary));
    $main_smarty->assign('og_content',  generatemetText($plan->link_summary));
    $meta_description= htmlentities($plan->title)." · ".$plan->link_field10." · Time: ".  $plan->link_field2 ." · Place: ".$plan->link_field3." · ".count(get_members($plan->id))." people joined". " · " .generatemetText($plan->link_summary);
    $main_smarty->assign('meta_description', $meta_description);
    $main_smarty->assign('plan_latitude', $plan->link_field4);
    $main_smarty->assign('plan_type', $plan->link_field10);
    $main_smarty->assign('plan_viewer_id', $current_user->user_id);
    $main_smarty->assign('randkey', rand(10000000, 100000000000));
    $main_smarty->assign('plan_place', $plan->link_field3);
    $main_smarty->assign('plan_datetime', $plan->link_field2);
    $main_smarty->assign('plan_update', $plan->link_field15);
    $main_smarty->assign('tab', $tab);
	$main_smarty->assign('plan_date', date( "F dS, Y", strtotime($plan->link_field2)));
	$main_smarty->assign('plan_date_iso', date( "c", strtotime($plan->link_field2)));
    $same_time="true";
    if(strtotime($plan->link_field14)-strtotime($plan->link_field2)>0){
        $same_time="false";
        $main_smarty->assign('plan_end_date_iso', date( "c", strtotime($plan->link_field14)));
        $main_smarty->assign('plan_end_date', date( "F dS, Y", strtotime($plan->link_field14)));

    }
    $main_smarty->assign('same_time', $same_time);
    $main_smarty->assign('plan_time', date( "h:i A", strtotime($plan->link_field2)));
    $main_smarty->assign('plan_longitude', $plan->link_field5);
    if($plan->editable){
        $main_smarty->assign('isEditable', "true");
    }else{
        $main_smarty->assign('isEditable', "false");
    }
    //echo $plan->link_field6;
    $main_smarty->assign('plan_closing_time', $plan->link_field6);
    $main_smarty->assign('edit_own_plan', getmyurl('plan_edit', $plan->id));

    $main_smarty->assign('the_comments', $plan->get_comments(true));
    if($plan->link_field13!="" && $plan->link_field13!=null){
        $postImage=my_base_url.my_pligg_base.'/images/banner/'.urldecode($plan->link_field13);

    }else{
        $postImage=my_base_url.my_pligg_base.'/images/interest/'.$plan->link_field1.".jpg";
    }
    $main_smarty->assign('prom_banner',$postImage);
    $main_smarty->assign('posttitle',sanitize($plan->title, 2));
    $main_smarty->assign('og_posttitle',sanitize($plan->title, 2));
    $main_smarty->assign('postUrl', my_base_url.$_SERVER['REQUEST_URI']);
    $main_smarty->assign('postImage', $postImage);



function get_members($ids){
    Global $db, $author_id, $main_smarty, $current_user,$link;


    $members=getPlanMembersShort($ids);
    $i=0;
    $hasjoined=false;
     foreach($members as $member){
        $array[$i]['user_id']=$member->user_id;
		if($member->user_id==$author_id){
			$main_smarty->assign('author_name', $member->user_names);
		}
        $array[$i]['name']=$member->user_names;
        $array[$i]['avatar']=get_avatar(100, "", "", "",$member->user_id );
        $array[$i]['url']=getmyFullurl('profiles', $member->user_url, '', '');
		
		
		// user request status  for $member->user_id
		 if($current_user->user_id==$member->user_id)
		 {
 			$array[$i]['me']="true";		 
		 }
		 include_once(mnminclude.'plan_members.php');
   		 $auth=planAuthor($id);
		
		//delete profile view 
		if($current_user->user_id==$auth)
		{
		$main_smarty->assign('author_on', true);
		}else{
		
		$main_smarty->assign('author_on', false);
		}
		
		
		if($current_user->user_id==$member->user_id){
		    $hasjoined=true;
			$array[$i]['cuser']="true";
		}else{
			$array[$i]['cuser']="false";
		}
		$i++;
    }
    //print_r($array);
    return $array;
}
$fetch_link_summary = true;
$main_smarty->assign('hasjoined', $hasjoined);
$main_smarty->assign('link_summary_output', $plan->print_summary("summary",true, 'link_summary.tpl'));
$main_smarty->assign('tpl_center', $the_template . '/plan_page');
$main_smarty->display($the_template . '/pligg.tpl');

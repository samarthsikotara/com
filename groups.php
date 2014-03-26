<?php

include_once('Smarty.class.php');
$main_smarty = new Smarty;
include_once('config.php');
include_once(mnminclude.'html1.php');
include_once(mnminclude.'link.php');
include_once(mnminclude.'scribble.php');
include_once(mnminclude.'interest.php');
include_once(mnminclude.'location.php');
include_once(mnminclude.'search.php');
include_once(mnminclude.'searchscribble.php');
include_once(mnminclude.'bookmark.php');
include_once(mnminclude.'interest_member.php');
include_once(mnminclude.'photo.php');
include_once(mnminclude.'plan_members.php');
include_once('global_variable.php');
$mobOpt=true;
include_once(mnminclude.'smartyvariables.php');

include_once(mnminclude.'group.php');
include_once(mnminclude.'groups.php');

$group = new groups();
$canonical=false;

if(isset($_REQUEST['group_id']) && is_numeric($_REQUEST['group_id'])){
$id=$_REQUEST['group_id'];
$group->group_id = $id;
$canonical=true;
}
else if(isset($_REQUEST['title'])){
    $title=$_REQUEST['title'];
    $group->group_title_url = $title;

}
else{
?>
<script     type="text/javascript">
window.location = "404error.php";
</script>
<?php

die();
}

if(!$group->group_exists()) { ?>
<script     type="text/javascript">
    window.location = "404error.php";
</script>
<?php

    die();
}
$id=$group->group_id;
?>
<script type="text/javascript">var groupId=<?php echo $id;?></script>
<?php
$showMember="false";
if(isset($_REQUEST['member']) && $_REQUEST['member']=="true"){
    $showMember="true";
    $canonical=true;
}
$offset=0;
$pagesize=10;

if(isset($_REQUEST['offset']) && is_numeric($_REQUEST['offset']))$offset=$_REQUEST['offset'];
$group->allgroupDetails();
//die('dvbfdjk');
$storyHtml = $group->groupstoriesHtml($offset,$pagesize);

$main_smarty->assign('story',$storyHtml);
if(isset($_REQUEST['data']) && $_REQUEST['data']=="story"){
    echo $storyHtml;
    die;
}
$groupmembers = get_all_members($id,'active');
$groupmembersreq = array();
$group->groupLocation();

if($group->group_creator==$current_user->user_id){
    //echo "abc";
    $groupmembersreq = get_all_members($id,'requested');
}
// pagename
define('pagename', 'groups');
$main_smarty->assign('pagename', pagename);
$main_smarty->assign('posttitle', $group->group_name);
$main_smarty->assign('og_posttitle', $group->group_name);
$main_smarty->assign('postImage', $group->group_photo['100']);
$main_smarty->assign('og_content', $group->group_desc);

$main_smarty->assign('groupId',$group->group_id);
$main_smarty->assign('groupName',$group->group_name);
$main_smarty->assign('showMember',$showMember);
$main_smarty->assign('groupPhoto',$group->group_photo['250']);
if($group->canAccess){
    $main_smarty->assign('canAccess',"true");
}else
$main_smarty->assign('canAccess',"false");

if($canonical){
    $main_smarty->assign('canonical_url',$group->group_url);
}
$main_smarty->assign('groupDescp',str_replace("\n", "<br>",$group->group_desc));
$main_smarty->assign('groupLocation',$group->group_locations);
$main_smarty->assign('friend',$groupmembers);
$main_smarty->assign('groupmembersreq',$groupmembersreq);
$main_smarty->assign('count_groupmembersreq',count($groupmembersreq));
$main_smarty->assign('memberRole',$group->memberRole());
$interests=$group->group_interest;
$locations=$group->group_locations;
$interest_list = array();
$i=0;
if(count($interests)!=0){
    foreach($interests as $item){
        $interest_list[$i]=getInterestOtherDetails($item['interest_id']);
        $i++;
    }
}
$groupsSug=groupforuser(0,12);
//print_r($groupsSug);
$groupSugCount=count($groupsSug);
if(count($groupsSug)>0){
    $i=0;
    foreach($groupsSug as $result){
        $groupsSug[$i]['image']=getGroupImage($result['group_id'], '100');
        $groupsSug[$i]['image_small']=getGroupImage($result['group_id'], '50');
        $groupsSug[$i]['image_large']=getGroupImage($result['group_id'], '250');
        $groupsSug[$i]['url']=getmyurl('group_page',$result['group_id'] );
        $i++;
    }
}

$main_smarty->assign('allgroup',$groupsSug);
$main_smarty->assign('interest', $interest_list);
$main_smarty->assign('locations', $locations);
$main_smarty->assign('status',isMember($group->group_id,$current_user->user_id));
$main_smarty->assign('groupplan',$group->groupplans(0, 10));

$main_smarty->assign('tpl_center', $the_template . '/group_page');
$main_smarty->display($the_template . '/pligg.tpl');
?>
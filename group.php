 <?php
include_once('Smarty.class.php');
$main_smarty = new Smarty;

include_once('config.php');
include_once(mnminclude.'html1.php');
include_once(mnminclude.'link.php');
include_once(mnminclude.'group.php');
include_once(mnminclude.'location.php');
include_once(mnminclude.'interest_member.php');
include_once('global_variable.php');
$mobOpt=true;
include_once(mnminclude.'smartyvariables.php');
include_once(mnminclude.'groups.php');

if($isMobile){$the_template="mobile";}

define('pagename', 'all_group');
$main_smarty->assign('pagename', pagename);
$main_smarty->assign('posttitle', "Groups on Shaukk");
$myGroup=myallgroup();
$myGroupCount=count($myGroup);
if(count($myGroup)>0){
    $i=0;
    foreach($myGroup as $result){
        $myGroup[$i]['image']=getGroupImage($result['group_id'], '100');
        $myGroup[$i]['image_small']=getGroupImage($result['group_id'], '50');
        $myGroup[$i]['image_large']=getGroupImage($result['group_id'], '250');
        $myGroup[$i]['url']=getmyurl('groups_url',$result['group_field6'] );
        $i++;
    }
}
$groupsSug=groupforuser(0,50);
//print_r($groupsSug);
 $groupSugCount=count($groupsSug);
if(count($groupsSug)>0){
    $i=0;
    foreach($groupsSug as $result){
        $groupsSug[$i]['image']=getGroupImage($result['group_id'], '100');
        $groupsSug[$i]['image_small']=getGroupImage($result['group_id'], '50');
        $groupsSug[$i]['image_large']=getGroupImage($result['group_id'], '250');
        $groupsSug[$i]['url']=getmyurl('groups_url',$result['group_field6'] );
        $i++;
    }
}

$main_smarty->assign('mygroup',$myGroup);
$main_smarty->assign('mygroupcount',$myGroupCount);

$main_smarty->assign('allgroup',$groupsSug);

// show the template
$main_smarty->assign('tpl_center', $the_template . '/group_center');
$main_smarty->display($the_template . '/pligg.tpl');
?>
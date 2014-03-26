<?php
include_once('Smarty.class.php');
$main_smarty = new Smarty;
include_once('config.php');
include(mnminclude.'html1.php');
include(mnminclude.'scribble.php');
include(mnminclude.'interest.php');
include(mnminclude.'plan_members.php');
include(mnminclude.'bookmark.php');
include(mnminclude.'interest_member.php');
include_once(mnminclude.'friends.php');
include_once(mnminclude.'location.php');
include_once(mnminclude.'group.php');
include_once(mnminclude.'groups.php');
include_once('global_variable.php');
$mobOpt=true;
include(mnminclude.'smartyvariables.php');
include_once(mnminclude.'user_fetch.php');

// module system hook

if(isset($_REQUEST['scribble_id']) && is_numeric($_REQUEST['scribble_id'])){
    $id=$_REQUEST['scribble_id'];
}
else{
?>
<script     type="text/javascript">
window.location = "404error.php";
</script>
<?php

die();
}

$main_smarty->assign('posttitle', $main_smarty->get_config_vars('PLIGG_Visual_Scribble'));


// pagename
define('pagename', 'scribble');
$scribble= new Scribble();
$scribble->id=$id;
if(!$scribble->read()){
    header("Location: ".my_pligg_base ."/404error.php");
}
$main_smarty->assign('scribble_id', $scribble->id );
$main_smarty->assign('pagename', pagename);
$scribble->details=true;
$scribble_content=$scribble->print_summary('full', true, 'scribble_details_center.tpl');
$main_smarty->assign('content', $scribble_content);
$main_smarty->assign('tpl_center', $the_template . '/scribbles_by_id_center');
$main_smarty->display($the_template . '/pligg.tpl');



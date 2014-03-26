<?php
include_once('Smarty.class.php');
$main_smarty = new Smarty;
include_once('config.php');
include(mnminclude.'html1.php');
include(mnminclude.'link.php');
include(mnminclude.'planjoinkey.php');
include(mnminclude.'plan_members.php');
include(mnminclude.'smartyvariables.php');
include_once(mnminclude.'location.php');
include_once(mnminclude.'group.php');
include_once(mnminclude.'groups.php');
include_once(mnminclude.'plan_members.php');
include_once(mnminclude.'interest_member.php');
include('global_variable.php');
$status=false;
if(!isset($_REQUEST['joinKey'])) {
    header("Location: 404error.php");
}
$key=sanitize($_REQUEST['joinKey'], 3);
try{
    if(joinPlanByKey($key)){
        $status=true;
        expireJoinkey($key);
    }
}catch(Exception $e){
    $mssg=$e->getMessage();
}


if($status){
    $mssg="You successfully joined the Plan";
    header("Location: index.php?joinStatus=true");
}elseif(!$status && $mssg==""){
    $mssg="An error occured while joining you in plan";
}
header("Location: index.php?sk=home&joinStatus=false&mssg=".urlencode($mssg));
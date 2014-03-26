<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Swaroop
 * Date: 2/23/13
 * Time: 2:51 PM
 * To change this template use File | Settings | File Templates.
 */
 

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
include_once('global_variable.php');
$mobOpt=true;
include(mnminclude.'smartyvariables.php');
include_once(mnminclude.'user_fetch.php');

define('pagename',"create_scribble");
$main_smarty->assign('posttitle', 'Create a Scribble');
if(!$current_user->authenticated) force_authentication();

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
$main_smarty->assign('interest_list', $interest_list);

$main_smarty->assign('tpl_center', $the_template . '/create_scribble');
$main_smarty->display($the_template . '/pligg.tpl');
?>
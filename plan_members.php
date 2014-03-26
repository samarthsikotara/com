
<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Swaroop
 * Date: 7/22/12
 * Time: 11:21 AM
 * To change this template use File | Settings | File Templates.
 */

include_once("link.php");
include_once "notification.php";
include_once("planjoinkey.php");
include_once("friends.php");
include_once("request.php");
include_once("generateHtml.php");
include_once("user_fetch.php");
include_once("facebookapi.php");
include_once(mnminclude."group.php");
include_once(mnminclude."groups.php");
include_once(mnminclude."achieve.php");


function getPlanMembersCount($id){
    global $db, $current_user;
    if(!is_numeric($id))die();
    return $db->get_var('SELECT COUNT(*)FROM '.table_plan_members." WHERE `plan_id`=".$id." AND `status`='Joined'");
}
function getPlanMembersShort($id){
    global $db, $current_user;
    if(!is_numeric($id))die();
    //echo 'SELECT '.table_plan_members.'.user_id, '.table_users.'.user_names,'.table_users.'.user_login FROM '.table_plan_members.' INNER JOIN '.table_users.' ON '.table_plan_members.'.user_id='.table_users.'.user_id WHERE '.table_plan_members.'.plan_id=".$id." AND '.table_plan_members.'.status="Joined" ';
    return $db->get_results('SELECT '.table_plan_members.'.user_id, '.table_users.'.user_names,'.table_users.'.user_login,'.table_users.'.user_url FROM '.table_plan_members.' INNER JOIN '.table_users.' ON '.table_plan_members.'.user_id='.table_users.'.user_id WHERE '.table_plan_members.'.plan_id='.$id.' AND '.table_plan_members.'.status="Joined" ');
}
function isGuestInvited($email, $plan_id){
    global $db;
    if(!check_email($email)) return false;
    if(!is_numeric($plan_id)) return false;
    $sql="SELECT count(*) FROM ".table_plan_members_guest." WHERE `plan_id`=".$plan_id." AND `user_email`='".$email."'";
    $count= $db->get_var($sql);
    if($count>0) return true;
    else return false;
}
function joinPlan($id, $privacy){
	global $db, $current_user;
	if(!is_numeric($id)) return false;
    //echo "saurav";
    //echo canJoin($id);
	if(canJoin($id)=='Join'){
	$sql="INSERT INTO ".table_plan_members." (`plan_id`, `user_id`, `join_time`, `status`, `invitedBy`, `privacy`) VALUES(".$id.", ".$current_user->user_id.",'".date('Y-m-d H:i:s')."', 'Joined', 0, '".$privacy."')";
	if($db->query($sql)){
         $plan= new Link();
         $plan->id=$id;

         if($plan->read()){
            postJoined(getmyFullurl('plan', $id),$plan->link_field3,date('m/d/Y H:i:s', strtotime($plan->link_field6)),date('m/d/Y H:i:s', strtotime($plan->link_field6)));
         }


         $achievement= new achievements();
         $achievement->entity_id['plan']= $id;
		 $achievement->thirdparty[0]=$plan->link_auhtor;
         $achievement->No_Of_Plans_Joined=1;
         $achievement->updateScore();
         $users_array[1]['id']=$current_user->user_id;
         $users_array[1]['name']=$current_user->user_name;
         $users_array[1]['time']=date("Y-m-d H:i:s", time());
         //echo $this->type;
         $type="plan";
         addNotification('join', $type,$id, $users_array);
         addSubscriber('join', $type,$id,$current_user->user_id);
         sendJoinNotifEmail($id);
         return true;
    }else return false;
    }
    else throw new Exception(generateJoinError($id));
}

function sendJoinNotifEmail($plan_id){
    global $db, $current_user;
    if(!is_numeric($plan_id)) return false;
    if(!$current_user->authenticated) return false;
    $plan_members = getPlanMembersShort($plan_id);
    $plan=new Link();
    $plan->id= $plan_id;
    $plan->get_author_info=true;
    $plan->read();
    $plan->username();
    $count=count($plan_members);
    if(fmod($count, 4.0)==0){
        foreach($plan_members as $member){
            if($member->user_id==$current_user->user_id) continue;
            if($plan->author==$member->user_id){
                    $word="your";
                }else{
                       $word= $plan->authorname."'s";
                }
            $user_info=getUserDetailsSmall($member->user_id);
            $email= $user_info->user_email;
            $array['word']=$word;
            $array['senderName']= $user_info->user_names;
            $array['planUrl']= getmyFullurl('plan', $plan_id);
            $array['userName']=  $current_user->user_name." & ".($count-1)." others";
            $array['planName']= $plan->title;
            $array['unsubUrl']= getmyFullurl('unsubscribe', $email);
            $subject=$array['userName']." others joined ".$word." plan ".$plan->title;
            $body=generateHTMLBody($array, 'plan_joined_email');
            //$body="Hi,<br><br>".$current_user->user_name." wants you to attend ".$word." plan '".$plan->title."' on ".date("l, jS F, Y (h:i:s A)", strtotime($plan->link_field2))."<br><br>To join this plan and enjoy ".$interestName." near you,<br><br>Register with <a href='".$registerUrl."'>Shaukk</a> or through <a href='$registerUrl'>facebook.</a><br><br>Who else has joined this plan? Invite your friends to this plan? Latest updates on this plan?<a herf='".$planUrl."'>Click here</a><br><br>You can now avail multiple exciting offers while creating, joining and sharing cool plans near you at Shaukk.<br><br>Thank You,<br>Shaukk Team";
           send_emailSmtp($email, "", $subject, $body, 'plan_join');
        }
    }
}

function joinPlanByKey($key){
	global $db, $current_user;
    $privacy="public";
    $key=sanitize($key, 3);
    $userDetails=getJoinKeyDetails($key);
    //print_r($userDetails);
    if(!$userDetails) throw new Exception('The link has expired');
    $sql="SELECT `user_accessToken`,`user_id`,`user_login`, `user_pass` from ".table_users." WHERE `user_id`=".$userDetails->userId." LIMIT 1";
    $user_info = $db->get_row($sql);
    if(!empty($user_info)){
        include_once mnminclude."login.php";
        if($current_user->Authenticate($user_info->user_login, $user_info->user_pass,false,  $user_info->user_pass)){
            joinPlan($userDetails->planId, "public");
                return true;

        }else{
            if(canJoinByUser($userDetails->planId, $userDetails->userId)=='Join'){
                $sql="INSERT INTO ".table_plan_members." (`plan_id`, `user_id`, `join_time`, `status`, `invitedBy`, `privacy`) VALUES(".$userDetails->planId.", ".$userDetails->userId.",'".date('Y-m-d H:i:s')."', 'Joined', 0, '".$privacy."')";
                if($db->query($sql)){
                     return true;
                }else return false;
            }

        }
    }
    else return false;
}
function generateJoinError($id){
    global $db, $current_user;
    if(!is_numeric($id)) die();
    if(canJoin($id)=="Guest") return "You must be logged in to Join this plan.";
    if(canJoin($id)=="Unjoin") return "You have already joined this plan.";
    if(canJoin($id)=="Closed") return "This plan is closed";
    if(canJoin($id)=="Full") return "This plan is already full";
    return true;
	// to check if the plan is private or public
	// to check if the seats are vacant for non-invited member
	// to check if the user is invited
	// to check if the registration is closed
}
function canJoin($id){
    global $db, $current_user;
    if(!is_numeric($id)) die();
    if($current_user->user_id==0) return "Guest";
    $link= new Link();
    $link->id=$id;
    $link->read();
    //echo $link->canJoin();
    return $link->canJoin();
}
function canJoinByUser($id, $user_id){
    global $db, $current_user;
    if(!is_numeric($id)) return false;
    $link= new Link();
    $link->id=$id;
    $link->read();
    //echo $link->canJoin();
    $status=$link->canJoin();
    if($status=="Unjoin" || $status=="Join"){
        if(hasJoinedByUser($id, $user_id)) return "Unjoin";
        else return "Join";
    }
    else return $status;
}
function planAuthor($id){
    global $db, $current_user;
    if(!is_numeric($id)) return false;
    $sql="SELECT `link_author` FROM ".table_links." WHERE `link_id`=".$id;
    return $db->get_var($sql);
}
function unJoinPlan($id){
	global $db, $current_user;
	if(!is_numeric($id)) return false;
    if(planAuthor($id)==$current_user->user_id) throw new Exception('You cannot unjoin your own plan');
    $link= new Link();
    $link->id=$id;
    $link->read();
    //echo $link->canJoin();
    $status=$link->canJoin();
    if($status=="Closed"){throw new Exception('This plan is closed');}
	$sql="DELETE FROM ".table_plan_members." WHERE `user_id`=".$current_user->user_id." AND `plan_id`=".$id;
	return $db->query($sql);
}
function hasJoined($id){
	global $db, $current_user;
	if(!is_numeric($id)) return false;
    $sql="SELECT COUNT(*) FROM ".table_plan_members." WHERE `plan_id`=".$id." AND `user_id`=".$current_user->user_id." AND `status`='Joined'";
	if($db->get_var($sql)>0){
		return true;
	}
	else return false;
}
function hasJoinedByUser($id, $user_id){
    global $db, $current_user;
	if(!is_numeric($id)) return false;
    $sql="SELECT COUNT(*) FROM ".table_plan_members." WHERE `plan_id`=".$id." AND `user_id`=".$user_id." AND `status`='Joined'";
	if($db->get_var($sql)>0){
		return true;
	}
	else return false;
}
function isInvited($id){
    global $db, $current_user;
    if(!is_numeric($id)) die();
    $sql= "SELECT COUNT(*) FROM ".table_plan_members." WHERE `plan_id`=".$id." AND `user_id`=".$current_user->user_id." AND `invitedBy`>0";
    if($db->get_var($sql)>0){
        return true;
    }
    else return false;
}
function invite($user, $id, $from_email=false){
    global $db, $current_user;
    $privacy="public";
    if(!is_numeric($user)) die();
    if(!is_numeric($id)) die();
    if(!$current_user->authenticated){return false;}
    $friend= new Friend();
    //echo "saurav";
    if($friend->get_friend_status($user)=="accepted" || $friend->get_following_status($user) || $from_email ){
       $plan=new Link();
       $plan->id= $id;
       $plan->get_author_info=true;
       $plan->read();
       $plan ->username();
       $invitee=getUserDetailsSmall($user)->user_names;
       $inviteeName=explode(" ", $invitee);
       if(!empty($inviteeName)){
           $inviteeName=$inviteeName[0];
       }else{
           $inviteeName=$invitee;
       }
       //echo $inviteeName;
       $array['interestName']=$plan->InterestName();
       if(canJoinByUser($id, $user)=="Join"){
           $sql="INSERT INTO ".table_plan_members." (`plan_id`, `user_id`, `join_time`, `status`, `invitedBy`, `privacy`) VALUES(".$id.", ".$user.",'".date('Y-m-d H:i:s')."', 'Invited', ".$current_user->user_id.", '".$privacy."')";
           $db->query($sql);
           $key= createJoinKey($id, $user);
           $array['plan_url']= getmyFullurl('plan', $id);

           if($key){
            $array['planJoinUrl']=my_base_url."/joinPlan.php?joinKey=".$key;
           }
           addPlanInviteNotifiocation($id, $user);
           if(trim($current_user->user_sex)=="F"){$word="her";}else if(trim($current_user->user_sex)=="B"){$word="its";}else{$word="his";}
           if(trim($current_user->user_sex)=="F"){$parent_word="her";}else if(trim($current_user->user_sex)=="B"){$parent_word="its";}else{$parent_word="him";}
           if($plan->author!=$current_user->user_id){
                $word= $plan->authorname."'s";
           }
           $user_details=$db->get_row("SELECT `user_email` FROM ".table_users." WHERE `user_id`='".$user."' LIMIT 1");
           $array['word']=$word;
           $array['inviteeName']=$inviteeName;
           $array['inviterName']=$current_user->user_name;
           $array['planName']=$plan->title;
           $array['planTime']=date("l, jS F, Y (h:i A)", strtotime($plan->link_field2));
           $array['unsubUrl']= getmyFullurl('unsubscribe', $user_details->user_email);
           //$subject=$current_user->user_name." is waiting for you to join ".$word." ".$array['interestName']." event at Shaukk";
		   /* changed By Aseem*/
           $subject=$current_user->user_name." invited you to attend the ".$word." ".$array['interestName']." event via Shaukk";
		   /******/
		   $body=generateHTMLBody($array, 'plan_invite');

           //$body="Hi ".$inviteeName.",<br><br>".$current_user->user_name." wants you to attend ".$word." plan '".$plan->title."' on ".date("l, jS F, Y (h:i:s A)", strtotime($plan->link_field2))."<br><br>Enjoy '".$interestName."' !<br><br><a href='".$url."'>Click here to join the plan.</a><br><br>Who else has joined this plan? Invite your friends to this plan? Latest updates on this plan? <a href='".$plan_url."'>Click here</a><br><br>You can now avail multiple exciting offers while creating, joining and sharing cool plans near you at shaukk.<br><br>Thank You,<br>Shaukk Team";
           //$user_details=getUserDetails($user);
           //print_r($user_details);
           //echo $body;
           send_emailSmtp($user_details->user_email, "", $subject, $body, 'plan_invite');
           return true;
       }else return false;
    }
    return false;
}
function invite_guest($user, $id){
    global $db, $current_user;
    $privacy="public";
    if(!check_email($user)) return false;
    if(!is_numeric($id)) return false;
    if(!$current_user->authenticated){return false;}
    $plan=new Link();
    $plan->id= $id;
    $plan->get_author_info=true;
    $plan->read();
    $plan->username();
    $array['interestName']=$plan->InterestName();
    //echo "saurav";
    if(email_exists($user)){
        $user_id=$db->get_var("SELECT `user_id` FROM ".table_users." WHERE `user_email`='".$user."' LIMIT 1");
        invite($user_id, $id, true);
        return true;
    }else{
        if(!isGuestInvited($user, $id)){
            $sql="INSERT INTO ".table_plan_members_guest." (`plan_id`, `user_email`, `join_time`, `status`, `invitedBy`, `privacy`) VALUES(".$id.", '".$user."','".date('Y-m-d H:i:s')."', 'Invited', ".$current_user->user_id.", '".$privacy."')";
            //die();
            $db->query($sql);
            if($plan->author==$current_user->user_id){
                if(trim($current_user->user_sex)=="F"){$word="her";}else if(trim($current_user->user_sex)=="B"){$word="its";}else{$word="his";}
            }else{
                   $word= $plan->authorname."'s";
            }
            $array['word']=$word;
            $array['registerUrl']= "http://shaukk.com/login.php";
            $array['planUrl']= getmyFullurl('plan', $id);
            $array['inviterName']= $current_user->user_name;
            $array['planName']= $plan->title;
            $array['planTime']= date("l, jS F, Y (h:i:s A)", strtotime($plan->link_field2));
            $array['unsubUrl']= getmyFullurl('unsubscribe', $user);

            //$subject=$current_user->user_name." via Shaukk";
            /* Changed By Aseem*/
			$subject=$current_user->user_name." shared a ".$word." ".$array['interestName']." event via Shaukk";
			/****/
			$body=generateHTMLBody($array, 'plan_invite_guest');
            //$body="Hi,<br><br>".$current_user->user_name." wants you to attend ".$word." plan '".$plan->title."' on ".date("l, jS F, Y (h:i:s A)", strtotime($plan->link_field2))."<br><br>To join this plan and enjoy ".$interestName." near you,<br><br>Register with <a href='".$registerUrl."'>Shaukk</a> or through <a href='$registerUrl'>facebook.</a><br><br>Who else has joined this plan? Invite your friends to this plan? Latest updates on this plan?<a herf='".$planUrl."'>Click here</a><br><br>You can now avail multiple exciting offers while creating, joining and sharing cool plans near you at Shaukk.<br><br>Thank You,<br>Shaukk Team";
            send_emailSmtp($user, "", $subject, $body, 'plan_invite');
            return true;
        }
        else return false;
    }

}

function remove_members($plan_id,$user_id){
	global $db, $current_user;
	if(!is_numeric($user_id)) die();
    if(planAuthor($user_id)!=$current_user->user_id) die('You cannot remove the user from plan');
	$sql="DELETE FROM ".table_plan_members." WHERE `user_id`=".$user_id." AND `plan_id`=".$plan_id;
	return $db->query($sql);
}


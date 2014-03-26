<?php
/**
 * Created by JetBrains PhpStorm.
 * User: mahipal
 * Date: 8/1/12
 * Time: 6:20 PM
 * To change this template use File | Settings | File Templates.
 */
function interestExists($id){
   if (!is_numeric($id)) return false;
    global $db;
    $sql="SELECT count(*) FROM ".table_interests. " WHERE interest_id=".$id;
   $count=$db->get_var($sql);

   if($count>0) return true;
    else return false;
}
function addinterest($id, $privacy){
    global $db, $current_user,$main_smarty,$the_template,$my_base_url,$my_pligg_base;

    if (!is_numeric($id)) die('Please Provide a valid Interest.');
    // if (isInterested($id)) return;
    if($current_user->user_id==0) die('You must be logged in to add an interest');
    $date= date('Y-m-d');
    if(isInterested($id)) die('You are already Interested');
    if($privacy!="public" && $privacy!="closed" && $privacy!="private") return;

    $sql = "INSERT IGNORE INTO ". table_interest_member ." ( `user_id` , `interest_id`, `privacy`, `joinedAt`) VALUES ('".$current_user->user_id ."', '".$id."','".$privacy."', '".$date."' ) ";
    //echo $sql;
    $db->query($sql);
    return true;
}
function removeinterest($id){
    global $db, $current_user,$main_smarty,$the_template,$my_base_url,$my_pligg_base;
    if (!is_numeric($id)) die();
    if (!isInterested($id)) return false;
    $sql="UPDATE ".table_interest_member." SET `status`='removed' WHERE user_id=".$current_user->user_id." AND interest_id=".$id;
    $db->query($sql);
    return true;
}
function isInterested($id){
    global $db, $current_user,$main_smarty,$the_template,$my_base_url,$my_pligg_base;

    if (!is_numeric($id)) die();

    $sql="SELECT count(*) FROM ".table_interest_member." WHERE `user_id`=".$current_user->user_id." AND `status`='active' AND `interest_id`=".$id;
    //echo $db->get_var($sql);
    if ($db->get_var($sql)==0) return false;
    else return true;

}
function interestedIn($id){
    global $db, $current_user,$main_smarty,$the_template,$my_base_url,$my_pligg_base;
    //echo $current_user;
   //$query="SELECT *  FROM `shinterest_member` WHERE `user_id`=".$current_user." AND `status`='active'";
    //echo "*******"  ;
    $sql=$db->get_results("SELECT `interest_id` FROM ".table_interest_member." WHERE `user_id`='".$id."' AND `status`='active'");
    //print_r($sql);
    return $sql;

}
function interestMember($id){
     if(!is_numeric($id)) die();
      global $db, $current_user,$main_smarty,$the_template,$my_base_url,$my_pligg_base;
     $sql=$db->get_results("SELECT `user_id` FROM ".table_interest_member." WHERE `interest_id`=".$id." AND `status`='active' AND `privacy`='public'");
    return $sql;
}
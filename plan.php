<?php
/**
 * Created by JetBrains PhpStorm.
 * User: mahipal
 * Date: 8/1/12
 * Time: 7:48 PM
 * To change this template use File | Settings | File Templates.
 */
if(!defined('mnminclude')){header('Location: ../404error.php');die();}
class plan {
    var $plan_id = 0;
    var $plan_title= '';
    var $plan_interest_id=0;
    var $plan_latitude= 0;
    var $plan_longitude=0;
    var $plan_category ='';
    var $plan_type='';
    var $plan_content='';
    var $plan_date='0000-00-00 00:00:00';
    var $plan_duration =0;
    var $plan_link_id= '';
    var $plan_user_id ='';
    var $plan_status ='';
    var $plan_limit=0;
    var $plan_reserved=0;
    var $plan_taken=0;

function save(){
    global $db, $current_user;
    $sql="INSERT INTO ".table_plan." (`plan_title`, `plan_interest_id`, `plan_latitude`, `plan_longitude`, `plan_category`, `plan_type`, `plan_content`, `plan_date`, `plan_duration`, `plan_user_id`, `plan_status`, `plan_reserved`, `plan_taken`) VALUES('".$db->escape($this->plan_title)."', '".$db->escape($this->plan_interest_id)."', '".$db->escape($this->plan_latitude)."', '".$db->escape($this->plan_longitude)."', '".$db->escape($this->plan_category)."', '".$db->escape($this->plan_type)."', '".$db->escape($this->plan_content)."', '".$db->escape($this->plan_date)."', '".$db->escape($this->plan_duration)."', '".$current_user."', '".$db->escape($this->plan_status)."', '".$db->escape($this->plan_reserved)."', '".$db->escape($this->plan_taken)."')";
    $db->query($sql);
}




}
?>
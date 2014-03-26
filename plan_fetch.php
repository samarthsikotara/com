<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Swaroop
 * Date: 2/7/12
 * Time: 4:29 PM
 * To change this template use File | Settings | File Templates.
 */
function getPlansByInterest($interestId, $sort){
        global $current_user, $db;
    if(!is_numeric($interestId))die();


    $sql="SELECT `link_id`, `link_field1`, `link_author`,`user_names`, `link_title`, `link_field2`,`link_field3`, COUNT(".table_plan_members.".id) AS `link_members` FROM ".table_links." INNER JOIN ".table_users." ON ".table_links.".link_author=".table_users.".user_id  LEFT JOIN  ".table_plan_members." ON ".table_links.".link_id=".table_plan_members.".plan_id WHERE link_field1=".$interestId." AND link_status='published' GROUP BY `link_id` ORDER BY `link_modified`";
   // echo $sql;
    return $db->get_results($sql);
}
function getPlansByInterestShort($interestId, $sort){
        global $current_user, $db;
    if(!is_numeric($interestId))die();


    $sql="SELECT `link_author`,`link_date`,`link_field3` FROM ".table_links." WHERE link_field1=".$interestId." AND link_status='published' ORDER BY `link_modified`";
    return $db->get_results($sql);
}
function getPlansById($id, $sort){
        global $current_user, $db;
    if(!is_numeric($id))die();


    $sql="SELECT `link_author`,`user_names`, `link_title`, `link_field2`,`link_field3` FROM ".table_links." INNER JOIN ".table_users." ON ".table_links.".link_author=".table_users.".user_id  INNER JOIN  ".table_plan_members." ON ".table_links.".link_id=".table_plan_members.".plan_id WHERE link_id=".$id." AND link_status='published' ORDER BY `link_modified` LIMIT 1";
    //echo $sql;
    return $db->get_results($sql);
}

 

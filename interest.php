<?php
include_once('Smarty.class.php');
$main_smarty = new Smarty;

include('config.php');
include(mnminclude.'html1.php');
include(mnminclude.'link.php');
include(mnminclude.'tags.php');
include(mnminclude.'search.php');
include(mnminclude.'plan_fetch.php');
include(mnminclude.'user_fetch.php');
include(mnminclude.'smartyvariables.php');
include(mnminclude.'interest.php');
include(mnminclude.'interest_member.php');
include(mnminclude.'global_variable.php');
// sidebar
$main_smarty = do_sidebar($main_smarty);
//the user must be logged in to view this page
//force_authentication();
function get_plans($interest_id){
    // this returns plans of a specific interest.
    // do changes here later, for showing personalized plans only or at the top etc.
    global $db;
    if (!is_float($radius)) die();
    if (!is_float($latitude)) die();
    if (!is_float($longitude)) die();
    // change this query or add other query after adding interest table, for retrieving info about the interest.
    $sql = 'SELECT ' . table_plans . '.*, ' . table_users . '.* FROM ' . table_plans . ' INNER JOIN ' . table_users . ' ON ' . table_plans . '.plan_user_id = ' . table_users . '.user_id WHERE ' . table_plans . '.plan_interest_id = ' . $interest_id . ' AND user_level<>"Spammer" ORDER BY ' . table_plans . '.plan_timestamp DESC';
    //echo $sql;
    $plans = $db->get_results($sql);
    $plans = object_2_array($plans);
    //getting the avatar source
    /*
    foreach($plans as $key => $val){
        $plans[$key]['Avatar_ImgSrc'] = get_avatar($avatar_size, "", $val['user_login'], $val['user_email']);
    }
    */
    return $plans;
}
// pagename
define('pagename', 'interest');
$main_smarty->assign('pagename', pagename);
if(isset($_GET['interestId'])){
    if(!is_numeric($_GET['interestId'])) showError('undefined');
        if(!interestExists($_GET['interestId'])) showError('notExists');
    else $main_smarty->assign('interestId', $_GET['interestId']);
}
    $i=0;
    $user=interestMember($_GET['interestId']);
    foreach($user as $item){
        $userList[$i]['name']=$item->user_names;
        $userList[$i]['id']=$item->user_id;
    }

    $main_smarty->assign('userList', $userList);
// check if the author of the plan is enabled

if(isset($_GET['view'])){
    if($_GET['view']=='plans'){
        $main_smarty->assign('view', "plans");
    }
    if($_GET['view']=='info'){
        $interestedIn=isInterested($_GET['interestId']);
        $main_smarty->assign('view', "info");
        $interest=getInterestOtherDetails($_GET['interestId']);
        $main_smarty->assign('interest', $interest);
        $main_smarty->assign('interestedIn', $interestedIn);
    }
}
//show the template
$main_smarty->assign('tpl_center', $the_template . '/interest_center');
$main_smarty->display($the_template . '/pligg.tpl');
?>
     <script type="text/javascript">
              $(document).ready(function(){
              $('#middle-strip-content').html('<img src="images/loading.gif">');
              q="work="+3+"&interestId=<?php echo $_GET['interestId']; ?>";
               //alert(q);
               $.ajax({
                   type: "POST",
                   url: "ajax.php",
                   data: q,
                   success : function(data){
                               $('#middle-strip-content').html(generateHtml(data,3));
                   }
               })
               $('#right-strip-content').html('<img src="images/loading.gif">');
               q="work="+4+"&interestId=<?php echo $_GET['interestId']; ?>";
               //alert(q);
               $.ajax({
                   type: "POST",
                   url: "ajax.php",
                   data: q,
                   success : function(data){
                          $('#right-strip-content').html(data);
                   }
               })
              })
     </script>


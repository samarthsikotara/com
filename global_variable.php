<?php

//username= developer_shaukk; password: great_legend_developers

global $current_user;
header('Cache-Control: max-age=604800');
function browser() {
    $ua = strtolower($_SERVER['HTTP_USER_AGENT']);
    // you can add different browsers with the same way ..
    if(preg_match('/(chromium)[ \/]([\w.]+)/', $ua))
            $browser = 'chromium';
    elseif(preg_match('/(chrome)[ \/]([\w.]+)/', $ua))
            $browser = 'chrome';
    elseif(preg_match('/(safari)[ \/]([\w.]+)/', $ua))
            $browser = 'safari';
    elseif(preg_match('/(opera)[ \/]([\w.]+)/', $ua))
            $browser = 'opera';
    elseif(preg_match('/(msie)[ \/]([\w.]+)/', $ua))
            $browser = 'msie';
    elseif(preg_match('/(mozilla)[ \/]([\w.]+)/', $ua))
            $browser = 'mozilla';

    preg_match('/('.$browser.')[ \/]([\w]+)/', $ua, $version);

    return array($browser,$version[2], 'name'=>$browser,'version'=>$version[2]);
}
function isWindowsXP(){
     $browser=browser();
    if (eregi('(Windows NT 5.1)|(Windows XP)', $_SERVER['HTTP_USER_AGENT'])) return true;
    if($browser[0]=="msie") return true;
    else return false;
}
$browser=browser();
//print_r($browser);
if($browser[0]=="msie" && $browser[1]<8){
   ?>
    <script type="text/javascript">
        window.location="http://shaukk.com/browser.php";
    </script>
<?php
}
if($browser[0]=="mozilla" && $browser[1]<3){
    ?>
    <script type="text/javascript">
        window.location="http://shaukk.com/browser.php";
    </script>
<?php
}
date_default_timezone_set('Asia/Calcutta');
?>
 <!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html dir="ltr" xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head prefix="og: http://ogp.me/ns# fb: http://ogp.me/ns/fb# object: http://ogp.me/ns/object#">
<script type="text/javascript">
  var isGuest=true;
  var _gaq = _gaq || [];
  _gaq.push(['_setAccount', 'UA-36849392-1']);
  _gaq.push(['_setDomainName', 'shaukk.com']);
  _gaq.push(['_trackPageview']);

  (function() {
    var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
    ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
  })();
  </script>
<?php
$home_url=getmyurl('index', "");
$tutorial_url=my_pligg_base."/?sk=wall&tutorial=yes";
$main_smarty->assign('tutorial_url', $tutorial_url);
$main_smarty->assign('home_url', $home_url);
if(isWindowsXP()){
    $main_smarty->assign('winXp', 'true');
}else{
    $main_smarty->assign('winXp', 'false');
}
if($current_user->authenticated){
    $main_smarty->assign('isGuest', 'false');
    $main_smarty->assign('profilePic', get_avatar('big', "", "", "", $current_user->user_id));
    $main_smarty->assign('profilePicBig', get_avatar('250', "", "", "", $current_user->user_id));
    $main_smarty->assign('profilePicSmall', get_avatar('50', "", "", "", $current_user->user_id));
    $main_smarty->assign('user_name', $current_user->user_name);
    $sideGroup=get_grouplist_user($current_user->user_id);

    ?>
    <script type="text/javascript">
       // alert('saurav');
        var profilePic = '<?php echo get_avatar('big', "", "", "", $current_user->user_id); ?>';
        var profilePic_small = '<?php echo get_avatar('small', "", "", "", $current_user->user_id); ?>';
        var profilePic_large = '<?php echo get_avatar('large', "", "", "", $current_user->user_id); ?>';
        var profilePic_50 = '<?php echo get_avatar('50', "", "", "", $current_user->user_id); ?>';
        var profile_id = <?php echo $current_user->user_id; ?>;
        var user_name = "<?php echo $current_user->user_name; ?>";
        var base_url = '<?php echo my_pligg_base; ?>';
        isGuest=false;
    </script>


    <?php
}
else{
    ?>
    <script type="text/javascript">
    var base_url = '<?php echo my_pligg_base; ?>';
    </script>
<?php
    $sideGroup=groupforuser(0,15);
    $main_smarty->assign('isGuest', 'true');
}
if(count($sideGroup)>0){
    $i=0;
    foreach($sideGroup as $result){
        $sideGroup[$i]['image_small']=getGroupImage($result['group_id'], '50');
        $sideGroup[$i]['image_big']=getGroupImage($result['group_id'], '100');
        $sideGroup[$i]['url']=getmyurl('group_page',$result['group_id'] );
        $i++;
    }
}

$main_smarty->assign('sidegroups', $sideGroup);
$main_smarty->assign('sidegroups_count', count($sideGroup));
//echo $the_template;
//echo $the_template;
$isMobile=false ;
$mobOpt=false;
if(ismobile()==1 || ( isset($_REQUEST['mob']) &&  $_REQUEST['mob']==1)){ $isMobile=true;}

function getActiveOffers(){
    global $db;
    $sql="SELECT `offer_id`,`plan_id`, `imageName`, `text` FROM ".table_offers." WHERE `isLive`=1";
    //print_r($db->get_results($sql));
    return $db->get_results($sql, ARRAY_A);
}
include_once('libs/fb_api_config.php');
$main_smarty->assign('sliderImages',getActiveOffers());
$main_smarty->assign('postUrl', my_base_url.$_SERVER['REQUEST_URI']);
$fb_config = array(
    'appId' => '221440314656956',
    'secret' => '03d87da0c93c8e0b6ebfd9cfe4835b14'
  );
$fb_perms='publish_stream,email,user_birthday,user_location,user_interests,friends_location';
if($get['return']==""){
    $login_redirect_uri='http://shaukk.com/login.php?referrer=facebook';
}else{
    $login_redirect_uri='http://shaukk.com/login.php?referrer=facebook&fb_return='.$get['return'];
}
$facebook = new Facebook($fb_config);
$params = array(
    'scope' => $fb_perms,
    'redirect_uri' => $login_redirect_uri
  );
$loginUrl = $facebook->getLoginUrl($params);
$main_smarty->assign('shaukk_register', getmyurl('register'));

$main_smarty->assign('fb_login_Url',$loginUrl);
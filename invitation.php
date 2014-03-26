<?php

include_once('Smarty.class.php');
$main_smarty = new Smarty;


include('config.php');
include(mnminclude.'html1.php');
include(mnminclude.'link.php');
include(mnminclude.'tags.php');
include(mnminclude.'search.php');
include(mnminclude.'user_fetch.php');
include(mnminclude.'smartyvariables.php');
include(mnminclude.'facebookapi.php');

define('pagename', 'fb_invitation');
$main_smarty->assign('pagename', pagename);
//displayAllfbfriends();
$res = fbfriendsNotOnshaukk();
//print_r($res);

echo '<ul>';
foreach($res as $value){
    echo '<li><input type="checkbox" name="fb_frnd_list" value="'.$value['frnd_fb_id'].'"><img src="http://graph.facebook.com/'.$value['frnd_fb_id'].'/picture">'.$value['frnd_fb_name'].'</li>';
    //echo $value['frnd_fb_id'];
}
echo '</ul> <input type="button" onclick="sendRequestToRecipients()" value="Invite">';

$main_smarty->display($the_template . '/pligg.tpl');
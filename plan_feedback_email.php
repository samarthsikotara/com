<?php
die;
include_once('Smarty.class.php');
$main_smarty = new Smarty;

include_once('config.php');
include(mnminclude.'html1.php');
include(mnminclude.'link.php');
include(mnminclude.'smartyvariables.php');
include_once(mnminclude.'interest_member.php');
include_once(mnminclude.'interest.php');
include_once(mnminclude.'location.php');
include_once(mnminclude.'group.php');
include_once(mnminclude.'groups.php');
include_once(mnminclude.'user.php');
include_once('global_variable.php');
include_once(mnminclude.'generateHtml.php');

$sql="SELECT `last_run` FROM ".table_email_category." WHERE email_code='feedback_email' ";
$last_run=$db->get_var($sql);
$time_diff=6*3600;
if(time()- strtotime($last_run)<15*60) {echo "too frequent baby....";die;}
$sql1="SELECT * FROM ".table_plan_members." INNER JOIN ".table_links." ON ".table_plan_members.".plan_id = ".table_links.".link_id INNER JOIN ".table_users." ON ".table_users.".user_id = ".table_plan_members.".user_id RIGHT JOIN ".table_interests." ON ".table_links.".link_field1=".table_interests.".interest_id WHERE((UNIX_TIMESTAMP(link_field14)) - UNIX_TIMESTAMP('".$last_run."') > ".$time_diff.") AND (UNIX_TIMESTAMP(link_field14) - UNIX_TIMESTAMP() < ".$time_diff.") AND `status`='Joined'";
//echo $sql1;
$feedback=$db->get_results($sql1, ARRAY_A);
		$array=array();
		foreach($feedback as $plan){
				$invitee=$plan['user_names'];
				$array['inviteename']=$invitee;
				$array['planName']=$plan['link_title'];
				$array['planUrl']= getmyFullurl('events', $plan['link_title_url']);
				$array['commentsUrl']= getmyFullurl('events', $plan['link_title_url'], 'comments');
				$array['unsubUrl']= getmyFullurl('unsubscribe', $plan['user_email']);
				$array['interestName']= $plan['interest_name'];
				$subject = "How was your experience of event -".$array['planName']." via Shaukk";
				$body=generateHTMLBody($array, 'plan_feedback_email');
				send_emailSmtp($plan['user_email'], "", $subject, $body, 'plan_feedback_email');
				//$plan['user_email']
				}
	    $sql4="UPDATE `shemail_category` SET last_run = NOW() WHERE email_code = 'feedback_email' ";
		$last_update=$db->query($sql4);


?>
{include file="../templates/wistie/mail_header.tpl"}
Hi {$inviteeName},<br><br>
{$inviterName} wants you to attend the {$word} {$interestName} plan <b>{$planName}</b> on {$planTime}<br><br>
<a style="background-image: linear-gradient(bottom, rgb(0,145,255) 50%, rgb(0,145,255) 100%);background-image: -o-linear-gradient(bottom, rgb(0,145,255) 50%, rgb(0,145,255) 100%);background-image: -moz-linear-gradient(bottom, rgb(0,145,255) 50%, rgb(0,145,255) 100%);background-image: -webkit-linear-gradient(bottom, rgb(0,145,255) 50%, rgb(0,145,255) 100%);background-image: -ms-linear-gradient(bottom, rgb(0,145,255) 50%, rgb(0,145,255) 100%);background-image: -webkit-gradient(linear,left bottom,left top,color-stop(0.5, rgb(0,145,255)),color-stop(1, rgb(0,145,255)));filter: progid:DXImageTransform.Microsoft.gradient(startColorStr='#4d90fe',EndColorStr='#4787ed');border: 1px solid #0489ed;font-weight:300;text-decoration:none;margin-bottom:5px;color: #fff!important;border-radius:2px;-webkit-border-radius:2px;-moz-border-radius:2px;cursor:default;float:left;padding:6px 12px;" href='{$plan_url}'>View Plan</a>
<a style="margin-left:10px;background-color: #d14836;border: 1px solid transparent;font-weight:300;text-decoration:none;margin-bottom:5px;color: #fff!important;border-radius:2px;-webkit-border-radius:2px;-moz-border-radius:2px;cursor:default;float:left;padding:6px 12px;" href='{$planJoinUrl}'>Click here to join</a>


<br><br>
<p>To view details of the plan or invite your friends to this plan, <a href='{$plan_url}'>Click here</a><br><br></p>
<!--<p>You can now avail multiple exciting offers while creating, joining and sharing cool plans near you at shaukk.<br><br></p>-->
<p>Thank You,<br>
Shaukk Team
</p>

<div style="font-size:10px;color:#999;margin:10px 0;border-top:1px solid #aaa;padding-top:3px;">
To no longer receive email communications from shaukk.com, please <a href='{$unsubUrl}' target="_blank">click here.</a>
</div>
</div>
</div>
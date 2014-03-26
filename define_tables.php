<?php

if(!defined('mnminclude')){header('Location: ../404error.php');die();}

if(!defined('table_prefix')){
	define('table_prefix','');
}
if(!defined('tables_defined')){
	define('table_bookmark', table_prefix . "bookmark" );
    define('table_brands', table_prefix . "brand" );
	define('table_categories', table_prefix . "categories" );
	define('table_comments', table_prefix . "comments" );
	define('table_friends', table_prefix . "friends" );
	define('table_links', table_prefix . "links" );
	define('table_trackbacks', table_prefix . "trackbacks" );
	define('table_users', table_prefix . "users" );
	define('table_tags', table_prefix . "tags" );
	define('table_votes', table_prefix . "votes" );
	define('table_config', table_prefix . "config" ); 
	define('table_modules', table_prefix . "modules" );
	define('table_messages', table_prefix . "messages" );
	define('table_formulas', table_prefix . "formulas" );
	define('table_saved_links', table_prefix . "saved_links" );
	define('table_totals', table_prefix . "totals" );
	define('table_feeds', table_prefix . "feeds" );
	define('table_feed_import_fields', table_prefix . "feed_import_fields" );
	define('table_feed_link', table_prefix . "feed_link" );
	define('table_misc_data', table_prefix . "misc_data" );
	define('table_redirects', table_prefix . "redirects" );
	define('table_groups', table_prefix . "groups" );
        define('table_group_interests', table_prefix . "group_interests" );
	define('table_group_member', table_prefix . "group_member" );
        define('table_group_plan', table_prefix . "group_plan" );
    define('table_group_location', table_prefix . "group_location" );
        define('table_group_invitations', table_prefix . "group_invitations" );
	define('table_group_shared', table_prefix . "group_shared" );
	define('table_pageviews', table_prefix . "pageviews" );
	define('table_tag_cache', table_prefix . "tag_cache" );
	define('table_plan_members', table_prefix . "planmembers" );
	define('table_plan_members_guest', table_prefix . "planmembers_new" );
	define('table_login_attempts', table_prefix . "login_attempts" );
	define('table_widgets', table_prefix . "widgets" );
	define('table_interests', table_prefix . "interests" );
	define('table_interests_type', table_prefix . "interest_type" );
	define('table_interest_member', table_prefix . "interest_member" );
	define('table_google_mail', table_prefix . "google_mail" );
	define('table_plans', table_prefix . "plans" );
	define('table_scribble_comments', table_prefix . "sccomments" );
	define('table_locations', table_prefix . "location" );
    define('table_locations_mumbai', table_prefix . "locations" );
	define('table_scribble', table_prefix . "scribble" );
	define('table_notify_sub', table_prefix . "notifi_sub" );
	define('table_notif_sub_backup', table_prefix . "notifi__sub_backup"     );
	define('table_notify_main', table_prefix . "notif_main" );
	define('table_fkeyword', table_prefix ."_friends_sug_keyword" );
	define('table_frnd_req', table_prefix . "friends_req" );
	define('table_frnd_req_rej', table_prefix . "friends_req_rejected" );
	define('table_reg_key', table_prefix."reg_key");
	define('table_plan_join_key', table_prefix."planjoinkey");
	define('table_fb_user', table_prefix."_fb_userdata");
	define('table_fb_friends', "tbl_fb_frnds");
	define('table_fb_interest', table_prefix."_fb_userdata");
	define('table_notf_text', table_prefix."notf_text");
	define('table_all_attempts', table_prefix."login_all_attempts");
	define('table_report', table_prefix."_report_abuse");
	define('table_appsession', table_prefix."_session");
	define('table_contact', table_prefix."_contact");
	define('table_photo',table_prefix."photos");
	define('table_offers',table_prefix."offers");
	define('table_unsubs_email',table_prefix."unsubscribeemail");
	define('table_news',table_prefix."news_new");
        define('table_news_interest',table_prefix."interest_news");
    define('table_plan_review',table_prefix."plan_review");
    define('table_google_contacts',"tbl_google_contacts");
	define('table_brands',table_prefix."brand_category");
        define('table_brands_ownership',table_prefix."brand_ownership");
	define('table_brand_interest',table_prefix."brand_interest");
	define('table_email',table_prefix."email");
	define('table_videos',table_prefix."videos");
	define('table_email_category',table_prefix."email_category");
    define('table_mail_login',table_prefix."maillogin");
    define('tables_defined', true);
}
?>
	
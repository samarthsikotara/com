<?php
    $youtube_email = "shaukk_admin@shaukk.com"; // Change this to your youtube sign in email.
    $youtube_password = "shaukkeen@!1234"; // Change this to your youtube sign in password.
    // Developer key: Get your key here: http://code.google.com/apis/youtube/dashboard/.
    $key = "AI39si6gge1HIje3W4US7MR6sPSiu05Z5lyS448nwIY4gS-_HrxXpXI-R8ZsX4JNv38abh82wsdx-XFmTG1ZRDE_Cn8hk36d_A";
    
    $source = 'shaukk-shaukk-1.0'; // A short string that identifies your application for logging purposes.
    $postdata = "Email=".$youtube_email."&Passwd=".$youtube_password."&service=youtube&source=" . $source;
    $curl = curl_init( "https://www.google.com/youtube/accounts/ClientLogin" );
    curl_setopt( $curl, CURLOPT_HEADER, "Content-Type:application/x-www-form-urlencoded" );
    curl_setopt( $curl, CURLOPT_POST, 1 );
    curl_setopt( $curl, CURLOPT_POSTFIELDS, $postdata );
    curl_setopt( $curl, CURLOPT_SSL_VERIFYPEER, 0 );
    curl_setopt( $curl, CURLOPT_RETURNTRANSFER, 1 );
    curl_setopt( $curl, CURLOPT_SSL_VERIFYHOST, 1 );
    $response = curl_exec( $curl );
    curl_close( $curl );

    list( $auth, $youtubeuser ) = explode( "\n", $response );
    list( $authlabel, $authvalue ) = array_map( "trim", explode( "=", $auth ) );
    list( $youtubeuserlabel, $youtubeuservalue ) = array_map( "trim", explode( "=", $youtubeuser ) );

    $youtube_video_title = $video_title; // This is the uploading video title.
    $youtube_video_description = $video_description; // This is the uploading video description.   
    $youtube_video_keywords = "demo"; // This is the uploading video keywords.
    $youtube_video_category = "Tech"; // This is the uploading video category. There are only certain categories that are accepted. See below 
    $data = '<?xml version="1.0"?>
                <entry xmlns="http://www.w3.org/2005/Atom"
                  xmlns:media="http://search.yahoo.com/mrss/"
                  xmlns:yt="http://gdata.youtube.com/schemas/2007">
                  <media:group>
                    <media:title type="plain">' . stripslashes( $youtube_video_title ) . '</media:title>
                    <media:description type="plain">' . stripslashes( $youtube_video_description ) . '</media:description>
                    <media:category
                      scheme="http://gdata.youtube.com/schemas/2007/categories.cat">'.$youtube_video_category.'</media:category>
                    <media:keywords>'.$youtube_video_keywords.'</media:keywords>
                  </media:group>
                </entry>';

    $headers = array( "Authorization: GoogleLogin auth=".$authvalue,
                 "GData-Version: 2",
                 "X-GData-Key: key=".$key,
                 "Content-length: ".strlen( $data ),
                 "Content-Type: application/atom+xml; charset=UTF-8" );

$curl = curl_init( "http://gdata.youtube.com/action/GetUploadToken");
curl_setopt( $curl, CURLOPT_USERAGENT, $_SERVER["HTTP_USER_AGENT"] );
curl_setopt( $curl, CURLOPT_RETURNTRANSFER, true );
curl_setopt( $curl, CURLOPT_TIMEOUT, 10 );
curl_setopt( $curl, CURLOPT_SSL_VERIFYPEER, false );
curl_setopt( $curl, CURLOPT_POST, 1 );
curl_setopt( $curl, CURLOPT_FOLLOWLOCATION, 1 );
curl_setopt( $curl, CURLOPT_HTTPHEADER, $headers );
curl_setopt( $curl, CURLOPT_POSTFIELDS, $data );
curl_setopt( $curl, CURLOPT_REFERER, true );
curl_setopt( $curl, CURLOPT_FOLLOWLOCATION, 1 );
curl_setopt( $curl, CURLOPT_HEADER, 0 );

$response = simplexml_load_string( curl_exec( $curl ) );
curl_close( $curl );
?>
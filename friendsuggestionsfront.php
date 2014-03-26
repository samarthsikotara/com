<?php
include_once('Smarty.class.php');
$main_smarty = new Smarty;

header('Content-type: json');
include('config.php');
include(mnminclude.'html1.php');
include(mnminclude.'link.php');
include(mnminclude.'tags.php');
include(mnminclude.'search.php');
include(mnminclude.'user_fetch.php');
include(mnminclude.'smartyvariables.php');
include(mnminclude.'friendsuggestion.php');
include(mnminclude.'location.php');

$friend = new friendsuggestion();

print_r($friend->allweigtagewithname(46,4,''));

?>
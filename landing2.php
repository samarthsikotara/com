<?php

include_once('Smarty.class.php');
$main_smarty = new Smarty;

include('config.php');
include(mnminclude.'html1.php');
include(mnminclude.'smartyvariables.php');
include(mnminclude.'interest.php');

define('pagename', 'landing');
$main_smarty->assign('pagename', pagename);
$main_smarty->assign('tpl_header', $the_template .'/header_landing');

$interest=getLandingPageInterest();
//print_r($interest);

for($i=1; $i<=51; $i++){
    $array[$i]['image'] = getInterestImage   ($interest[$i][0], '50');
    $array[$i]['id'] = $interest[$i][0];

}
$big= array(0, 16, 22, 31, 41);
//print_r($big);
shuffle($array);

//print_r($array);
$array[0]['image']="images/interest/74.jpg";
$array[16]['image']="images/interest/44.jpg";
$array[22]['image']="images/interest/76.jpg";
$array[31]['image']="images/interest/68.jpg";
$array[41]['image']="images/interest/17.jpg";

$array[0]['id']="74";
$array[16]['id']="44";
$array[22]['id']="76";
$array[31]['id']="68";
$array[41]['id']="17";
$i=0;

foreach($array as $item){
    if(in_array($i, $big)){
        $arr[$i]= getInterestImage   ($item['id'], 'original');
        $i++;
        continue;
    }
    $arr[$i]=$item['image'];
    $i++;
}

$main_smarty->assign('array', $arr );
//print_r($array);
$main_smarty->assign('tpl_center', $the_template . '/landing_center');
$main_smarty->display($the_template . '/pligg.tpl');
?>

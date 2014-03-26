<?php
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
include_once(mnminclude.'news.php');
include_once(mnminclude.'test_brand.php');
include_once('global_variable.php');
global $current_user,$db;


$rem_w= array("the", "in", "mumbai", "a", "an", "of", "at", "and", "&", "pvt", "ltd", "pvt.", "ltd.", 'restaurant'. 'cafe');

$id = $_GET['id'];

$sql="SELECT * FROM `shvenues` WHERE id = ".$id." ";
$result=$db->get_results($sql, ARRAY_A);
//print_r($result);
//$latitude="SELECT latitude FROM `shvenues` WHERE id = ".$id." ";
$latitude=$result[0]['latitude'];

//$longitude="SELECT longitude FROM `shvenues` WHERE id = ".$id." ";
$longitude=$result[0]['longitude'];

//$source="SELECT source FROM `shvenues` WHERE id = ".$id." ";
$source=$result[0]['source'];

$lat1 = $latitude - 0.005;
$lng1 = $longitude - 0.005;

$lat2 = $latitude + 0.005;
$lng2 = $longitude + 0.005;

$name=$result[0]['name'];
$words=explode(" ", $name);
//print_r($words);
$words=array_diff($words, $rem_w);
$other_info="SELECT * FROM `shvenues` WHERE latitude BETWEEN ".$lat1." AND ".$lat2." AND longitude BETWEEN ".$lng1." AND ".$lng2." AND source!='".$source."' ";
if(count($words)>0){
        $other_info.="AND ( 1=2 ";
	foreach($words as $word){
		$other_info.=" OR name LIKE '%".strtolower($word)."%' ";
	}
$other_info.=")";
}
//echo $other_info;
$final=$db->get_results($other_info, ARRAY_A);
//print_r($final);


$around=array();
$j=0;
foreach($final as $final_all){
	$around[$j]['id1']=$final_all['id'];
	$around[$j]['name1']=$final_all['name'];
	$around[$j]['address1']=$final_all['address'];
	$around[$j]['desc1']=$final_all['description'];
	$around[$j]['lat1']=$final_all['latitude'];
	$around[$j]['lon1']=$final_all['longitude'];
	$around[$j]['street1']=$final_all['street'];
	$around[$j]['locality1']=$final_all['locality'];
	$around[$j]['city1']=$final_all['city'];
	$around[$j]['country1']=$final_all['country'];
	$around[$j]['pin1']=$final_all['pin'];
	$around[$j]['source1']=$final_all['source'];
	$around[$j]['status1']=$final_all['status'];
	$around[$j]['phone11']=$final_all['phone1'];
	$around[$j]['checkinscount1']=$final_all['checkinscount'];
	$around[$j]['userscount1']=$final_all['userscount'];
	$around[$j]['venue_id1']=$final_all['venue_id'];
	$around[$j]['category1']=$final_all['category'];
	$around[$j]['restricted1']=$final_all['restricted'];
	
	$j++;
	
}


$event=array();
$i=0;

foreach($result as $user){

	$event[$i]['id']=$user['id'];
	$event[$i]['name']=$user['name'];
	$event[$i]['address']=$user['address'];
	$event[$i]['desc']=$user['description'];
	$event[$i]['lat']=$user['latitude'];
	$event[$i]['lon']=$user['longitude'];
	$event[$i]['street']=$user['street'];
	$event[$i]['locality']=$user['locality'];
	$event[$i]['city']=$user['city'];
	$event[$i]['country']=$user['country'];
	$event[$i]['pin']=$user['pin'];
	$event[$i]['source']=$user['source'];
	$event[$i]['status']=$user['status'];
	$event[$i]['phone1']=$user['phone1'];
	$event[$i]['checkinscount']=$user['checkinscount'];
	$event[$i]['userscount']=$user['userscount'];
	$event[$i]['venue_id']=$user['venue_id'];
	$event[$i]['category']=$user['category'];
	$event[$i]['restricted']=$user['restricted'];
	$event[$i]['venue_id']=$user['venue_id'];
	$event[$i]['interest']=$user['interest'];
	$event[$i]['interest1']=$user['interest1'];
	$event[$i]['interest2']=$user['interest2'];
	$event[$i]['interest3']=$user['interest3'];
	$event[$i]['interest4']=$user['interest4'];
	$event[$i]['interest5']=$user['interest5'];
	$event[$i]['interest6']=$user['interest6'];
	$event[$i]['interest7']=$user['interest7'];
	$event[$i]['interest8']=$user['interest8'];
	$event[$i]['interest9']=$user['interest9'];
	$event[$i]['interest10']=$user['interest10'];
	$event[$i]['interest11']=$user['interest11'];
	$event[$i]['interest12']=$user['interest12'];
	$event[$i]['interest13']=$user['interest13'];
	$event[$i]['interest14']=$user['interest14'];
	$i++;

}

//Venue_interest_List



//----//
if(isset($_POST['save']) && $_POST['save'] == 'Submit'){

	$venue_id = sanitize(strip_tags($_POST['id1'],3));

	$name= sanitize(strip_tags($_POST['name']),3);
	$interest_name = sanitize(strip_tags($_POST['interest']),3);
	$desc = sanitize(strip_tags($_POST['desc']),3);
	$address = sanitize(strip_tags($_POST['address'],3));
	$street = sanitize(strip_tags($_POST['street']),3);
	//echo $street;
	$locality = sanitize(strip_tags($_POST['locality']),3);
	$city = sanitize(strip_tags($_POST['city']),3);
	$country = sanitize(strip_tags($_POST['country']),3);
	$pin = sanitize(strip_tags($_POST['pin']),3);
	$checkinscount = sanitize(strip_tags($_POST['checkinscount']),3);
	$phone = sanitize(strip_tags($_POST['phone1']),3);
	
	$interest = sanitize(strip_tags($_POST['interest']),3);
	$interest1 = sanitize(strip_tags($_POST['interest1']),3);
	$interest2 = sanitize(strip_tags($_POST['interest2']),3);
	$interest3 = sanitize(strip_tags($_POST['interest3']),3);
	$interest4 = sanitize(strip_tags($_POST['interest4']),3);
	$interest5 = sanitize(strip_tags($_POST['interest5']),3);
	$interest6 = sanitize(strip_tags($_POST['interest6']),3);
	$interest7 = sanitize(strip_tags($_POST['interest7']),3);
	$interest8 = sanitize(strip_tags($_POST['interest8']),3);
	$interest9 = sanitize(strip_tags($_POST['interest9']),3);
	$interest10 = sanitize(strip_tags($_POST['interest10']),3);
	$interest11 = sanitize(strip_tags($_POST['interest11']),3);
	$interest12 = sanitize(strip_tags($_POST['interest12']),3);
	$interest13 = sanitize(strip_tags($_POST['interest13']),3);
	$interest14 = sanitize(strip_tags($_POST['interest14']),3);
	
	$interest_id = sanitize(strip_tags($_POST['interest_id']),3);
	//echo $interest_id;
	$interest_id1 = sanitize(strip_tags($_POST['interest_id1']),3);
	
	$interest_id2 = sanitize(strip_tags($_POST['interest_id2']),3);
	$interest_id3 = sanitize(strip_tags($_POST['interest_id3']),3);
	$interest_id4= sanitize(strip_tags($_POST['interest_id4']),3);
	$interest_id5 = sanitize(strip_tags($_POST['interest_id5']),3);
	$interest_id6 = sanitize(strip_tags($_POST['interest_id6']),3);
	$interest_id7 = sanitize(strip_tags($_POST['interest_id7']),3);
	$interest_id8= sanitize(strip_tags($_POST['interest_id8']),3);
	$interest_id9 = sanitize(strip_tags($_POST['interest_id9']),3);
	$interest_id10 = sanitize(strip_tags($_POST['interest_id10']),3);
	$interest_id11 = sanitize(strip_tags($_POST['interest_id11']),3);
	$interest_id12 = sanitize(strip_tags($_POST['interest_id12']),3);
	$interest_id13 = sanitize(strip_tags($_POST['interest_id13']),3);
	$interest_id14 = sanitize(strip_tags($_POST['interest_id14']),3);
	$file_name = $_FILES['image_file'];
	//print_r($file_name);
	
	$interests = array($interest_id,$interest_id1,$interest_id2,$interest_id3,$interest_id4,$interest_id5,$interest_id6,$interest_id7,$interest_id8,$interest_id9,$interest_id10,$interest_id11,$interest_id12,$interest_id13,$interest_id14);
	//print_r($interests[0]);
	
	

		//global $db, $current_user;
		$sql_update="UPDATE `shvenues` SET name = '".$name."',checkinscount = '".$checkinscount."',description = '".$desc."',street = '".$street."',locality = '".$locality."',city = 'Mumbai',country = 'India',pin = '".$pin."',phone1 = '".$phone."',interest = '".$interest."',interest1 = '".$interest1."',interest2 = '".$interest2."',interest3 = '".$interest3."',interest4 = '".$interest4."',interest5 = '".$interest5."',interest6 = '".$interest6."',interest7 = '".$interest7."',interest8 = '".$interest8."',interest9 = '".$interest9."',interest10 = '".$interest11."',interest12 = '".$interest12."',interest13 = '".$interest13."',interest14 = '".$interest14."' WHERE venue_id = '".$venue_id."' ";
		//echo $sql_update;
		$db->query($sql_update);
	$interests = array($interest_id,$interest_id1,$interest_id2,$interest_id3,$interest_id4,$interest_id5,$interest_id6,$interest_id7,$interest_id8,$interest_id9,$interest_id10,$interest_id11,$interest_id12,$interest_id13,$interest_id14);
		//print_r($interests[1]);
					for($i=0;$i<15;$i++)
					{				
						if(!is_null($interests[$i])) {
								$sql_insert="INSERT INTO `shvenue_interest`(venue_id,interest_id) VALUES ('".$venue_id."',".$interests[$i].")";
								//echo $sql_insert;
								$db->query($sql_insert);
						}
					}
						
/* Image Upload */
	
	//$output = '';
        $user_image_path = "avatars/venue_uploaded" . "/";
        $user_image_apath = "/" . $user_image_path;
        //echo $user_image_apath;
		
		$allowedFileTypes = array("image/jpeg","image/gif","image/png",'image/x-png','image/pjpeg');
        //echo $file_name;
		if(!is_array($file_name)) return false;
        
		$myfile = $file_name['name'];
        $imagename = basename($myfile);
		//echo $imagename;
        $mytmpfile = $file_name['tmp_name'];
        $imagesize = getimagesize($mytmpfile);
       	//echo "hii11";
	    //print_r($imagesize);
        //print_r($file_name);
        //die;
        
		if(!in_array($file_name['type'],$allowedFileTypes)){
            $error['Type'] = 'Only these file types are allowed : jpeg, gif, png';
        }
        else if (empty($imagesize) || $file_name['size']>1024*1024*10){
            $error['size'] = 'Invalid Size. Greater than 10MB';
        }
		
        //print_r($error);
        //die;
        if(empty($error)){

            $width = $imagesize[0];
            $height = $imagesize[1];

            $imagename = $venue_id . "_original.jpg";

            $newimage = $user_image_path . $imagename ;

            $result1 = @move_uploaded_file($file_name['tmp_name'], $newimage);
            if(empty($result1))
                $error["result"] = "There was an error moving the uploaded file.";
        }



        // create large avatar
        include "class.pThumb.php";
        $img=new pThumb();
        $img->pSetSize(50, 50);
        $img->pSetQuality(100);
        $img->pCreate($newimage);
        $img->pSave($user_image_path . $venue_id . "_50.jpg");
        $img = "";

       // create small avatar
        $img=new pThumb();
        $img->pSetSize(250, 250);
        $img->pSetQuality(100);
        $img->pCreate($newimage);
        $img->pSave($user_image_path . $venue_id . "_250.jpg");
        $img = "";

        $img=new pThumb();
        $img->pSetSize(100, 100);
        $img->pSetQuality(100);
        $img->pCreate($newimage);
        $img->pSave($user_image_path . $venue_id . "_100.jpg");
        $img = "";

        $img=new pThumb();
        $img->pSetSize($imagesize[0], $imagesize[1]);
        $img->pSetQuality(100);
        $img->pCreate($newimage);
        $img->pSave($user_image_path . $venue_id . "_original.jpg");
        $img = "";
        unset($newimage);
		/*
        if(isset($error) && is_array($error)) {
            while(list($key, $val) = each($error)) {
                $output.= $val;
                $output.= "<br>";
            }
        }
        */
	/****************/				

}

$main_smarty->assign('event', $event);
$main_smarty->assign('around', $around);



$main_smarty->assign('tpl_header', $the_template . '/header');
            $main_smarty->assign('tpl_center', $the_template . '/eventful_venue_display');

            $main_smarty->display($the_template . '/pligg.tpl');

?>	
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
include_once(mnminclude.'photo.php');

include_once('global_variable.php');
global $current_user,$db;

//set to true if this page has mobile template
$mobOpt=false;
//og-title
$main_smarty->assign('og_posttitle', 'Bawaras');
//Site Title
$main_smarty->assign('posttitle', 'Bawaras');
//met description
$main_smarty->assign('description', '');
//og-description
$main_smarty->assign('og_content', '');
//og-image
$main_smarty->assign('postImage', 'http://shaukk.com/avatars/user_uploaded/1_original.jpg');

if ($current_user->user_id > 0 && $current_user->authenticated)
    $login=$current_user->user_login;
else{
    header('Location: '.$my_base_url.$my_pligg_base.'/bawaras.php');
    die;
}

$user=new User();
//if(isset($login))$user->username = $login;
if(isset($id))$user->id=$id;
$user->id=$current_user->user_id;
$user_id=$user->id;
$photo=new Photo();

	if(isset($_POST['save']) && $_POST['save']=='enter') {
	
		//echo "hi";
		//print_r($_POST);
		$files=$_FILES['bawaras-images'];
		//print_r($_FILES);
		
		if(!is_numeric($user_id)) return false;
		$count=count($_FILES['bawaras-images']['name']);
		echo $count;
		//print_r($_FILES);
		
		for($i=0; $i<$count; $i++){
        $photo=new Photo();
        $array['tmp_name']=$files['tmp_name'][$i];
        $array['name']=$files['name'][$i];
        $array['error']=$files['error'][$i];
        $array['type']=$files['type'][$i];
        $array['size']=$files['size'][$i];
        $photo->photo=$array;
		//print_r($array);
		
        $photo->typeName='bawaras';
        $photo->typeId=$user_id;
		$photo->userId=$current_user->user_id;
		//print_r($photo->uploadPhoto());
		
        if($photo->uploadPhoto()){
            $return[$i][0]="success";
            $return[$i][1]=my_pligg_base."/image.php?photoId=".$photo->photoid;
        }else{
            $return[$i][0]="failed";
            $result[$i][0]['error']=$photo->error;
        }
        $photo="";
    }
	echo json_encode($return);
	
						
	}
	
	$photos=$photo->getPhotosByPlan($planId=27);
	//print_r($photos);
	//echo "hi";
    $i=0;
	if($photos){
    	$main_smarty->assign('photos_count', count($photos));
		$main_smarty->assign('photos', $photos);
	}else{
    	$main_smarty->assign('photos_count', 0);
	}
	$current_user=$currerent->user_id;
	$delete = $photo->deletePhoto();
	//echo $delete;
	
$main_smarty->assign('tpl_header', $the_template . '/header');
$main_smarty->assign('tpl_center', $the_template . '/bawaras_sample');

$main_smarty->display($the_template . '/pligg.tpl');


?>
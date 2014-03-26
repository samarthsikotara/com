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


//set to true if this page has mobile template
$mobOpt=false;
//og-title
$main_smarty->assign('og_posttitle', 'Shaukk Brands');
//Site Title
$main_smarty->assign('posttitle', 'Brands');
//met description
$main_smarty->assign('description', '');
//og-description
$main_smarty->assign('og_content', '');
//og-image
$main_smarty->assign('postImage', 'http://shaukk.com/avatars/user_uploaded/1_original.jpg');


if(isset($_POST['submit'])=='Search source code'){

$search = htmlentities(trim($_POST['search'])); 

// new code 

//echo $search;

	$error=array();

	if(empty($search))
	{
		$error[]="please Enter the Proper Term";
	}
	else if(strlen($search)<1)
	{
		$error[]="Your Search keyword has atleast 1 word";	
	}
	/*
	else if(search_results($search)==false)
	{
		$error[]="Your search for ".$search." returned no results";
	}
	*/
	if(empty($error))
	{
		// Search
		//search_results($search);
		$returned_results=array();
		
		$where="";
		$search=preg_split('/[\s]+/', $search);
		print_r($search);
		$total_keywords=count($search);
		
		foreach($search as $key=>$keyword){
		
		$where .=" location_name LIKE '%$keyword%' ";
			if($key != ($total_keywords - 1))
			{
				$where .= "AND";
			}
		}
		//echo $where;
		$result = "SELECT * FROM `shusers` INNER JOIN `shlocation` ON `shusers`.user_id=`shlocation`.user_id  WHERE  ".$where." AND user_sex='B' ";
		
		echo $result;
		$results=$db->get_results($result, ARRAY_A);
		print_r($results);
		//echo "hi";
		$brand=array();

			foreach($results as $result){

				$brand['title']=$result['user_names'];
				$brand['url']=getmyFullurl('profileId',$result['user_id']);
				$brand['descriptions']=$result['user_desc'];

				print_r($brand['title']);

			}

		
	}
	else
	{
		foreach($error as $er)
		{
			echo $er."<br />";
		}
	}
	
	
			
/*
$errors=array();

$search_exploded = explode (" ", $search);

foreach($search_exploded as $search_each)
{
$x++;
if($x==1)

$construct .="INNER JOIN `shlocation` ON `shusers`.user_id=`shlocation`.user_id WHERE location_name LIKE '%$search_each%' ";

else
$construct .="INNER JOIN `shinterest_member` ON `shinterest_member`.user_id=`shusers`.user_id INNER JOIN `shinterests` ON `shinterests`.interest_id=`shinterest_member`.interest_id WHERE interest_name LIKE '%$search_each%'";
   
}


$sql="SELECT * FROM `shusers` $construct AND user_sex='B'";
$results=$db->get_results($sql, ARRAY_A);
//print_r($results);
echo $sql;

$brand=array();

foreach($results as $result){

$brand['title']=$result['user_names'];
$brand['url']=getmyFullurl('profileId',$result['user_id']);
$brand['descriptions']=$result['user_desc'];

print_r($brand['title']);

}
*/

}
$main_smarty->assign('brand',$brand);
	
$main_smarty->assign('tpl_header', $the_template . '/header');
$main_smarty->assign('tpl_center', $the_template . '/brand_search');

$main_smarty->display($the_template . '/pligg.tpl');

?>
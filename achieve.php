<?php
if(!defined('mnminclude')){header('Location: ../404error.php');die();}

global $db, $current_user;
class achievements
{

//global $db, $current_user;
var $No_Of_Plans_Created=0;
var $No_Of_Scribble_Created=0;
var $No_Of_Plans_Joint=0;
var $No_Of_Offers_Taken=0;
var $No_Of_Friends=0;
var $Final_Score=0;
$sql1="SELECT  FROM shachievements WHERE Type='No_Of_Plans_Created'";	
$mul=$db->get_var($sql1);

public function No_Of_Plans_Created()
{
global $db, $current_user;
$this->No_Of_Plans_Created=1;
$sql1="SELECT weight FROM shachievements WHERE Type='No_Of_Plans_Created'";	
$mul=$db->get_var($sql1);
$sql="SELECT count(link_id) from  shlinks WHERE link_author = 50 ";
$plans = $db->get_var($sql);
$score= ($No_Of_Plans_Created * $mul);
$Final_Score=$score+$mul;
}

//echo $score;

public function No_Of_Scribble_Created()
{
global $db, $current_user;
$No_Of_Scribble_Created=1;
$sql1="SELECT weight FROM shachievements WHERE Type='No_Of_Scribble_Created'";	
$mul=$db->get_var($sql1);
$sql1="SELECT count(scribble_id) FROM  ".table_scribble." WHERE scribble_author = 29";
$No_Of_Scribble_Created=$db->get_var($sql1);
$score= ($No_Of_Scribble_Created * $mul);
$Final_Score=$score+$mul;
}
public function updateScore(){


if(is_numeric($this->No_Of_Plans_Created) && $this->No_Of_Plans_Created>0){
	$array[$i]['type']='No_Of_Plans_Created';
	$array[$i]['value']=$this->No_Of_Plans_Created;
	$i++;
	
}

if(is_numeric($this->No_Of_Scribble_Created) && $this->No_Of_Plans_Created>0){
	$array[]='No_Of_Scribble_Created';
	$i++;
}

if(!empty($array)){
	$sql3="SELECT `Weight`,`Type` FROM  ".table_achievements." WHERE `Type` IN (";
	foreach ($array as $item){
		$sql3.="'".$item."',";
	}
	$sql3=subsstring($sql3, 0, -1);
	$sql3.=")";
	// find the total score
	
	
	// upate the score in the users table 
	return true;

}






	//echo $No_Of_Scribble_Created;
	return $score;
	//return $score1;

}

}

?>


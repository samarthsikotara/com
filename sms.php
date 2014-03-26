<?php
class SMS{

$username="";
$password="";
$sms_type="";
$sms="";
$limit_sms_char="50";
$mobile="";
$status="";
$error="";
$type="";

function isValidate(){

	if(!isValidPhone($phone)){
        
        $error=true;
        return $errorMssg="Register_Error_Phone";
		}
	if($limit_sms_char > 50)
	{	
		$error=true;
        return $errorMssg="Enter Proper limit of sms size";
		
	}
	if($status!=0 || $status!=1)
	{
			
        return $errorMssg="Enter Proper Status Value";
	
	}
		

}

function sendSMS(){
	//Use SMS API code here 
	//check if the user is subscribed or not
	 if($this->isNumberUnsubscribed())
	 {
	 	$this->unsubscribed();
	 }
	//send sms
	$sms="INSERT into ".table_sms." () Values()";
}
function isNumberUnsubscribed(){


	if($this->status==0)
	{
		$this->unsubscribe();
	}


}

function unsubscribe(){
//$sms="You are unsubscribed for this Service";
// if mobile number is subscribed for that type or not
	if(!$this->isNumberUnsubscribed()){
	
		$mobile=$db->escape($this->mobile);
		$status=$db->escape($this->status);
		$type=$db->escape($this->type);
		$sql="INSERT INTO ".table_sms_unsubscribe." (phoneno,status,type) VALUES ('$mobile','$status','$type')";
		$results=$db->query($sql);
		return true;
	}
}
function subscribe(){

		$mobile=$db->escape($this->mobile);
		$status=$db->escape($this->status);
		$type=$db->escape($this->type);
		$sql="INSERT INTO ".table_sms." (phoneno,status,type) VALUES ('$mobile','$status','$type')";
		$results=$db->query($sql);
		return true;
		
}





}

?>
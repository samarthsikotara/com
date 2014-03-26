<?php

// read from table the mails to be sent 
$sql="SELECT * FROM ".table_email." WHERE status = 0 ";
$result=$db->get_results($sql,ARRAY_A);

// loop in each item
foreach($result as $ans){

	$subject=$ans['subject'];
	$to=$ans['email'];
	$body=$ans['content'];
	$name=$ans['name'];
	if(sendEmail($subject,$body,$to,$name))
	{
		$status=1;
		$sql1="UPDATE ".table_email." SET status = ".$status." AND senttime = NOW() WHERE email = ".$to." ";
		$db->query($sql1);

	}
	else
	{
		//echo "Email sent Failed";
	}
	
	}

	//send mail to idividal
	// update the individual row

function sendEmail($subject,$body,$to,$name){

//send the mail from the Function
require_once(mnminclude."../phpmailer/class.phpmailer.php");
        $mail  = new PHPMailer();
        $mail->IsSMTP();                            // telling the class to use SMTP
        $mail->Host       = "smtp.gmail.com";       // SMTP server
        $mail->SMTPDebug  = 0;                      // enables SMTP debug information (for testing)
        $mail->SMTPAuth   = true;
        $mail->isHTML(true);
        $mail->SMTPSecure = "ssl";                  // enable SMTP authentication
        $mail->Port       = 465;                    // set the SMTP port for the GMAIL server
        if($type=="NONE"){
            $mail->Username   = "info@shaukk.com";   // SMTP account username
            $mail->Password   = "shaukk_info";      // SMTP account password
            $mail->SetFrom('info@shaukk.com', 'Shaukk');
        }else{
            $mail->Username   = "noreply@shaukk.com";   // SMTP account username
            $mail->Password   = "shaukk_no-reply";      // SMTP account password
            $mail->SetFrom('noreply@shaukk.com', 'Shaukk');
        }

        $mail->Subject    = $subject;
        $mail->MsgHTML($body);
        $mail->AddAddress($to, $name);
        //die($body);
        //echo "sending mail.....";
        if($mail->send())return true;
          else return false;
		  

}

?>
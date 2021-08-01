<?php
        function SendMail($to,$message,$subject,$ccIds=''){
                date_default_timezone_set('Asia/Calcutta');
                require_once('class.phpmailer.php');
                $from = 'srsmngfgcb@gmail.com';
                $mail             = new PHPMailer();
                $mail->IsSMTP(); // telling the class to use SMTP
                $mail->Host       = "smtp.gmail.com"; // SMTP server
                $mail->SMTPDebug  = 0;                     // enables SMTP debug information (for testing)
                // 1 = errors and messages
                // 2 = messages only
                $mail->SMTPAuth   = true;                  // enable SMTP authentication
                $mail->SMTPSecure = "ssl";                 // sets the prefix to the servier
                $mail->Host       = "smtp.gmail.com";      // sets GMAIL as the SMTP server
                $mail->Port       = 465;                   // set the SMTP port for the GMAIL server
                $mail->Username   = "srsmngfgcb@gmail.com";  // GMAIL username
                $mail->Password   = "Goutham1998";            // GMAIL password
                $mail->SetFrom('srsmngfgcb@gmail.com', 'no-reply@gfgcb.com');
                $mail->AddReplyTo($from, 'no-reply@gfgcb.com');
                $mail->Subject = $subject;
                $mail->MsgHTML($message);
                if(is_array($to)){
                    // mail to multiple recipients
                    foreach ($to as $tomail){
                        $mail->AddAddress($tomail, "");
                    }
                }  else {
                     // mail to single recipient
                    $mail->AddAddress($to, "");
                }
                    if(is_array($ccIds)){
                        foreach($ccIds as $eachCCId){
                            $mail->AddCC($eachCCId, "");
                        }
                }  else {
                    $mail->AddCC($ccIds, "");
                    }
                if(!$mail->Send()) {
                    $errormsg = "Mailer Error: " . $mail->ErrorInfo;
                } else {
                    $errormsg = "sent";
                }   
            return $errormsg;
        }
?>

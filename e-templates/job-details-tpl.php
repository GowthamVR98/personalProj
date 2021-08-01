<?php
include('./includes/smtp-mailer-function.php');
function mailTemplate($to, $subject, $password, $to_name) {
$msgs = '
    <!DOCTYPE html>
    <html>
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1">
            <meta content="text/html; charset=utf-8" http-equiv="Content-Type">
            <title>Email for Student Absence</title>
        </head>
        <body style="font-family:verdana">
            <div style="width:700px;max-width: 100%;margin: 0 auto ;text-align: center;background-color:#4a883f;">
                <div style="padding:6% 5% 0">
                    <div style="border-radius:46px 46px 0 0;background-color:#fff;padding:0 6% 15px">
                        <div style="float:left;padding:20px 0 10px;vertical-align:middle">
                            <a href="www.brita.in" target="_blank"><img src="srsmngfgcbalumniportal.000webhostapp.com/images/Alumni-Logo.png" alt="AlumniPortal"></a>
                        </div>
                        <div style="clear: both"></div>
                        <div style="border-bottom:2px solid #eeeeee"></div>
                        <div style="line-height:22px">
                            <p style="text-align: left">Dear User, </p>
                            <p style="font-size:14px;text-align:justify;margin:5px 10px">New Job added by Admin</p>
                            <p style="font-size:14px;text-align:justify;margin:5px 10px"></p>
                        </div><br>
                        <div>
                            <p style="text-align:left;font-size:14px;margin: 0px;"><strong>Regards,</strong></p>
                            <p style="text-align:left;font-size:14px;margin: 0px;">srsmngfgcb@gmail.com</p>
                        </div><br>
                        <div style="border-bottom:2px solid #eeeeee"></div>
                        <span style="font-size:11px;">This email ID is not monitored for any responses</span>
                    </div>  
                    <footer style="padding: 20px 0;margin:0;text-align: center">
                    </footer>
                </div>
            </div>
        </body>
    </html>';
    SendMail($to, $msgs, $subject);
    return true;
}
$subject = "Login Credentials";
mailTemplate(trim($_POST['txtEmail']), $subject, $password['decripted'], trim($_POST['txtName']));


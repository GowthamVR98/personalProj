<?php
class mailCron {
    private $dbobj;
    private $login_session_id;
    private $dbOption;

    function __construct($dbobj, $login_session_id = NULL,$dbOption = NULL) {
        $this->dbobj = $dbobj;
        $this->login_session_id = $login_session_id;
        $this->dbOption = $dbOption;
    }

    public function insertCronJobData($subject, $toMailIdWithName = array(), $content = array(), $ccMailId = array(array())) {
        // parameter format 
        // $subject = 'subject',
        // $toMailIdWithName = array('mailid1{-}name1','mailid2{-}name2'),
        // $content = array('content1','content2'),
        // $ccMailId = array(array('cc11','cc12'),array('cc21','cc22'))
        foreach ($toMailIdWithName as $key => $singleMailAddress) {
            if (!empty($content[$key])) {
                $contentToDB = $content[$key];
            } else {
                $contentToDB = NULL;
            }
            if (!empty($ccMailId[$key])) {
                $ccMailIdToDB = implode(',', $ccMailId[$key]);
            } else {
                $ccMailIdToDB = NULL;
            }
            $values = array( $subject, $singleMailAddress, $ccMailIdToDB, $contentToDB, 0,CURRENT_DT, $this->login_session_id,$_SERVER['REMOTE_ADDR']);
            $this->dbOption->InsertQuery($this->dbobj, TBL_PRIFIX.'mail_cronjob', array('subject','to_email','cc_email','content','flag','added_date','added_by','added_ip'), $values);
        }
        return TRUE;
    }
    public function getUserData($db, $id,$condition) {
        $result = $this->dbOption->selectData($this->dbobj, TBL_PRIFIX.'user_login_details', array('concat_ws("{-}",email_id,fname) as mailData'),$condition, array($id));
        return  $result;
    } 
    private function getMailTemplate($toMail, $toName, $content, $subject, $ccMailIds) {
        $msg = '
         <!DOCTYPE html>
            <html>
                <head>
                <meta charset="UTF-8">
                <meta name="viewport" content="width=device-width, initial-scale=1">
                <meta content="text/html; charset=utf-8" http-equiv="Content-Type"/>
                <title>Email for Student Absence</title>
                </head>
                <body style="font-family:verdana">
                    <div style="width:700px;max-width: 100%;margin: 0 auto ;text-align: center;background-color: #2e609c;">
                        <div style="padding:6% 5% 0">
                            <div style="border-radius:46px 46px 0 0;background-color:#fff;padding:0 6% 15px">
                                <div style="float:left;padding:20px 0 10px;vertical-align:middle">
                                    <a href="http://srsmngfgcbalumniportal.000webhostapp.com" target="_blank" ><img src="http://srsmngfgcbalumniportal.000webhostapp.com/images/Alumni-Logo.png" alt="GFGCB"></a>
                                </div>
                                <div style="clear: both"></div>
                                <div style="border-bottom:2px solid #eeeeee"></div>
                                <div style="line-height:22px">
                                    <p style="text-align: left">Dear ' . ucfirst($toName) . ', </p>
                                     <p style="text-align:left;font-size:14px">'.$content.'</p>
                                </div>
                                    <div>
                                    <p style="text-align:left;font-size:14px;margin: 0px;"><strong>Regards,</strong></p>
                                    <p style="text-align:left;font-size:14px;margin: 0px;">SRSMNGFGCB</p>
                                </div>
                                <div style="border-bottom:2px solid #eeeeee"></div>
                                <span style="font-size:13px;"><a href="index" title="Click here to login"> Click here to login</a> </span><br>
                                <span style="font-size:11px;">This email ID is not monitored for any responses</span>
                            </div>  
                            <footer style="padding: 20px 0;margin:0;text-align: center">
                            </footer>
                        </div>
                    </div>
                </body>
            </html>';
        $return = SendMail($toMail,$msg,$subject,$ccMailIds);
        return $return;    
    }
    private function updateCronJob($cronjobId){ // function to update mail_cronjob table flag value
        $flagValue = 1;
        $update = "UPDATE alumni_mail_cronjob SET flag=:flag WHERE cron_job_id=:cronjob_id";
        $updateCronjob = $this->dbobj->prepare($update);
        $updateCronjob->bindParam(':flag', $flagValue, PDO::PARAM_STR);
        $updateCronjob->bindParam(':cronjob_id', $cronjobId, PDO::PARAM_INT);
        $updateCronjob->execute();
        $cronjobUpdateCount = $updateCronjob->rowCount();
        if($cronjobUpdateCount > 0){
            $status = 'sent';
        }  else {
            $status = 'failed';
        }
        return $status;
    }
    public function callCronJobData() {
        $flagValue = 0;
        $cronjobData = $this->dbobj->prepare("SELECT * FROM alumni_mail_cronjob WHERE flag=:flag");
        $cronjobData->bindParam(':flag', $flagValue, PDO::PARAM_STR);
        $cronjobData->execute();
        $cronjobCount = $cronjobData->rowCount();
        if ($cronjobCount > 0) {
            while ($cronjobrow = $cronjobData->fetch()) {
                $ccMailIds = '';
                $pos = strpos($cronjobrow['to_email'], '{-}');
                if (!empty($pos)) {
                    list($toMail, $toName) = explode('{-}', $cronjobrow['to_email']);
                } else {
                    $toMail = explode(',', $cronjobrow['to_email']);
                    $toName = 'Customer';
                }
                if (!empty($cronjobrow['cc_email'])) {
                    $ccMailIds = explode(",", $cronjobrow['cc_email']);
                }
                $result = $this->getMailTemplate($toMail, $toName, $cronjobrow['content'], $cronjobrow['subject'], $ccMailIds);
                if($result == TRUE){ //update cronjob flag column
                    $this->updateCronJob($cronjobrow['cron_job_id']);
                }
            }
        }
        return TRUE;
    }
}

?>
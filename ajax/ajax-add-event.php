<?php
include('../includes/admin-config.php');
$dbobj = new GetCommomOperation();
$loginSessionId = $dbobj->getSessionId();
if(isset($_POST['title'])){
    $title = isset($_POST['title']) ? $_POST['title'] : "";
    $description = isset($_POST['description']) ? $_POST['description'] : "";
    $start = isset($_POST['start']) ? $_POST['start'] : "";
    $end = isset($_POST['end']) ? $_POST['end'] : "";
    $userType = isset($_POST['eventType']) ? $_POST['eventType'] : "";
    if($userType == 'alumni'){
        $backGroundColor = '#E91E63';
        $borderColor = '#E91E63';
    }else if($userType == 'student'){
        $backGroundColor = '#00BCD4';
        $borderColor = '#00BCD4';
    }else if($userType == 'staff'){
        $backGroundColor = '#FF9800';
        $borderColor = '#FF9800';
    }else if($userType == 'all'){
        $backGroundColor = '#9C27B0';
        $borderColor = '#9C27B0';
    }
    $value = array(
    	$title,$description,$start,$end,$userType,$backGroundColor,$borderColor,CURRENT_DT,$loginSessionId,$_SERVER['REMOTE_ADDR']
    );
    $InserEvent = $dbobj->InsertQuery($dbh, TBL_PRIFIX.'events', array('title','description','start','end','event_type','backgroundColor','borderColor','date_added','added_by','added_ip'), $value);
     if($InserEvent['status']){
        $_SESSION['success'] = 'Event has been successfully added.';
        echo json_encode(array('status' => true,'msg' => 'Event has been successfully added.'));
     }else{
        $_SESSION['error'] = 'Event could not added.';
     	echo json_encode(array('status' => false,'msg' => 'Event could not added.'));
     }
}
?>
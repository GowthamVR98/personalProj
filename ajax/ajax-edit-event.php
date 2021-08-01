<?php
include('../includes/admin-config.php');
$dbobj = new GetCommomOperation();
$loginSessionId = $dbobj->getSessionId();
if(isset($_POST['id'])){
    $update = $dbobj->UpdatetData($dbh, TBL_PRIFIX.'events', 'title = ?, start = ?,end = ?,date_modified = ?, modified_by = ?, modified_ip = ?', 'id = ?', 
        array(trim($_POST['title']),trim($_POST['start']),trim($_POST['end']),CURRENT_DT,$loginSessionId,$_SERVER['REMOTE_ADDR'],$_POST['id'])
        );
        if($update){
            $_SESSION['success'] = 'Event has been updated successfully.';
        }else{
            $_SESSION['error'] = 'Event could not updated.';
        }
        exit(0);
}

?>
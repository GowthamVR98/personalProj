<?php
include('../includes/admin-config.php');
$dbobj = new GetCommomOperation();
if(isset($_POST['id'])){
    $result = $dbobj->DeleteData($dbh, TBL_PRIFIX.'events', 'id = ?', array($_POST['id']));
    if($result){
    	 $_SESSION['success'] = 'Event has been deleted successfully.';
    }else{
    	 $_SESSION['error'] = 'Event could not deleted.';
    }
    exit(0);
}
?>
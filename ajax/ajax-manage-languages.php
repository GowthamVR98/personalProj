<?php
    include('../includes/admin-config.php');
    $dbobj = new GetCommomOperation();
    $loginSessionId = $dbobj->getSessionId();
    if(isset($_POST['delete'])){
       $result = $dbobj->DeleteData($dbh, TBL_PRIFIX.'language_master', 'language_id = ?', array($_POST['delete']));
        if($result){
            $_SESSION['success'] = 'Language has been deleted successfully.';
            echo json_encode(array('status' => true));
        }else{
            $_SESSION['error'] = 'Language could not deleted.';
            echo json_encode(array('status' => false));
        }
    }else if(isset($_POST['enable'])){
        $update = $dbobj->UpdatetData($dbh, TBL_PRIFIX.'language_master', 'status = ?,modified_date = ?,modified_by = ?', 'language_id = ?', array(1,CURRENT_DT,$loginSessionId,$_POST['enable']));
        if($update){
            $_SESSION['success'] = 'Language has been enabled successfully.';
            echo json_encode(array('status' => true));
        }else{
            $_SESSION['error'] = 'Language could not enabled.';
            echo json_encode(array('status' => false));
        }
    } else if(isset($_POST['disable'])){
        $update = $dbobj->UpdatetData($dbh, TBL_PRIFIX.'language_master', 'status = ?,modified_date = ?,modified_by = ?', 'language_id = ?', array(0,CURRENT_DT,$loginSessionId,$_POST['disable']));
         if($update){
             $_SESSION['success'] = 'Language has been disabled successfully.';
             echo json_encode(array('status' => true));
         }else{
             $_SESSION['error'] = 'Language could not disabled.';
             echo json_encode(array('status' => false));
         }
     }

?>
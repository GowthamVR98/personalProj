<?php
    include('../includes/admin-config.php');
    $dbobj = new GetCommomOperation();
    $loginSessionId = $dbobj->getSessionId();
    if(isset($_POST['delete'])){
       $result = $dbobj->DeleteData($dbh, TBL_PRIFIX.'industry_master', 'industry_id = ?', array($_POST['delete']));
        if($result){
            $_SESSION['success'] = 'Industry has been deleted successfully.';
            echo json_encode(array('status' => true));
        }else{
            $_SESSION['error'] = 'Industry could not deleted.';
            echo json_encode(array('status' => false));
        }
    }else if(isset($_POST['enable'])){
        $update = $dbobj->UpdatetData($dbh, TBL_PRIFIX.'industry_master', 'status = ?,modified_date = ?,modified_by = ?', 'industry_id = ?', array(1,CURRENT_DT,$loginSessionId,$_POST['enable']));
        if($update){
            $_SESSION['success'] = 'Industry has been enabled successfully.';
            echo json_encode(array('status' => true));
        }else{
            $_SESSION['error'] = 'Industry could not enabled.';
            echo json_encode(array('status' => false));
        }
    } else if(isset($_POST['disable'])){
        $update = $dbobj->UpdatetData($dbh, TBL_PRIFIX.'industry_master', 'status = ?,modified_date = ?,modified_by = ?', 'industry_id = ?', array(0,CURRENT_DT,$loginSessionId,$_POST['disable']));
         if($update){
             $_SESSION['success'] = 'Industry has been disabled successfully.';
             echo json_encode(array('status' => true));
         }else{
             $_SESSION['error'] = 'Industry could not disabled.';
             echo json_encode(array('status' => false));
         }
     }

?>
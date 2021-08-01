<?php
    include('../includes/admin-config.php');
    $dbobj = new GetCommomOperation();
    $loginSessionId = $dbobj->getSessionId();
    if(isset($_POST['loadTheme'])){
        $getTheame = $dbobj->selectData($dbh, TBL_PRIFIX.'dynamic_theme', array('theme_name'), 'added_by = ?', array($loginSessionId));
        if($getTheame['total'] > 0){
            $resP = $getTheame['values'][0]['theme_name'];
        }else{
            $resP = 'red';
        }
        echo json_encode(array('theme' => $resP));
    }
    if(isset($_POST['insertTheme'])){
        $getTheame = $dbobj->selectData($dbh, TBL_PRIFIX.'dynamic_theme', array('theme_name'), 'added_by = ?', array($loginSessionId));
        if($getTheame['total'] > 0){
            $update = $dbobj->UpdatetData($dbh, TBL_PRIFIX.'dynamic_theme', 'theme_name = ?,modified_date = ?', 'added_by = ?', array(trim($_POST['insertTheme']),CURRENT_DT,$loginSessionId));
            if($update){
                $resP =  true;
            }else{
                $resP =  false;
            }
        }else{
            $values = array(trim($_POST['insertTheme']),CURRENT_DT,$loginSessionId);
            $result = $dbobj->InsertQuery($dbh, TBL_PRIFIX.'dynamic_theme', array('theme_name','added_date','added_by'), $values);
            if($result['status']){
                $resP = $result['status'];
            }else{
                $resP =  $result['status'];
            }
        }
        echo json_encode(array('ChangeTheme' => $resP,'color' => $_POST['insertTheme']));
    }
?>
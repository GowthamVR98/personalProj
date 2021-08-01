<?php
    include('../includes/admin-config.php');
    $dbobj = new GetCommomOperation();
    $loginSessionId = $dbobj->getSessionId();
    if(isset($_POST['delete'])){
       $result = $dbobj->DeleteData($dbh, TBL_PRIFIX.'subject_master', 'subject_id = ?', array($_POST['delete']));
        if($result){
            $_SESSION['success'] = 'Subject has been deleted successfully.';
            echo json_encode(array('status' => true));
        }else{
            $_SESSION['error'] = 'Subject could not deleted.';
            echo json_encode(array('status' => false));
        }
    }else if(isset($_POST['enable'])){
        $update = $dbobj->UpdatetData($dbh, TBL_PRIFIX.'subject_master', 'status = ?,modified_date = ?,modified_by = ?', 'subject_id = ?', array(1,CURRENT_DT,$loginSessionId,$_POST['enable']));
        if($update){
            $_SESSION['success'] = 'Subject has been enabled successfully.';
            echo json_encode(array('status' => true));
        }else{
            $_SESSION['error'] = 'Subject could not enabled.';
            echo json_encode(array('status' => false));
        }
    } else if(isset($_POST['disable'])){
        $update = $dbobj->UpdatetData($dbh, TBL_PRIFIX.'subject_master', 'status = ?,modified_date = ?,modified_by = ?', 'subject_id = ?', array(0,CURRENT_DT,$loginSessionId,$_POST['disable']));
         if($update){
             $_SESSION['success'] = 'Subject has been disabled successfully.';
             echo json_encode(array('status' => true));
         }else{
             $_SESSION['error'] = 'Subject could not disabled.';
             echo json_encode(array('status' => false));
         }
    }else if(isset($_POST['courseId'])){
        $getSubjectData = $dbobj->selectData($dbh, TBL_PRIFIX.'course_master', array('course_type','year_sem_count'), 'course_id = ?', array($_POST['courseId']));
        $responseData = '';
        if($getSubjectData['total'] > 0){
            $getSubjectData = $getSubjectData['values'][0];
            if($getSubjectData['course_type'] == 'years'){
                $responseData .= '<select class="form-control" name="sltYearSem" title="Select Year" required>
                                    <optgroup label="Select Year">';
                                    for($i = 1; $i <= $getSubjectData['year_sem_count']; $i++){
                                        if(isset($_POST['semId'])){
                                            $responseData .= '<option '.($_POST['semId'] == $i.'_year' ? 'selected': '').' value="'.$i.'_year">'.$i.' Year</option>';
                                        }else{
                                            $responseData .= '<option value="'.$i.'_year">'.$i.' Year</option>';
                                        }
                                    }
                $responseData .= '</optgroup>
                                </select>';
            }else if($getSubjectData['course_type'] == 'semister'){
                $responseData .= '<select class="form-control" name="sltYearSem" title="Select Semister" required>
                                    <optgroup label="Select Semister">';
                                    for($i = 1; $i <= $getSubjectData['year_sem_count']; $i++){
                                        if(isset($_POST['semId'])){
                                            $responseData .= '<option '.($_POST['semId'] == $i.'_semister' ? 'selected': '').' value="'.$i.'_semister">'.$i.' Semister</option>';
                                        }else{
                                            $responseData .= '<option value="'.$i.'_semister">'.$i.' Semister</option>';
                                        }
                                    }
                $responseData .= '</optgroup>
                                </select>';
            }
            echo $responseData;
        } 
    }
?>
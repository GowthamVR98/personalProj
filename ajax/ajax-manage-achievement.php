<?php
    include('../includes/admin-config.php');
    $dbobj = new GetCommomOperation();
    $loginSessionId = $dbobj->getSessionId();
    if(isset($_POST['delete'])){
       $result = $dbobj->DeleteData($dbh, TBL_PRIFIX.'achievement_details', 'achievement_id = ?', array($_POST['delete']));
        if($result){
            $_SESSION['success'] = 'Achievement has been deleted successfully.';
            echo json_encode(array('status' => true));
        }else{
            $_SESSION['error'] = 'Achievement could not deleted.';
            echo json_encode(array('status' => false));
        }
    }else if(isset($_POST['enable'])){
        $update = $dbobj->UpdatetData($dbh, TBL_PRIFIX.'achievement_details', 'status = ?,modified_date = ?,modified_by = ?', 'achievement_id = ?', array(1,CURRENT_DT,$loginSessionId,$_POST['enable']));
        if($update){
            $_SESSION['success'] = 'Achievement has been enabled successfully.';
            echo json_encode(array('status' => true));
        }else{
            $_SESSION['error'] = 'Achievement could not enabled.';
            echo json_encode(array('status' => false));
        }
    } else if(isset($_POST['disable'])){
        $update = $dbobj->UpdatetData($dbh, TBL_PRIFIX.'achievement_details', 'status = ?,modified_date = ?,modified_by = ?', 'achievement_id = ?', array(0,CURRENT_DT,$loginSessionId,$_POST['disable']));
         if($update){
             $_SESSION['success'] = 'Achievement has been disabled successfully.';
             echo json_encode(array('status' => true));
         }else{
             $_SESSION['error'] = 'Achievement could not disabled.';
             echo json_encode(array('status' => false));
         }
     }else if(isset($_POST['pageno'])){
        $pageno = $_POST['pageno'];
        $no_of_records_per_page = 9;
        $offset = ($pageno-1) * $no_of_records_per_page;
        $exists = $dbobj->selectData($dbh, TBL_PRIFIX.'achievement_details', array('achievement_id'), 'achievement_id != ?', array(0));
        $total_pages = ceil($exists['total'] / $no_of_records_per_page);

        $userData = $dbobj->getUserData($dbh,$loginSessionId);
        $sql = "SELECT T2.title,T2.attachement_file,T2.description FROM ".TBL_PRIFIX."achievement_details as T2
            WHERE  T2.status = ?
            GROUP BY T2.achievement_id ASC  LIMIT $offset, $no_of_records_per_page";
        $stmt = $dbh->prepare($sql);
        $stmt->execute(array(1));
        $rowCount = $stmt->rowCount();
        $dataSet = '';
        if ($rowCount > 0) {
            for ($i = 1; $i <= $rowCount; $i++) {
                $result['values'][] = $stmt->fetch(PDO::FETCH_ASSOC);
            }
            foreach($result['values'] as $row){
                $dataSet .= '
                        <div class="media">
                            <div class="media-left">
                                <a href="'.ALUM_WS_UPLOADS.ALUM_ACHIEVEMENT.$row['attachement_file'].'" target="_blank" title="View">
                                    <img class="media-object" src="'.ALUM_WS_UPLOADS.ALUM_ACHIEVEMENT.$row['attachement_file'].'" width="150" height="90">
                                </a>
                            </div>
                            <div class="media-body">
                                <h4 class="media-heading">'.$row['title'].'</h4>
                                <p>
                                    '.$row['description'].'
                                </p>
                            </div>
                        </div>';
            }
            if($exists['total'] > $no_of_records_per_page){
                $dataSet .='<div class="clearfix"></div>
                            <nav>
                               <ul class="pagination pull-right">
                                    <li class="'.($pageno == 1 ? 'disabled' : '').'">
                                        <a href="javascript:void(0);" class="'.($pageno == 1 ? '' : 'previous-link').' waves-effect" id="'.$pageno.'">
                                            <i class="material-icons">chevron_left</i>
                                        </a>
                                    </li>';
                                for($i=1; $i<= $total_pages; $i++){
                                    if($pageno ==$i){
                                        $dataSet .='<li class="active"><a href="javascript:void(0);" class="page-link waves-effect" id="'.$i.'">'.$i.'</a></li>';
                                    }else{
                                        $dataSet .='<li><a href="javascript:void(0);" class="page-link waves-effect" id="'.$i.'">'.$i.'</a></li>';
                                    }
                                }
                                     
                       $dataSet .='<li class="'.($pageno == $total_pages ?  'disabled' : '').'">
                                        <a href="javascript:void(0);" class="'.($pageno == $total_pages ?  '' : 'next-link').' waves-effect" id="'.$pageno.'">
                                            <i class="material-icons">chevron_right</i>
                                        </a>
                                    </li>
                                </ul>
                            </nav>';
            }
            echo json_encode(array('htmlContent' => $dataSet,'total' => $total_pages));
        }else{
            echo json_encode(array('htmlContent' => 'No record found'));
        }
     }

?>
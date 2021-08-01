<?php
    include('../includes/admin-config.php');
    $dbobj = new GetCommomOperation();
    $loginSessionId = $dbobj->getSessionId();
    if(isset($_POST['delete'])){
       $result = $dbobj->DeleteData($dbh, TBL_PRIFIX.'reunion_details', 'reunion_id = ?', array($_POST['delete']));
        if($result){
            $_SESSION['success'] = 'Reunion has been deleted successfully.';
            echo json_encode(array('status' => true));
        }else{
            $_SESSION['error'] = 'Reunion could not deleted.';
            echo json_encode(array('status' => false));
        }
    }else if(isset($_POST['enable'])){
        $update = $dbobj->UpdatetData($dbh, TBL_PRIFIX.'reunion_details', 'status = ?,modified_date = ?,modified_by = ?', 'reunion_id = ?', array(1,CURRENT_DT,$loginSessionId,$_POST['enable']));
        if($update){
            $_SESSION['success'] = 'Reunion has been enabled successfully.';
            echo json_encode(array('status' => true));
        }else{
            $_SESSION['error'] = 'Reunion could not enabled.';
            echo json_encode(array('status' => false));
        }
    } else if(isset($_POST['disable'])){
        $update = $dbobj->UpdatetData($dbh, TBL_PRIFIX.'reunion_details', 'status = ?,modified_date = ?,modified_by = ?', 'reunion_id = ?', array(0,CURRENT_DT,$loginSessionId,$_POST['disable']));
         if($update){
             $_SESSION['success'] = 'Reunion has been disabled successfully.';
             echo json_encode(array('status' => true));
         }else{
             $_SESSION['error'] = 'Reunion could not disabled.';
             echo json_encode(array('status' => false));
         }
     }else if(isset($_POST['approveProcess'])){
        if(isset($_POST['approve'])){
            $id = $_POST['approve'];
            $approveStatus = 'approved';
            $msg = 'Reunion has been approved successfully.';
        }else{
            $msg = 'Reunion has been rejected successfully.';
            $id = $_POST['reject'];
            $approveStatus = 'rejected';
        }
        $values = array($approveStatus,CURRENT_DT,$loginSessionId,$_SERVER['REMOTE_ADDR'],trim($id));
        $result = $dbobj->UpdatetData($dbh, TBL_PRIFIX.'reunion_details','approval_status = ?,modified_date = ?,modified_by = ?,modified_ip = ?','reunion_id = ?', $values,true);
        if($result){
            $_SESSION['success'] =  $msg;
            exit(0);
        }else{
            $_SESSION['error'] = 'Something went to wrong.';
            exit(0);
        }
    }else if(isset($_POST['reject'])){
        $getData = $dbobj->selectData($dbh, TBL_PRIFIX.'reunion_details', array('reunion_id'), 'reunion_id = ?', array($_POST['reject']));
        if($getData['total'] > 0){
            $data = '';
            $data .='<form id="form_validation" method="POST" enctype="multipart/form-data">
                        <div class="body">
                            <!-- Inline Layout -->
                            <div class="form-group form-float">
                                <div class="form-line">
                                    <textarea class="form-control" placeholder="Reject Reason" name="txtRejectReason"></textarea>
                                    <input type="hidden" name="hiddenId" value="'.$getData['values'][0]['reunion_id'].'">
                                </div>
                            </div>
                            <div class="clearfix"></div>
                            <!-- #END# Inline Layout -->
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-default waves-effect" data-dismiss="modal">CLOSE</button>
                            <button type="submit" name="btnReject" class="btn btn-danger waves-effect" title="Reject">Reject</button>
                        </div>
                    </form>';
        }
        echo $data;
    }else if(isset($_POST['ReasonId'])){
         $getData = $dbobj->selectData($dbh, TBL_PRIFIX.'reunion_details', array('reject_reason'), 'reunion_id = ?', array($_POST['ReasonId']));
        if($getData['total'] > 0){
            $data = '';
            $data .='<form id="form_validation" method="POST" enctype="multipart/form-data">
                        <div class="body">
                            <!-- Inline Layout -->
                            <div class="form-group form-float">
                                <div class="form-line">
                                    <textarea class="form-control" placeholder="Album Name" name="txtAlbumName" readonly>'.$getData['values'][0]['reject_reason'].'</textarea>
                                </div>
                            </div>
                            <div class="clearfix"></div>
                            <!-- #END# Inline Layout -->
                        </div>
                    </form>';
        }
        echo $data;
    }else if(isset($_POST['pageno'])){
        $pageno = $_POST['pageno'];
        $no_of_records_per_page = 9;
        $offset = ($pageno-1) * $no_of_records_per_page;
        $exists = $dbobj->selectData($dbh, TBL_PRIFIX.'reunion_details', array('reunion_id'), 'status = ? AND approval_status = ?', array(1,'approved'));
        $total_pages = ceil($exists['total'] / $no_of_records_per_page);

        $userData = $dbobj->getUserData($dbh,$loginSessionId);
        $sql = "SELECT T2.title,T2.reunion_logo,T2.description,T2.location,T2.start_date,T2.start_time,T2.end_date,T2.end_time FROM ".TBL_PRIFIX."reunion_details as T2
            WHERE  T2.status = ? and T2.approval_status = ? 
            GROUP BY T2.reunion_id ASC  LIMIT $offset, $no_of_records_per_page";
        $stmt = $dbh->prepare($sql);
        $stmt->execute(array(1,'approved'));
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
                                <a href="'.ALUM_WS_UPLOADS.ALUM_RUNION.$row['reunion_logo'].'" target="_blank" title="View">
                                    <img class="media-object" src="'.ALUM_WS_UPLOADS.ALUM_RUNION.$row['reunion_logo'].'" width="200" height="200">
                                </a>
                            </div>
                            <div class="media-body">
                                <h4 class="media-heading">'.$row['title'].'</h4>
                                <small><b>Start:</b> '.AppDate::datetime($row['start_date'].' '.$row['start_time']).'</small><br>
                                <small><b>End:</b> '.AppDate::datetime($row['end_date'].' '.$row['end_time']).'</small><br>
                                <small><b>Location:</b>: '.$row['location'].'</small>
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
<?php
    include('../includes/admin-config.php');
    include '../includes/emp-crypto-graphy.php';
    $SafeCrypto = new SafeCrypto();
    $dbobj = new GetCommomOperation();
    $loginSessionId = $dbobj->getSessionId();
    if(isset($_POST['approveProcess'])){
        if(isset($_POST['approve'])){
            $id = $_POST['approve'];
            $approveStatus = 'approved';
            $msg = 'Video has been approved successfully.';
        }else{
            $msg = 'Video has been rejected successfully.';
            $id = $_POST['reject'];
            $approveStatus = 'rejected';
        }
        $values = array($approveStatus,CURRENT_DT,$loginSessionId,$_SERVER['REMOTE_ADDR'],trim($id));
        $result = $dbobj->UpdatetData($dbh, TBL_PRIFIX.'video_details','approval_status = ?,modified_date = ?,modified_by = ?,modified_ip = ?','video_id = ?', $values,true);
        if($result){
            $_SESSION['success'] =  $msg;
            exit(0);
        }else{
            $_SESSION['error'] = 'Something went to wrong.';
            exit(0);
        }
    }else if(isset($_POST['reject'])){
        $getData = $dbobj->selectData($dbh, TBL_PRIFIX.'video_details', array('video_id'), 'video_id = ?', array($_POST['reject']));
        if($getData['total'] > 0){
            $data = '';
            $data .='<form id="form_validation" method="POST" enctype="multipart/form-data">
                        <div class="body">
                            <!-- Inline Layout -->
                            <div class="form-group form-float">
                                <div class="form-line">
                                    <textarea class="form-control" placeholder="Reject Reason" name="txtRejectReason"></textarea>
                                    <input type="hidden" name="hiddenId" value="'.$getData['values'][0]['video_id'].'">
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
    }else if(isset($_POST['pageno'])){
        $pageno = $_POST['pageno'];
        $no_of_records_per_page = 9;
        $offset = ($pageno-1) * $no_of_records_per_page;
        $exists = $dbobj->selectData($dbh, TBL_PRIFIX.'video_details', array('video_id'), 'approval_status = ?', array('pending'));
        $total_pages = ceil($exists['total'] / $no_of_records_per_page);

        $userData = $dbobj->getUserData($dbh,$loginSessionId);
        $sql = "SELECT T2.video_id,T2.title,T2.youtub_link,T2.added_by,T2.status FROM ".TBL_PRIFIX."video_details as T2 WHERE approval_status = ? GROUP BY T2.added_date DESC  LIMIT $offset, $no_of_records_per_page";
        $stmt = $dbh->prepare($sql);
        $stmt->execute(array('pending'));
        $rowCount = $stmt->rowCount();
        $dataSet = '';
        if ($rowCount > 0) {
            for ($i = 1; $i <= $rowCount; $i++) {
                $result['values'][] = $stmt->fetch(PDO::FETCH_ASSOC);
            }
            foreach($result['values'] as $row){
                $dataSet .= '<div class="col-lg-5 col-md-6 col-sm-12 col-xs-12">
                        <div class="card">
                            <div class="header">
                                <h2>'.$row['title'].'</h2>
                                <ul class="header-dropdown m-r--5">
                                    <li class="dropdown">
                                        <a href="javascript:void(0);" class="ManageAction" id="approve_'.$row['video_id'].'"><i class="material-icons" title = "Approve" style="font-size:21px;color:green;font-weight:900;">check</i></a>
                                        <a href="javascript:void(0);" class="RejectedReason" id="reject_'.$row['video_id'].'"><i class="material-icons" data-toggle="modal" data-target="#RemarkModal" style="font-size:21px;color:red;font-weight:900;" title="Reject">close</i></a>
                                    </li>
                                </ul>
                            </div>
                            <div class="body">
                                <div id="carousel-example-generic" class="carousel slide" data-ride="carousel">
                                    <!-- Wrapper for slides -->
                                    <div class="carousel-inner" role="listbox">
                                        <div class="item active">
                                            <iframe src="'.str_replace(array('https://www.youtube.com/watch?v=','https://youtu.be/'), array('http://www.youtube.com/embed/','http://www.youtube.com/embed/'), $row['youtub_link']).'" allowfullscreen="" width="350" height="300" frameborder="0"></iframe>
                                        </div>
                                    </div>
                                </div>
                            </div>
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
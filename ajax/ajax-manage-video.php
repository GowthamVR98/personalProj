<?php
    include('../includes/admin-config.php');
    include '../includes/emp-crypto-graphy.php';
    $SafeCrypto = new SafeCrypto();
    $dbobj = new GetCommomOperation();
    $loginSessionId = $dbobj->getSessionId();
    if(isset($_POST['delete'])){
       $result = $dbobj->DeleteData($dbh, TBL_PRIFIX.'video_details', 'video_id = ?', array($_POST['delete']));
        if($result){
            $_SESSION['success'] = 'Video has been deleted successfully.';
            echo json_encode(array('status' => true));
        }else{
            $_SESSION['error'] = 'Video could not deleted.';
            echo json_encode(array('status' => false));
        }
    }else if(isset($_POST['enable'])){
        $update = $dbobj->UpdatetData($dbh, TBL_PRIFIX.'video_details', 'status = ?,modified_date = ?,modified_by = ?', 'video_id = ?', array(1,CURRENT_DT,$loginSessionId,$_POST['enable']));
        if($update){
            $_SESSION['success'] = 'Video has been enabled successfully.';
            echo json_encode(array('status' => true));
        }else{
            $_SESSION['error'] = 'Video could not enabled.';
            echo json_encode(array('status' => false));
        }
    } else if(isset($_POST['disable'])){
        $update = $dbobj->UpdatetData($dbh, TBL_PRIFIX.'video_details', 'status = ?,modified_date = ?,modified_by = ?', 'video_id = ?', array(0,CURRENT_DT,$loginSessionId,$_POST['disable']));
         if($update){
             $_SESSION['success'] = 'Video has been disabled successfully.';
             echo json_encode(array('status' => true));
         }else{
             $_SESSION['error'] = 'Video could not disabled.';
             echo json_encode(array('status' => false));
         }
     }else if(isset($_POST['ReasonId'])){
         $getData = $dbobj->selectData($dbh, TBL_PRIFIX.'video_details', array('reject_reason'), 'video_id = ?', array($_POST['ReasonId']));
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
        $exists = $dbobj->selectData($dbh, TBL_PRIFIX.'video_details', array('video_id'), 'approval_status = ?', array('approved'));
        $total_pages = ceil($exists['total'] / $no_of_records_per_page);

        $userData = $dbobj->getUserData($dbh,$loginSessionId);
        $sql = "SELECT T2.video_id,T2.title,T2.youtub_link,T2.added_by,T2.status FROM ".TBL_PRIFIX."video_details as T2 WHERE approval_status = ? GROUP BY T2.added_date DESC  LIMIT $offset, $no_of_records_per_page";
        $stmt = $dbh->prepare($sql);
        $stmt->execute(array('approved'));
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
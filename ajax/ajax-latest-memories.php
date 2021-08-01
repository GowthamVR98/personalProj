<?php
    include('../includes/admin-config.php');
    $dbobj = new GetCommomOperation();
    include '../includes/emp-crypto-graphy.php';
    $SafeCrypto = new SafeCrypto();
    $loginSessionId = $dbobj->getSessionId();
if(isset($_POST['pageno'])){
        $pageno = $_POST['pageno'];
        $no_of_records_per_page = 9;
        $offset = ($pageno-1) * $no_of_records_per_page;
        $exists = $dbobj->selectData($dbh, TBL_PRIFIX.'album_details', array('album_id'), 'approval_status = ?', array("approved"));
        $total_pages = ceil($exists['total'] / $no_of_records_per_page);

        $userData = $dbobj->getUserData($dbh,$loginSessionId);
        $sql = "SELECT T2.album_id,T2.album_name,T1.gallery_file,T2.added_by,T2.album_status FROM ".TBL_PRIFIX."album_details as T2
            LEFT JOIN ".TBL_PRIFIX."gallery_details as T1 ON T1.album_id = T2.album_id WHERE  T2.approval_status = ?
            GROUP BY T2.album_id ASC  LIMIT $offset, $no_of_records_per_page";
        $stmt = $dbh->prepare($sql);
        $stmt->execute(array('approved'));
        $rowCount = $stmt->rowCount();
        $dataSet = '';
        if ($rowCount > 0) {
            for ($i = 1; $i <= $rowCount; $i++) {
                $result['values'][] = $stmt->fetch(PDO::FETCH_ASSOC);
            }
            foreach($result['values'] as $row){
                $dataSet .= '<div class="col-lg-4 col-md-6 col-sm-12 col-xs-12">
                        <div class="card">
                            <div class="header">
                                <h2>'.$row['album_name'].'</h2>
                            </div>
                            <div class="body">
                                <div id="carousel-example-generic" class="carousel slide" data-ride="carousel">
                                    <!-- Wrapper for slides -->
                                    <div class="carousel-inner" role="listbox">
                                        <div class="item active">
                                            <img src="'.ALUM_WS_UPLOADS.ALUM_ALBUM.$row['gallery_file'].'" />
                                        </div>
                                        <div class="item">
                                            <img src="'.ALUM_WS_UPLOADS.ALUM_ALBUM.$row['gallery_file'].'" />
                                        </div>
                                         <a class=" carousel-control " style="top:70px; width:100%;" href="alumni-view-gallery?albumId='. $SafeCrypto->encrypt($row['album_id']).'">
                                            <span class="">View</span>
                                        </a>
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
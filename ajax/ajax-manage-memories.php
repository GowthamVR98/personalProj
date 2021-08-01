<?php
	include('../includes/admin-config.php');
    $dbobj = new GetCommomOperation();
	include '../includes/emp-crypto-graphy.php';
	$SafeCrypto = new SafeCrypto();
    $loginSessionId = $dbobj->getSessionId();
	if(isset($_POST['editAlbum'])){
		 $getData = $dbobj->selectData($dbh, TBL_PRIFIX.'album_details', array('album_id,album_name'), 'album_id = ?', array($_POST['editAlbum']));
		if($getData['total'] > 0){
			$data = '';
			$data .='<form id="form_validation" method="POST" enctype="multipart/form-data">
	                    <div class="body">
	                        <!-- Inline Layout -->
	                        <div class="form-group form-float">
	                            <div class="form-line">
	                                <input type="text" class="form-control" placeholder="Album Name" value="'.$getData['values'][0]['album_name'].'" name="txtAlbumName" required maxlength="60">
	                                <input type="hidden" class="form-control" value="'.$getData['values'][0]['album_id'].'"  name="txtAlbumId"/>
	                            </div>
	                        </div>
	                        <div class="clearfix"></div>
	                        <!-- #END# Inline Layout -->
	                    </div>
	                    <div class="modal-footer">
	                        <button type="button" class="btn btn-default waves-effect" data-dismiss="modal">CLOSE</button>
	                        <button type="submit" name="btnEditAlbum" class="btn btn-danger waves-effect">Add</button>
	                    </div>
	                </form>';
		}
		echo $data;
	}else if(isset($_POST['ReasonId'])){
         $getData = $dbobj->selectData($dbh, TBL_PRIFIX.'album_details', array('reject_reason'), 'album_id = ?', array($_POST['ReasonId']));
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
    }else if(isset($_POST['delete'])){
       $result = $dbobj->DeleteData($dbh, TBL_PRIFIX.'album_details', 'album_id = ?', array($_POST['delete']));
        if($result){
            $_SESSION['success'] = 'Album has been deleted successfully.';
            echo json_encode(array('album_status' => true));
        }else{
            $_SESSION['error'] = 'Album could not deleted.';
            echo json_encode(array('album_status' => false));
        }
    }else if(isset($_POST['enable'])){
        $update = $dbobj->UpdatetData($dbh, TBL_PRIFIX.'album_details', 'album_status = ?,modified_date = ?,modified_by = ?', 'album_id = ?', array(1,CURRENT_DT,$loginSessionId,$_POST['enable']));
        if($update){
            $_SESSION['success'] = 'Album has been enabled successfully.';
            echo json_encode(array('album_status' => true));
        }else{
            $_SESSION['error'] = 'Album could not enabled.';
            echo json_encode(array('album_status' => false));
        }
    } else if(isset($_POST['disable'])){
        $update = $dbobj->UpdatetData($dbh, TBL_PRIFIX.'album_details', 'album_status = ?,modified_date = ?,modified_by = ?', 'album_id = ?', array(0,CURRENT_DT,$loginSessionId,$_POST['disable']));
         if($update){
             $_SESSION['success'] = 'Album has been disabled successfully.';
             echo json_encode(array('album_status' => true));
         }else{
             $_SESSION['error'] = 'Album could not disabled.';
             echo json_encode(array('album_status' => false));
         }
     }else if(isset($_POST['GalleryDelete'])){
         $result = $dbobj->DeleteData($dbh, TBL_PRIFIX.'gallery_details', 'gallery_id = ?', array($_POST['GalleryDelete']));
        if($result){
            $_SESSION['success'] = 'Image has been deleted successfully.';
            echo json_encode(array('album_status' => true));
        }else{
            $_SESSION['error'] = 'Image could not deleted.';
            echo json_encode(array('album_status' => false));
        }
     }else if(isset($_POST['pageno'])){
        $pageno = $_POST['pageno'];
     	$no_of_records_per_page = 9;
        $offset = ($pageno-1) * $no_of_records_per_page;
        $exists = $dbobj->selectData($dbh, TBL_PRIFIX.'album_details', array('album_id'), 'added_by = ?', array($loginSessionId));
        $total_pages = ceil($exists['total'] / $no_of_records_per_page);

        $userData = $dbobj->getUserData($dbh,$loginSessionId);
        $sql = "SELECT T2.album_id,T2.album_name,T1.gallery_file,T2.added_by,T2.album_status,T2.approval_status FROM ".TBL_PRIFIX."album_details as T2
            LEFT JOIN ".TBL_PRIFIX."gallery_details as T1 ON T1.album_id = T2.album_id  WHERE T2.added_by = ?
            GROUP BY T2.album_id ASC  LIMIT $offset, $no_of_records_per_page";
        $stmt = $dbh->prepare($sql);
        $stmt->execute(array($loginSessionId));
        $rowCount = $stmt->rowCount();
        $dataSet = '';
        if ($rowCount > 0) {
            for ($i = 1; $i <= $rowCount; $i++) {
                $result['values'][] = $stmt->fetch(PDO::FETCH_ASSOC);
            }
            foreach($result['values'] as $row){
                if($row['approval_status'] == 'pending'){
                    $approvalStatus = '<a href="javascript:void(0);"><span class="badge bg-orange" style="position:absolute; top:0;left:0;">Pending</span></a>';
                }else if($row['approval_status'] == 'approved'){
                    $approvalStatus = '<a href="javascript:void(0);"><span class="badge bg-teal" style="position:absolute; top:0;left:0;">Approved</span></a>';
                }else if($row['approval_status'] == 'rejected'){
                    $approvalStatus = '<a href="javascript:void(0);" class="RejectedReason" id="ReasonId_'.$row['album_id'].'"><span class="badge bg-pink" title="Click to view reason" style="position:absolute; top:0;left:0;" data-toggle="modal" data-target="#RemarkModal">Rejected</span></a>';
                }
                $dataSet .= '<div class="col-lg-4 col-md-6 col-sm-12 col-xs-12">
                        <div class="card">
                            <div class="header" style="position:relative;">
                                <h2>'.$row['album_name'].' '.$approvalStatus.'</h2>
                                '.($loginSessionId == $row['added_by'] || $userData['values'][0]['super_admin'] == 'yes'? 
                                    '<ul class="header-dropdown m-r--5">
                                        <li class="dropdown">
                                            <a href="javascript:void(0);" class="editAction" id="editAlbum_'.$row['album_id'].'" data-toggle="modal" data-target="#defaultModal"><i class="material-icons" title="Edit Title" id="21" style=" font-size:21px;">edit</i></a>
                                            '.($row['album_status'] == 1 ? '<a href="javascript:void(0);" class="ManageAction" id="disable_'.$row['album_id'].'"><i class="material-icons" title = "Disable" style="font-size:21px;">block</i></a>' : '<a href="javascript:void(0);" class="ManageAction" id="enable_'.$row['album_id'].'"><i class="material-icons" style="font-size:21px;" title="Enable">check_circle</i></a>').'
                                            <a href="javascript:void(0);" class="ManageAction" id="delete_'.$row['album_id'].'"><i class="material-icons" title="Delete" style="color:#e21313; font-size:21px;">delete</i></a>
                                        </li>
                                    </ul>' : '').'
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
                                         <a class=" carousel-control " style="top:70px; width:100%;" href="alumni-gallery?albumId='. $SafeCrypto->encrypt($row['album_id']).'">
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
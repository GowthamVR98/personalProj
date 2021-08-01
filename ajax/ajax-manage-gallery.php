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
     }else if(isset($_POST['pageno'])){
        $pageno = $_POST['pageno'];
        $no_of_records_per_page = 2;
     	$no_of_records_per_page = 2;
        $offset = ($pageno-1) * $no_of_records_per_page;
        $exists = $dbobj->selectData($dbh, TBL_PRIFIX.'gallery_details', array('album_id'), 'album_id = ?', array($_POST['albumId']));
        $total_pages = ceil($exists['total'] / $no_of_records_per_page);

        $userData = $dbobj->getUserData($dbh,$loginSessionId);
        $sql = "SELECT T2.album_id,T2.album_name,T1.gallery_name,T1.gallery_file,T2.added_by FROM ".TBL_PRIFIX."gallery_details as T1
                LEFT JOIN ".TBL_PRIFIX."album_details as T2 ON T1.album_id = T2.album_id WHERE T1.album_id = ?
                GROUP BY T1.gallery_id ASC LIMIT $offset, $no_of_records_per_page";
        $stmt = $dbh->prepare($sql);
        $stmt->execute(array($_POST['albumId']));
        $rowCount = $stmt->rowCount();
        $dataSet = '';
        if ($rowCount > 0) {
            for ($i = 1; $i <= $rowCount; $i++) {
                $result['values'][] = $stmt->fetch(PDO::FETCH_ASSOC);
            }
            $dataSet .= '<div class="header">
                    <h2>
                        '.$result['values'][0]['album_name'].'
                    </h2>
                     <ul class="header-dropdown m-r--5">
                        <li class="dropdown">
                            <button type="button" data-color="blue" class="btn bg-blue waves-effect addImage" data-toggle="modal" data-target="#defaultModal">Add Image</button>
                        </li>
                    </ul>
                </div>
                <div class="body">
                    <div id="aniimated-thumbnials" class="list-unstyled row clearfix">';
            foreach($result['values'] as $row){
                $dataSet .= '<div class="col-lg-3 col-md-4 col-sm-6 col-xs-12">
                                <a href="'.ALUM_WS_UPLOADS.ALUM_ALBUM.$row['gallery_file'].'" data-sub-html="'.$row['gallery_name'].'">
                                    <img class="img-responsive thumbnail" src="'.ALUM_WS_UPLOADS.ALUM_ALBUM.$row['gallery_file'].'">
                                </a>
                            </div> ';
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
        }
     }

?>
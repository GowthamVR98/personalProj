<?php
include 'includes/admin-config.php';
$dbobj = new GetCommomOperation();
	if (isset($_POST["btnImport"])) {
        $fileName = $_FILES["txtImportFile"]["tmp_name"];
        if ($_FILES["txtImportFile"]["size"] > 0) {
            $file = fopen($fileName, "r");
            $i = 0;
            $duplicate = array();
            while (($column = fgetcsv($file, 10000, ",")) !== FALSE) {
            	if($i > 0){
            		$exists = $dbobj->selectData($dbh, TBL_PRIFIX.'student_registration_no', array('reg_id'), 'register_number = ?', array($column[0]));
				    if($exists['total'] == 0){
			        	$value = array($column[0],$column[1],CURRENT_DT);
		                $insertRegistrationId = $dbobj->InsertQuery($dbh, TBL_PRIFIX.'student_registration_no', array('register_number','user_type','added_date'), $value);
				    }else{
				        $duplicate[] = array($column[0],$column[1]);
				    }
            	}
            $i ++;
            }
            if($insertRegistrationId['status']){
            	$_SESSION['success'] = 'Registration ids imported successfully.';
		        header('location:alumni-import-registrationid');
		        exit(0);
            }else{
		        $_SESSION['error'] = 'Registration ids could not added.';
		        header('location:alumni-import-registrationid');
		        exit(0);
            }
        }
    }
?>
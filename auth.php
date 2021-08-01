 <?php
	//Start session
	if (!session_id() ) {
		session_start();
    }
	//Check whether the session variable SESS_MEMBER_ID is present or not
	if(!isset($_SESSION['SESS_ADMIN_ID']) || (trim($_SESSION['SESS_ADMIN_ID']) == '')) {
		header("location: index");
		exit(0);
    }else{
		define('LOGIN_SESS_ID', $_SESSION['SESS_ADMIN_ID']);
	}
?>
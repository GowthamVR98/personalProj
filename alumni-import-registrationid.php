<?php
include 'includes/admin-config.php';
include_once 'auth.php';
$dbobj = new GetCommomOperation();
if (isset($_POST["btnImport"])) {
        $fileName = $_FILES["txtImportFile"]["tmp_name"];
        if ($_FILES["txtImportFile"]["size"] > 0) {
            $file = fopen($fileName, "r");
            $i = 0;
            $duplicate = array();
            $insertStatus = false;
            while (($column = fgetcsv($file, 10000, ",")) !== FALSE) {
                if($i > 0){
                    $exists = $dbobj->selectData($dbh, TBL_PRIFIX.'student_registration_no', array('reg_id'), 'register_number = ?', array($column[0]));
                    if($exists['total'] == 0){
                        $value = array($column[0],$column[1],CURRENT_DT);
                        $insertRegistrationId = $dbobj->InsertQuery($dbh, TBL_PRIFIX.'student_registration_no', array('register_number','user_type','added_date'), $value);
                        if($insertRegistrationId['status']){
                            $insertStatus = true;
                        }
                    }else{
                        $duplicate[] = array($column[0],$column[1]);
                    }
                }
            $i ++;
            }
            if($insertStatus){
                $_SESSION['success'] = 'Registration ids imported successfully.';
                header('location:alumni-import-registrationid');
                exit(0);
            }else{
                $_SESSION['error'] = 'Registration ids could not imported.';
                header('location:alumni-import-registrationid');
                exit(0);
            }
        }
    }
?>
<!DOCTYPE html>
<html>
<head>
    <?php include (ALUM_TEMPLATES.'metatag.php');?>
</head>

<body class="theme-red">
    <?php
        // <!-- Page Loader -->
        include ALUM_TEMPLATES.'loader.php';
        // <!-- #END# Page Loader -->
        // <!-- Top Bar -->
            include ALUM_TEMPLATES.'top-navigation.php';
        // <!-- #Top Bar -->
        // <!-- Left Sidebar -->
            include ALUM_TEMPLATES.'left-links.php';
        // <!-- #Left Sidebar -->
    ?>
    <section class="content">
        <div class="container-fluid">
            <!-- Calender -->
            <div class="row clearfix">
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                    <div class="card">
                        <form id="form_validation" method="POST" enctype="multipart/form-data">
                            <div class="header">
                                <h2>Import Users Registration Id</h2>
                                <ul class="header-dropdown m-r--5">
                                    <li class="dropdown">
                                        <button class="btn btn-primary waves-effect" name="btnImport" type="submit">SUBMIT</button>
                                    </li>
                                </ul>
                            </div>
                            <div class="body">
                                <!-- Inline Layout -->
                                <div class="form-group form-float">
                                        <label class="form-label">Upload File(csv)</label>
                                    <div class="form-line">
                                        <input type="file" class="form-control" name="txtImportFile" required id="file" accept=".csv">
                                    </div>
                                </div>
                                <div class="form-group form-float">
                                    <div class="demo-google-material-icon">
                                        <span class="icon-name">Download Sample File(csv) <a href="images/sample-files/sample-for-registrationId-import.csv" title="Download" download><i class="material-icons" style="vertical-align:middle;">file_download</i></a></span>
                                    </div>
                                </div>
                                <div class="clearfix"></div>
                                <!-- #END# Inline Layout -->
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <!-- #END# Calender -->
    </section>
    <?php include ALUM_TEMPLATES.'footer.php';?> 
    <script type="text/javascript">
        $(document).ready(
        function() {
            $("#frmCSVImport").on(
            "submit",
            function() {

                $("#response").attr("class", "");
                $("#response").html("");
                var fileType = ".csv";
                var regex = new RegExp("([a-zA-Z0-9\s_\\.\-:])+("
                        + fileType + ")$");
                if (!regex.test($("#file").val().toLowerCase())) {
                    $("#response").addClass("error");
                    $("#response").addClass("display-block");
                    $("#response").html(
                            "Invalid File. Upload : <b>" + fileType
                                    + "</b> Files.");
                    return false;
                }
                return true;
            });
        });
    </script>
</body>

</html>

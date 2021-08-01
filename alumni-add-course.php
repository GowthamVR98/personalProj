<?php
include 'includes/admin-config.php';
include_once 'auth.php';
include 'includes/emp-crypto-graphy.php';
$dbobj = new GetCommomOperation();
$SafeCrypto = new SafeCrypto();
if(isset($_POST['btnskill'])){
    $checkExists = $dbobj->check_exist($dbh, TBL_PRIFIX.'course_master',array('course_id'), 'course = ?', array(trim($_POST['txtSkill'])));
    if($checkExists){
        $_SESSION['error'] = 'course already exist.';
        header('location:alumni-add-course.php');
        exit(0);
    }else{
        $loginSessionId = $dbobj->getSessionId();
        $values = array(trim($_POST['txtCode']),trim($_POST['txtSkill']),trim($_POST['sltCourseType']),trim($_POST['txtCount']),CURRENT_DT,$loginSessionId,$_SERVER['REMOTE_ADDR']);
        $result = $dbobj->InsertQuery($dbh, TBL_PRIFIX.'course_master', array('course_code','course','course_type','year_sem_count','added_date','added_by','added_ip'), $values);
        if($result['status']){
            $_SESSION['success'] = 'New course has been added successfully.';
            header('location:alumni-add-course.php');
            exit(0);
        }else{
            $_SESSION['error'] = 'Something went to wrong.';
            header('location:alumni-add-course.php');
            exit(0);
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <?php include (ALUM_TEMPLATES.'metatag.php');?>
    <link href="plugins/bootstrap-select/css/bootstrap-select.css" rel="stylesheet" />
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
            <div class="block-header">
                <h2>
                    <button class="btn btn-primary waves-effect pull-right" id="ShowHide" type="submit">Add Course</button>
                    <button class="btn btn-primary waves-effect pull-right" id="hideOptionBlock" type="button" style="display:none;">Hide</button>
                </h2>
                <div class="clearfix"></div>
            </div>
            <!-- Calender -->
            <div class="row clearfix"  id="hideShow">
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                    <div class="card">
                        <form id="form_validation" method="POST">
                            <div class="header">
                                <h2>Add Course</h2>
                                <ul class="header-dropdown m-r--5">
                                    <li class="dropdown">
                                        <button class="btn btn-primary waves-effect" name="btnskill" type="submit">SUBMIT</button>
                                    </li>
                                </ul>
                            </div>
                            <div class="body">
                                <!-- Inline Layout -->
                                <div class="col-sm-4">
                                    <div class="form-group form-float clearfix">
                                        <div class="form-line">
                                            <input type="text" class="form-control" name="txtCode" required maxlength="60">
                                            <label class="form-label">Enter Course Code</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="form-group form-float clearfix">
                                        <div class="form-line">
                                            <input type="text" class="form-control" name="txtSkill" required maxlength="60">
                                            <label class="form-label">Enter Course Name</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="form-group form-float clearfix">
                                        <div class="form-line">
                                            <select class="form-control" name="sltCourseType" title="Select Course Type" required>
                                                <optgroup label="Select Course Type">
                                                <option value="years">Years</option>
                                                <option value="semister">Semister</option>
                                                
                                                </optgroup>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="form-group form-float clearfix">
                                        <div class="form-line">
                                            <input type="text" class="form-control" name="txtCount" required maxlength="60">
                                            <label class="form-label"> Enter No. Of Years/Semister </label>
                                        </div>
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
             <!-- Basic Examples -->
             <div class="row clearfix">
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                    <div class="card">
                        <div class="header">
                            <h2>
                                Manage Course
                            </h2>
                            <ul class="header-dropdown m-r--5">
                                <li class="dropdown">
                                    <a href="javascript:void(0);" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">
                                        <i class="material-icons">more_vert</i>
                                    </a>
                                    <ul class="dropdown-menu pull-right">
                                        <li><a href="javascript:void(0);">Action</a></li>
                                        <li><a href="javascript:void(0);">Another action</a></li>
                                        <li><a href="javascript:void(0);">Something else here</a></li>
                                    </ul>
                                </li>
                            </ul>
                        </div>
                        <div class="body">
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped table-hover js-basic-example dataTable">
                                    <thead>
                                        <tr>
                                            <th>Course Code</th>
                                            <th>Course</th>
                                            <th>Course Type</th>
                                            <th>No. Of Years/Semisters</th>
                                            <th>Date Added</th>
                                            <th>Added By</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php 
                                        $sql = "SELECT concat_ws(' ',T1.fname,T1.lname) as Name, T2.year_sem_count,T2.course_id,T2.course_type,T2.course_code ,T2.course,T2.status,T2.added_date,T2.added_by FROM ".TBL_PRIFIX."course_master as T2 LEFT JOIN  ".TBL_PRIFIX."user_login_details as T1 ON T1.user_id = T2.added_by ORDER BY T2.course_id ASC";
                                        $stmt = $dbh->prepare($sql);
                                        $stmt->execute();
                                        $rowCount = $stmt->rowCount();
                                        if ($rowCount > 0) {
                                                for ($i = 1; $i <= $rowCount; $i++) {
                                                    $result['values'][] = $stmt->fetch(PDO::FETCH_ASSOC);
                                                }
                                                foreach($result['values'] as $row){
                                                    echo '<tr>';
                                                    echo '<td>'.$row['course_code'].'</td>';
                                                    echo '<td>'.$row['course'].'</td>';
                                                    echo '<td>'.$row['course_type'].'</td>';
                                                    echo '<td>'.$row['year_sem_count'].'</td>';
                                                    echo '<td>'.AppDate::date($row['added_date']).'</td>';
                                                    echo '<td>'.$row['Name'].'</td>';
                                                    echo '<td style="text-align:center;">
                                                    '.($row['status'] == 1 ? '<a href="javascript:void(0);" class="ManageAction" id="disable_'.$row['course_id'].'"><i class="material-icons" title = "Disable" style="font-size:21px;">block</i></a>' : '<a href="javascript:void(0);" class="ManageAction" id="enable_'.$row['course_id'].'"><i class="material-icons" style="font-size:21px;" title="Enable">check_circle</i></a>').'
                                                    <a href="alumni-edit-course.php?courseId='.$SafeCrypto->encrypt($row['course_id']).'"><i class="material-icons" title="Edit" id="'.$row['course_id'].'" style=" font-size:21px;">edit</i></a>';
                                                   $exists = $dbobj->selectData($dbh, TBL_PRIFIX.'user_login_details', array('user_id'), 'course_id = ?', array($row['course_id']));
                                                    if($exists['total'] == 0){
                                                        echo '<a href="javascript:void(0);" class="ManageAction" id="delete_'.$row['course_id'].'"><i class="material-icons" title="Delete" style="color:#e21313; font-size:21px;">delete</i></a>';
                                                    }
                                                    echo '</td>';
                                                    echo '</tr>';
                                                }
                                            }else{
                                            }
                                        ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- #END# Basic Examples -->
        </div>
    </section>
    <?php include ALUM_TEMPLATES.'footer.php';?> 
    <script src="plugins/bootstrap-select/js/bootstrap-select.js"></script>
    <script>
        $(document).ready(function(){
            $('.ManageAction').on('click',function(){
                var id = $(this).attr('id');
                var Splitvalue = id.split('_');
                if (confirm('Are you sure you want '+Splitvalue[0]+'?')) {
                    $.ajax({
                        type:'POST',
                        url:'ajax/ajax-manage-course.php',
                        data:Splitvalue[0]+'='+Splitvalue[1],
                        success: function(response){
                            window.location.reload();                           
                        }
                    });
                }
            });
            $('[name="sltCourseType"]').selectpicker();
        });
    </script>
</body>

</html>

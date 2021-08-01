<?php
include 'includes/admin-config.php';
include_once 'auth.php';
include 'includes/emp-crypto-graphy.php';
$dbobj = new GetCommomOperation();
$SafeCrypto = new SafeCrypto();
if(isset($_POST['btnsSubject'])){
    $checkExists = $dbobj->check_exist($dbh, TBL_PRIFIX.'subject_master',array('subject_id'), 'subject_code = ? OR subject_name = ?', array(trim($_POST['txtSubjectCode']),trim($_POST['txtSubjectName'])));
    if($checkExists){
        $_SESSION['error'] = 'Subject already exist.';
        header('location:alumni-add-subject');
        exit(0);
    }else{
        $loginSessionId = $dbobj->getSessionId();
        $values = array(trim($_POST['sltCourse']),trim($_POST['sltYearSem']),trim($_POST['txtSubjectCode']),trim($_POST['txtSubjectName']),trim($_POST['sltSubType']),trim($_POST['txtTheoryMarks']),trim($_POST['txtPracticalMarks']),CURRENT_DT,$loginSessionId,$_SERVER['REMOTE_ADDR']);
        $result = $dbobj->InsertQuery($dbh, TBL_PRIFIX.'subject_master', array('course_id','semister_year','subject_code','subject_name','subject_type','theory_marks','practical_marks','added_date','added_by','added_ip'), $values);
        if($result['status']){
            $_SESSION['success'] = 'Subject has been added successfully.';
            header('location:alumni-add-subject');
            exit(0);
        }else{
            $_SESSION['error'] = 'Something went to wrong.';
            header('location:alumni-add-subject');
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
                    <button class="btn btn-primary waves-effect pull-right" id="ShowHide" type="submit">Add Subject</button>
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
                                <h2>Add Subject</h2>
                                <ul class="header-dropdown m-r--5">
                                    <li class="dropdown">
                                        <button class="btn btn-primary waves-effect" name="btnsSubject" type="submit">SUBMIT</button>
                                    </li>
                                </ul>
                            </div>
                            <div class="body">
                                <!-- Inline Layout -->
                                <div class="col-sm-4">
                                    <div class="form-group form-float clearfix">
                                        <div class="form-line">
                                            <select class="form-control" name="sltCourse" title="Select Course" required>
                                                <optgroup label="Select Course ">
                                                <?php 
                                                    $getCourseData = $dbobj->selectData($dbh, TBL_PRIFIX.'course_master', array('course_id','course'), 'status != ?', array(0));
                                                    if($getCourseData['total'] > 0){
                                                        foreach($getCourseData['values'] as $rows){
                                                            echo '<option value="'.$rows['course_id'].'">'.$rows['course'].'</option>';
                                                        }
                                                    }else{
                                                        echo '<option value="no-select">No Record</option>';
                                                    }
                                                ?>
                                                </optgroup>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="form-group form-float clearfix">
                                        <div class="form-line" id="selectData">
                                            <select class="form-control" name="sltYearSem" title="Select Semister/Year" required>
                                                <option value="no-select">No Record</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="form-group form-float clearfix">
                                        <div class="form-line">
                                            <input type="text" class="form-control" required title="Subject Code" name="txtSubjectCode">
                                            <label class="form-label">Enter Subject Code</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="form-group form-float clearfix">
                                        <div class="form-line">
                                            <input type="text" class="form-control" required title="Subject Code" name="txtSubjectName">
                                            <label class="form-label">Enter Subject Name</label>
                                        </div>
                                    </div>
                                </div>
                                 <div class="col-sm-4">
                                    <div class="form-group form-float clearfix">
                                        <div class="form-line">
                                            <select class="form-control" name="sltSubType" title="Select Type" required>
                                                <optgroup label="Select Type ">
                                                     <option value="subject">Subject</option>
                                                     <option value="language">Language</option>
                                                     <option value="optional">Optional</option>
                                                </optgroup>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="form-group form-float clearfix">
                                        <div class="form-line">
                                            <input type="text" class="form-control" required title="Theory Marks" name="txtTheoryMarks">
                                            <label class="form-label">Enter Theory Marks</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="form-group form-float clearfix">
                                        <div class="form-line">
                                            <input type="text" class="form-control" required title="Practical Marks" name="txtPracticalMarks">
                                            <label class="form-label">Enter Practical Marks</label>
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
                                Manage Subject
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
                                            <th>Course</th>
                                            <th>Semister/Year</th>
                                            <th>Subject Code</th>
                                            <th>Subject Name</th>
                                            <th>Type</th>
                                            <th>Theory Marks</th>
                                            <th>Practical Marks</th>
                                            <!-- <th>Added By</th> -->
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php 
                                        $sql = "SELECT concat_ws(' ',T1.fname,T1.lname) as Name,T2.subject_code,T2.theory_marks,T2.practical_marks,T2.subject_name,T2.subject_type,T2.subject_id,T2.semister_year,T3.course,T2.added_date,T2.added_by,T2.status FROM ".TBL_PRIFIX."subject_master as T2 LEFT JOIN  ".TBL_PRIFIX."user_login_details as T1 ON T1.user_id = T2.added_by  LEFT JOIN ".TBL_PRIFIX."course_master as T3 ON T2.course_id = T3.course_id  ORDER BY T2.subject_id ASC";
                                        $stmt = $dbh->prepare($sql);
                                        $stmt->execute();
                                        $rowCount = $stmt->rowCount();
                                        if ($rowCount > 0) {
                                                for ($i = 1; $i <= $rowCount; $i++) {
                                                    $result['values'][] = $stmt->fetch(PDO::FETCH_ASSOC);
                                                }
                                                foreach($result['values'] as $row){
                                                    echo '<tr>';
                                                    echo '<td>'.$row['course'].'</td>';
                                                    echo '<td>'.str_replace('_', ' ', $row['semister_year']).'</td>';
                                                    echo '<td>'.$row['subject_code'].'</td>';
                                                    echo '<td>'.$row['subject_name'].'</td>';
                                                    echo '<td>'.$row['subject_type'].'</td>';
                                                    echo '<td>'.$row['theory_marks'].'</td>';
                                                    echo '<td>'.$row['practical_marks'].'</td>';
                                                    // echo '<td>'.$row['Name'].'</td>';
                                                    echo '<td style="text-align:center;">
                                                    '.($row['status'] == 1 ? '<a href="javascript:void(0);" class="ManageAction" id="disable_'.$row['subject_id'].'"><i class="material-icons" title = "Disable" style="font-size:21px;">block</i></a>' : '<a href="javascript:void(0);" class="ManageAction" id="enable_'.$row['subject_id'].'"><i class="material-icons" style="font-size:21px;" title="Enable">check_circle</i></a>').'
                                                    <a href="alumni-edit-subject.php?subjectId='.$SafeCrypto->encrypt($row['subject_id']).'"><i class="material-icons" title="Edit" id="'.$row['subject_id'].'" style=" font-size:21px;">edit</i></a>';
                                                   $exists = $dbobj->selectData($dbh, TBL_PRIFIX.'user_login_details', array('user_id'), 'subject_id = ?', array($row['subject_id']));
                                                    if($exists['total'] == 0){
                                                        echo '<a href="javascript:void(0);" class="ManageAction" id="delete_'.$row['subject_id'].'"><i class="material-icons" title="Delete" style="color:#e21313; font-size:21px;">delete</i></a>';
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
            $('body').on('change','[name="sltCourse"]',function(){
                var CourseId = $(this).val();
                 $.ajax({
                    type:'POST',
                    url:'ajax/ajax-manage-subject.php',
                    data:'courseId='+CourseId,
                    success: function(response){
                        $('#selectData').html(response);   
                        $('[name="sltYearSem"]').selectpicker();                   
                    }
                });
            });
            $('.ManageAction').on('click',function(){
                var id = $(this).attr('id');
                var Splitvalue = id.split('_');
                if (confirm('Are you sure you want '+Splitvalue[0]+'?')) {
                    $.ajax({
                        type:'POST',
                        url:'ajax/ajax-manage-subject.php',
                        data:Splitvalue[0]+'='+Splitvalue[1],
                        success: function(response){
                            window.location.reload();                           
                        }
                    });
                }
            });
        });
    </script>
</body>

</html>

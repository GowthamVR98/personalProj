<?php
include 'includes/admin-config.php';
include_once 'auth.php';
$dbobj = new GetCommomOperation();
if(isset($_GET['subjectId'])){
    include 'includes/emp-crypto-graphy.php';
    $SafeCrypto = new SafeCrypto();
    $id = $SafeCrypto->decrypt(trim($_GET['subjectId']));
    $exists = $dbobj->selectData($dbh, TBL_PRIFIX.'subject_master', array('course_id','semister_year','subject_code','subject_name','subject_type','theory_marks','practical_marks'), 'subject_id = ?', array($id));
    if($exists['total'] > 0){
        $getData = $exists['values'][0];
    }else{
        $_SESSION['error'] = 'No record found.';
        header('location:alumni-add-subject.php');
        exit(0);
    }
}else{
    $_SESSION['error'] = 'Invalid url.';
    header('location:alumni-add-subject.php');
    exit(0);
}
if(isset($_POST['btnskill'])){
    $checkExists = $dbobj->check_exist($dbh, TBL_PRIFIX.'subject_master',array('subject_id'), 'subject_code = ? OR subject_name = ?', array(trim($_POST['txtSubjectCode']),trim($_POST['txtSubjectName'])),$id,true);
    if($checkExists){
        $_SESSION['error'] = 'Subject already exist.';
        header('location:alumni-edit-subject.php?subjectId='.$SafeCrypto->encrypt($id));
        exit(0);
    }else{
        $update = $dbobj->UpdatetData($dbh, TBL_PRIFIX.'subject_master', 'course_id = ?,semister_year = ?,subject_code = ?,subject_name = ?,subject_type = ?,theory_marks = ?,practical_marks = ?', 'subject_id = ?', array(trim($_POST['sltCourse']),trim($_POST['sltYearSem']),trim($_POST['txtSubjectCode']),trim($_POST['txtSubjectName']),trim($_POST['sltSubType']),trim($_POST['txtTheoryMarks']),trim($_POST['txtPracticalMarks']),$id));
        if($update){
            $_SESSION['success'] = 'Subject has been updated successfully.';
            header('location:alumni-edit-subject.php?subjectId='.$SafeCrypto->encrypt($id));
            exit(0);
        }else{
            $_SESSION['warning'] = 'No changes done.';
            header('location:alumni-edit-subject.php?subjectId='.$SafeCrypto->encrypt($id));
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
            <!-- Calender -->
            <div class="row clearfix">
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                    <div class="card">
                        <form id="form_validation" method="POST">
                            <div class="header">
                                <h2>Edit Subject</h2>
                                <ul class="header-dropdown m-r--5">
                                    <li class="dropdown">
                                        <button class="btn btn-warning waves-effect" onclick="location.href='alumni-add-subject'" type="button">Back</button>
                                        <button class="btn btn-primary waves-effect" name="btnskill" type="submit">Update</button>
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
                                                            if($rows['course_id'] == $getData['course_id']){
                                                                echo '<option selected value="'.$rows['course_id'].'">'.$rows['course'].'</option>';
                                                            }else{
                                                                echo '<option value="'.$rows['course_id'].'">'.$rows['course'].'</option>';
                                                            }
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
                                            <input type="text" class="form-control" required value="<?php echo $getData['subject_code'];?>" title="Subject Code" name="txtSubjectCode">
                                            <label class="form-label">Enter Subject Code</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="form-group form-float clearfix">
                                        <div class="form-line">
                                            <input type="text" class="form-control" required value="<?php echo $getData['subject_name'];?>" title="Subject Code" name="txtSubjectName">
                                            <label class="form-label">Enter Subject Name</label>
                                        </div>
                                    </div>
                                </div>
                                 <div class="col-sm-4">
                                    <div class="form-group form-float clearfix">
                                        <div class="form-line">
                                            <select class="form-control" name="sltSubType" title="Select Type" required>
                                                <optgroup label="Select Type ">
                                                     <option <?php echo ($getData['subject_type'] == 'subject' ? 'selected': '');?> value="subject">Subject</option>
                                                     <option <?php echo ($getData['subject_type'] == 'language' ? 'selected': '');?> value="language">Language</option>
                                                     <option <?php echo ($getData['subject_type'] == 'optional' ? 'selected': '');?> value="optional">Optional</option>
                                                </optgroup>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="form-group form-float clearfix">
                                        <div class="form-line">
                                            <input type="text" class="form-control" required value="<?php echo $getData['theory_marks'];?>" title="Theory Marks" name="txtTheoryMarks">
                                            <label class="form-label">Enter Theory Marks</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="form-group form-float clearfix">
                                        <div class="form-line">
                                            <input type="text" class="form-control" required value="<?php echo $getData['practical_marks'];?>" title="Practical Marks" name="txtPracticalMarks">
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
        </div>
    </section>
    <?php include ALUM_TEMPLATES.'footer.php';?> 
    <script src="plugins/bootstrap-select/js/bootstrap-select.js"></script>
    <script>
        $(document).ready(function(){
            var CourseIds = '<?php echo $getData['course_id']?>';
            var semYearIds = '<?php echo $getData['semister_year']?>';
             $.ajax({
                type:'POST',
                url:'ajax/ajax-manage-subject.php',
                data:'courseId='+CourseIds+'&semId='+semYearIds,
                success: function(response){
                    $('#selectData').html(response);   
                    $('[name="sltYearSem"]').selectpicker();                   
                }
            });
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
        });
    </script>
</body>
</html>
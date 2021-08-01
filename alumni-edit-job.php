<?php
include 'includes/admin-config.php';
include_once 'auth.php';
$dbobj = new GetCommomOperation();
$loginSessionId = $dbobj->getSessionId();
if(isset($_GET['jobyId'])){
    include 'includes/emp-crypto-graphy.php';
    $SafeCrypto = new SafeCrypto();
    $id = $SafeCrypto->decrypt(trim($_GET['jobyId']));
    $exists = $dbobj->selectData($dbh, TBL_PRIFIX.'job_details', array('company_id', 'designation_id', 
                'industry_id', 'expiry_date', 'job_type', 'salary',
                'location', 'job_time', 'job_qualification', 'skill_id', 'min_experience', 'max_experience',
                'number_vacansies','job_description'), 'job_id = ?', array($id));
    if($exists['total'] > 0){
        $data = $exists['values'][0];
    }else{
        $_SESSION['error'] = 'No record found.';
        header('location:alumni-add-job');
        exit(0);
    }
}else{
    $_SESSION['error'] = 'Invalid url.';
    header('location:alumni-add-job.php');
    exit(0);
}
if(isset($_POST['btnskill'])){
    $update = $dbobj->UpdatetData($dbh, TBL_PRIFIX.'job_details', 
        'company_id = ?,designation_id = ?,industry_id = ?,expiry_date = ?,job_type = ?,salary = ?,location = ?, job_time = ?, job_qualification = ?,skill_id = ?,min_experience = ?,max_experience = ?,number_vacansies = ?,job_description = ?,modified_date = ?,modified_by = ?,modified_ip = ?', 'job_id = ?', 
        array(trim($_POST['txtcompany']),trim($_POST['sltDesig']),trim($_POST['sltIndustry']),
            trim(SystemDate::date($_POST['txtexpiryDate'])),trim($_POST['sltJobType']),
            trim($_POST['sltSalary']),trim($_POST['txtLocation']),trim($_POST['sltJobTime']),
            trim($_POST['txtQualification']),trim(implode(',', $_POST['sltSkills'])),trim($_POST['sltMinEx']),
            trim($_POST['sltMaxEx']),trim($_POST['txtVacancies']),trim($_POST['txtDescription']),
            CURRENT_DT,$loginSessionId,$_SERVER['REMOTE_ADDR'],$id));
    if($update){
        $_SESSION['success'] = 'Job details has been updated successfully.';
        header('location:alumni-edit-job.php?jobyId='.$SafeCrypto->encrypt($id));
        exit(0);
    }else{
        $_SESSION['warning'] = 'No changes Done.';
        header('location:alumni-edit-job.php?jobyId='.$SafeCrypto->encrypt($id));
        exit(0);
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
                        <form id="form_validation" method="POST" enctype="multipart/form-data">
                            <div class="header">
                                <h2>Edit Job Details</h2>
                                <ul class="header-dropdown m-r--5">
                                    <li class="dropdown">
                                        <button class="btn btn-warning waves-effect" onclick="location.href='alumni-add-job'" type="button">Back</button>
                                        <button class="btn btn-primary waves-effect" name="btnskill" type="submit">Update</button>
                                    </li>
                                </ul>
                            </div>
                            <div class="body">
                                <!-- Inline Layout -->
                                <div class="form-group form-float">
                                    <div class="form-line">
                                        <select class="form-control" name="txtcompany" data-live-search="true" title="Select Company">
                                            <optgroup label="Select Company">
                                            <?php 
                                                $getCompanyData = $dbobj->selectData($dbh, TBL_PRIFIX.'company_master', array('company_id','company_name'), 'status != ?', array(0));
                                                if($getCompanyData['total'] > 0){
                                                    foreach($getCompanyData['values'] as $rows){
                                                        if($data['company_id'] == $rows['company_id']){
                                                            echo '<option value="'.$rows['company_id'].'" selected>'.$rows['company_name'].'</option>';
                                                        }else{
                                                        echo '<option value="'.$rows['company_id'].'">'.$rows['company_name'].'</option>';
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
                                <div class="form-group form-float">
                                    <div class="form-line">
                                        <select class="form-control" name="sltDesig" title="Select Designation" data-live-search="true">
                                            <optgroup label="Select Designation">
                                            <?php 
                                                $getSkillsData = $dbobj->selectData($dbh, TBL_PRIFIX.'designation_master', array('designation_id','designation'), 'status != ?', array(0));
                                                if($getSkillsData['total'] > 0){
                                                    foreach($getSkillsData['values'] as $rows){
                                                        if($data['designation_id'] == $rows['designation_id']){
                                                            echo '<option value="'.$rows['designation_id'].'" selected>'.$rows['designation'].'</option>';
                                                        }else{
                                                        echo '<option value="'.$rows['designation_id'].'">'.$rows['designation'].'</option>';
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
                                <div class="form-group form-float">
                                    <div class="form-line">
                                        <select class="form-control" name="sltIndustry" data-live-search="true" title="Select Industry">
                                            <optgroup label="Select Industry">
                                            <?php 
                                                $getSkillsData = $dbobj->selectData($dbh, TBL_PRIFIX.'industry_master', array('industry_id','industry'), 'status != ?', array(0));
                                                if($getSkillsData['total'] > 0){
                                                    foreach($getSkillsData['values'] as $rows){
                                                        if($data['industry_id'] == $rows['industry_id']){
                                                            echo '<option value="'.$rows['industry_id'].'" selected>'.$rows['industry'].'</option>';
                                                        }else{
                                                            echo '<option value="'.$rows['industry_id'].'">'.$rows['industry'].'</option>';
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
                                <div class="form-group form-float">
                                    <div class="form-line" id="bs_datepicker_container">
                                        <input type="text" value="<?php echo (!empty($data['expiry_date']) ? AppDate::date($data['expiry_date']) : '');?>" class="form-control" readonly  title="Expiry Date" name="txtexpiryDate" required>
                                        <label class="form-label">Expiry Date</label>
                                    </div>
                                </div>
                                <div class="form-group form-float">
                                    <div class="form-line">
                                        <select class="form-control" name="sltJobType" title="Select Job Type">
                                            <optgroup label="Select Job Type">
                                            <option <?php echo ($data['job_type'] == 'experienced' ? 'selected' : '');?> value="experienced">Experienced</option>
                                            <option <?php echo ($data['job_type'] == 'freshers' ? 'selected' : '');?> value="freshers">Freshers</option>
                                            <option <?php echo ($data['job_type'] == 'intern' ? 'selected' : '');?> value="intern">Intern</option>
                                            </optgroup>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group form-float">
                                    <div class="form-line">
                                        <select class="form-control" name="sltSalary" title="Select Salary">
                                            <optgroup label="Select Salary">
                                            <option <?php echo ($data['salary'] == 'below-1.5' ? 'selected' : '');?> value="below-1.5">Below 1.5 lakh</option>
                                            <option <?php echo ($data['salary'] == '1.5-5' ? 'selected' : '');?> value="1.5-5">1.5 - 5 lakh</option>
                                            <option <?php echo ($data['salary'] == '5-8' ? 'selected' : '');?> value="5-8">5 - 8 lakh</option>
                                            <option <?php echo ($data['salary'] == 'above-8' ? 'selected' : '');?> value="above-8">Above 8 lakh</option>
                                            </optgroup>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group form-float">
                                    <div class="form-line">
                                        <input type="text" value="<?php echo (!empty($data['location']) ? $data['location'] : '');?>"  class="form-control" title="Location" name="txtLocation" required>
                                        <label class="form-label">Location</label>
                                    </div>
                                </div>
                                <div class="form-group form-float">
                                    <div class="form-line">
                                        <select class="form-control" name="sltJobTime" title="Select Job Time">
                                            <optgroup label="Select Job Time">
                                            <option <?php echo ($data['job_time'] == 'full-time' ? 'selected' : '');?> value="full-time">Full Time</option>
                                            <option <?php echo ($data['job_time'] == 'part-time' ? 'selected' : '');?> value="part-time">Part Time</option>
                                            </optgroup>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group form-float">
                                    <div class="form-line">
                                        <input type="text" class="form-control" value="<?php echo (!empty($data['job_qualification']) ? $data['job_qualification'] : '');?>" title="Job Qualification" name="txtQualification" required>
                                        <label class="form-label">Job Qualification</label>
                                    </div>
                                </div>
                                <div class="form-group form-float">
                                    <div class="form-line">
                                        <select class="form-control" name="sltSkills[]" data-live-search="true" multiple title="Select Skills">
                                            <optgroup label="Select Skills">
                                            <?php 
                                                $SkillIds = explode(',',$data['skill_id']);
                                                $getSkillsData = $dbobj->selectData($dbh, TBL_PRIFIX.'skill_master', array('skill_id','skill'), 'status != ?', array(0));
                                                if($getSkillsData['total'] > 0){
                                                    foreach($getSkillsData['values'] as $rows){
                                                        if(in_array($rows['skill_id'],$SkillIds)){
                                                            echo '<option value="'.$rows['skill_id'].'" selected>'.$rows['skill'].'</option>';
                                                        }else{
                                                            echo '<option value="'.$rows['skill_id'].'">'.$rows['skill'].'</option>';
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
                                <div class="form-group form-float">
                                    <div class="form-line">
                                        <select class="form-control" name="sltMinEx" title="Select Min Experience">
                                            <optgroup label="Select Min Experience">
                                            <option <?php echo ($data['min_experience'] == '1' ? 'selected' : '');?> value="1">1</option>
                                            <option <?php echo ($data['min_experience'] == '2' ? 'selected' : '');?> value="2">2</option>
                                            <option <?php echo ($data['min_experience'] == '3' ? 'selected' : '');?> value="3">3</option>
                                            <option <?php echo ($data['min_experience'] == '4' ? 'selected' : '');?> value="4">4</option>
                                            <option <?php echo ($data['min_experience'] == '5' ? 'selected' : '');?> value="5">5</option>
                                            <option <?php echo ($data['min_experience'] == '6' ? 'selected' : '');?> value="6">6</option>
                                            <option <?php echo ($data['min_experience'] == '7' ? 'selected' : '');?> value="7">7</option>
                                            <option <?php echo ($data['min_experience'] == '8' ? 'selected' : '');?> value="8">8</option>
                                            <option <?php echo ($data['min_experience'] == '9' ? 'selected' : '');?> value="9">9</option>
                                            <option <?php echo ($data['min_experience'] == '10' ? 'selected' : '');?> value="10">10</option>
                                            </optgroup>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group form-float">
                                    <div class="form-line">
                                        <select class="form-control" name="sltMaxEx" title="Select Max Experience">
                                            <optgroup label="Select Max Experience">
                                            <option <?php echo ($data['max_experience'] == '1' ? 'selected' : '');?> value="1">1</option>
                                            <option <?php echo ($data['max_experience'] == '2' ? 'selected' : '');?> value="2">2</option>
                                            <option <?php echo ($data['max_experience'] == '3' ? 'selected' : '');?> value="3">3</option>
                                            <option <?php echo ($data['max_experience'] == '4' ? 'selected' : '');?> value="4">4</option>
                                            <option <?php echo ($data['max_experience'] == '5' ? 'selected' : '');?> value="5">5</option>
                                            <option <?php echo ($data['max_experience'] == '6' ? 'selected' : '');?> value="6">6</option>
                                            <option <?php echo ($data['max_experience'] == '7' ? 'selected' : '');?> value="7">7</option>
                                            <option <?php echo ($data['max_experience'] == '8' ? 'selected' : '');?> value="8">8</option>
                                            <option <?php echo ($data['max_experience'] == '9' ? 'selected' : '');?> value="9">9</option>
                                            <option <?php echo ($data['max_experience'] == '10' ? 'selected' : '');?> value="10">10</option>
                                            </optgroup>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group form-float">
                                    <div class="form-line">
                                        <input type="text" class="form-control" value="<?php echo (!empty($data['number_vacansies']) ? $data['number_vacansies'] : '');?>" title="Job Qualification" name="txtVacancies" required>
                                        <label class="form-label">Number Of Vacancies</label>
                                    </div>
                                </div>
                                <div class="form-group form-float">
                                    <div class="form-line">
                                        <textarea class="form-control" title="Job Qualification" name="txtDescription" required><?php echo (!empty($data['job_description']) ? $data['job_description'] : '');?></textarea>
                                        <label class="form-label">Job Description</label>
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
        $('#bs_datepicker_container input').datepicker({
            format: 'dd-mm-yyyy',
            autoclose: true,
            container: '#bs_datepicker_container'
        });
    </script>
</body>
</html>

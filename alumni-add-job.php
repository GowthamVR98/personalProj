<?php
include 'includes/admin-config.php';
include_once 'auth.php';
include 'includes/emp-crypto-graphy.php';
$dbobj = new GetCommomOperation();
$SafeCrypto = new SafeCrypto();
    if(isset($_POST['btnCompany'])){
        $loginSessionId = $dbobj->getSessionId();
        $values = array(trim($_POST['txtcompany']),trim($_POST['sltDesig']),trim($_POST['sltIndustry']),
                        trim(SystemDate::date($_POST['txtexpiryDate'])),trim($_POST['sltJobType']),
                        trim($_POST['sltSalary']),trim($_POST['txtLocation']),trim($_POST['sltJobTime']),
                        trim($_POST['txtQualification']),
                        trim(implode(',', $_POST['sltSkills'])),trim($_POST['sltMinEx']),trim($_POST['sltMaxEx']),
                        trim($_POST['txtVacancies']),trim($_POST['txtDescription']),
                        CURRENT_DT,$loginSessionId,$_SERVER['REMOTE_ADDR']);
        $result = $dbobj->InsertQuery($dbh, TBL_PRIFIX.'job_details', 
            array('company_id', 'designation_id', 'industry_id', 'expiry_date', 'job_type', 'salary',
                 'location', 'job_time', 'job_qualification', 'skill_id', 'min_experience', 'max_experience',
                 'number_vacansies','job_description', 'added_date', 'added_by','added_ip'), $values,true);
        if($result['status']){
            include_once ALUM_ETEMPLATES.'job-details-tpl.php';
            $_SESSION['success'] = 'New job details has been added successfully.';
            header('location:alumni-add-job.php');
            exit(0);
        }else{
            $_SESSION['error'] = 'Something went to wrong.';
            header('location:alumni-add-job.php');
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
            <div class="block-header">
                <h2>
                    <button class="btn btn-primary waves-effect pull-right" id="ShowHide" type="submit">Add Job</button>
                    <button class="btn btn-primary waves-effect pull-right" id="hideOptionBlock" type="button" style="display:none;">Hide</button>
                </h2>
                <div class="clearfix"></div>
            </div>
            <!-- Calender -->
            <div class="row clearfix"  id="hideShow">
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                    <div class="card">
                        <form id="form_validation" method="POST" enctype="multipart/form-data">
                            <div class="header">
                                <h2>Add Job</h2>
                                <ul class="header-dropdown m-r--5">
                                    <li class="dropdown">
                                        <button class="btn btn-primary waves-effect" name="btnCompany" type="submit">SUBMIT</button>
                                    </li>
                                </ul>
                            </div>
                            <div class="body">
                                <!-- Inline Layout -->
                                <div class="col-sm-6">
                                    <div class="form-group form-float ">
                                        <div class="form-line">
                                            <select class="form-control" name="txtcompany" data-live-search="true" title="Select Company">
                                                <optgroup label="Select Company">
                                                <?php 
                                                    $getCompanyData = $dbobj->selectData($dbh, TBL_PRIFIX.'company_master', array('company_id','company_name'), 'status != ?', array(0));
                                                    if($getCompanyData['total'] > 0){
                                                        foreach($getCompanyData['values'] as $rows){
                                                            echo '<option value="'.$rows['company_id'].'">'.$rows['company_name'].'</option>';
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
                                <div class="col-sm-6">
                                    <div class="form-group form-float">
                                        <div class="form-line">
                                            <select class="form-control" name="sltDesig" title="Select Designation" data-live-search="true">
                                                <optgroup label="Select Designation">
                                                <?php 
                                                    $getSkillsData = $dbobj->selectData($dbh, TBL_PRIFIX.'designation_master', array('designation_id','designation'), 'status != ?', array(0));
                                                    if($getSkillsData['total'] > 0){
                                                        foreach($getSkillsData['values'] as $rows){
                                                            echo '<option value="'.$rows['designation_id'].'">'.$rows['designation'].'</option>';
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
                                <div class="col-sm-6">
                                    <div class="form-group form-float">
                                        <div class="form-line">
                                            <select class="form-control" name="sltIndustry" data-live-search="true" title="Select Industry">
                                                <optgroup label="Select Industry">
                                                <?php 
                                                    $getSkillsData = $dbobj->selectData($dbh, TBL_PRIFIX.'industry_master', array('industry_id','industry'), 'status != ?', array(0));
                                                    if($getSkillsData['total'] > 0){
                                                        foreach($getSkillsData['values'] as $rows){
                                                            echo '<option value="'.$rows['industry_id'].'">'.$rows['industry'].'</option>';
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
                                <div class="col-sm-6">
                                    <div class="form-group form-float">
                                        <div class="form-line" id="bs_datepicker_container">
                                            <input type="text" class="form-control" readonly  title="Expiry Date" name="txtexpiryDate" required>
                                            <label class="form-label">Expiry Date</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group form-float">
                                        <div class="form-line">
                                            <select class="form-control" name="sltJobType" title="Select Job Type">
                                                <optgroup label="Select Job Type">
                                                <option value="experienced">Experienced</option>
                                                <option value="freshers">Freshers</option>
                                                <option value="intern">Intern</option>
                                                </optgroup>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group form-float">
                                        <div class="form-line">
                                            <select class="form-control" name="sltSalary" title="Select Salary">
                                                <optgroup label="Select Salary">
                                                <option value="below-1.5">Below 1.5 lakh</option>
                                                <option value="1.5-5">1.5 - 5 lakh</option>
                                                <option value="5-8">5 - 8 lakh</option>
                                                <option value="above-8">Above 8 lakh</option>
                                                </optgroup>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group form-float">
                                        <div class="form-line">
                                            <input type="text" class="form-control" title="Location" name="txtLocation" required>
                                            <label class="form-label">Location</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group form-float">
                                        <div class="form-line">
                                            <select class="form-control" name="sltJobTime" title="Select Job Time">
                                                <optgroup label="Select Job Time">
                                                <option value="full-time">Full Time</option>
                                                <option value="part-time">Part Time</option>
                                                </optgroup>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group form-float">
                                        <div class="form-line">
                                            <input type="text" class="form-control"  title="Job Qualification" name="txtQualification" required>
                                            <label class="form-label">Job Qualification</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group form-float">
                                        <div class="form-line">
                                            <select class="form-control" name="sltSkills[]" data-live-search="true" multiple title="Select Skills">
                                                <optgroup label="Select Skills">
                                                <?php 
                                                    $getSkillsData = $dbobj->selectData($dbh, TBL_PRIFIX.'skill_master', array('skill_id','skill'), 'status != ?', array(0));
                                                    if($getSkillsData['total'] > 0){
                                                        foreach($getSkillsData['values'] as $rows){
                                                            echo '<option value="'.$rows['skill_id'].'">'.$rows['skill'].'</option>';
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
                                <div class="col-sm-6">
                                    <div class="form-group form-float">
                                        <div class="form-line">
                                            <select class="form-control" name="sltMinEx" title="Select Min Experience">
                                                <optgroup label="Select Min Experience">
                                                <option value="1">1</option>
                                                <option value="2">2</option>
                                                <option value="3">3</option>
                                                <option value="4">4</option>
                                                <option value="5">5</option>
                                                <option value="6">6</option>
                                                <option value="7">7</option>
                                                <option value="8">8</option>
                                                <option value="9">9</option>
                                                <option value="10">10</option>
                                                </optgroup>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group form-float">
                                        <div class="form-line">
                                            <select class="form-control" name="sltMaxEx" title="Select Max Experience">
                                                <optgroup label="Select Max Experience">
                                                <option value="1">1</option>
                                                <option value="2">2</option>
                                                <option value="3">3</option>
                                                <option value="4">4</option>
                                                <option value="5">5</option>
                                                <option value="6">6</option>
                                                <option value="7">7</option>
                                                <option value="8">8</option>
                                                <option value="9">9</option>
                                                <option value="10">10</option>
                                                </optgroup>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group form-float">
                                        <div class="form-line">
                                            <input type="text" class="form-control"  title="Job Qualification" name="txtVacancies" required>
                                            <label class="form-label">Number Of Vacancies</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group form-float">
                                        <div class="form-line">
                                            <textarea class="form-control"  title="Job Qualification" name="txtDescription" required></textarea>
                                            <label class="form-label">Job Description</label>
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
                                Manage Job Details
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
                                            <th>Company</th>
                                            <th>Designation</th>
                                            <th>Industry</th>
                                            <th>Job Type</th>
                                            <th>Job Time</th>
                                            <th>Expiry Date</th>
                                            <th>Added By</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php 
                                        $sql = "SELECT concat_ws(' ',T1.fname,T1.lname) as Name, T2.expiry_date,T2.job_type,T2.job_time,T2.job_id,T3.company_name,T5.industry,T4.designation,T2.status,T2.added_date,T2.added_by FROM ".TBL_PRIFIX."job_details as T2
                                            LEFT JOIN ".TBL_PRIFIX."user_login_details as T1 ON T1.user_id = T2.added_by
                                            LEFT JOIN ".TBL_PRIFIX."company_master as T3 ON T2.company_id = T3.company_id
                                            LEFT JOIN ".TBL_PRIFIX."designation_master as T4 ON T2.designation_id = T4.designation_id
                                            LEFT JOIN ".TBL_PRIFIX."industry_master as T5 ON T2.industry_id = T5.industry_id
                                            GROUP BY T2.job_id ASC";
                                        $stmt = $dbh->prepare($sql);
                                        $stmt->execute();
                                        $rowCount = $stmt->rowCount();
                                        if ($rowCount > 0) {
                                                for ($i = 1; $i <= $rowCount; $i++) {
                                                    $result['values'][] = $stmt->fetch(PDO::FETCH_ASSOC);
                                                }
                                                foreach($result['values'] as $row){
                                                    echo '<tr>';
                                                    echo '<td>'.$row['company_name'].'</td>';
                                                    echo '<td>'.$row['designation'].'</td>';
                                                    echo '<td>'.$row['industry'].'</td>';
                                                    echo '<td>'.$row['job_type'].'</td>';
                                                    echo '<td>'.$row['job_time'].'</td>';
                                                    echo '<td>'.AppDate::date($row['expiry_date']).'</td>';
                                                    echo '<td>'.$row['Name'].'</td>';
                                                    echo '<td style="text-align:center;">
                                                    '.($row['status'] == 1 ? '<a href="javascript:void(0);" class="ManageAction" id="disable_'.$row['job_id'].'"><i class="material-icons" title = "Disable" style="font-size:21px;">block</i></a>' : '<a href="javascript:void(0);" class="ManageAction" id="enable_'.$row['job_id'].'"><i class="material-icons" style="font-size:21px;" title="Enable">check_circle</i></a>').'
                                                    <a href="alumni-edit-job.php?jobyId='.$SafeCrypto->encrypt($row['job_id']).'"><i class="material-icons" title="Edit" id="'.$row['job_id'].'" style=" font-size:21px;">edit</i></a>
                                                    <a href="javascript:void(0);" class="ManageAction" id="delete_'.$row['job_id'].'"><i class="material-icons" title="Delete" style="color:#e21313; font-size:21px;">delete</i></a>
                                                    </td>';
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
        $('#bs_datepicker_container input').datepicker({
            format: 'dd-mm-yyyy',
            autoclose: true,
            container: '#bs_datepicker_container'
        });
        $('.ManageAction').on('click',function(){
            var id = $(this).attr('id');
            var Splitvalue = id.split('_');
            if (confirm('Are you sure you want '+Splitvalue[0]+'?')) {
                $.ajax({
                    type:'POST',
                    url:'ajax/ajax-manage-job.php',
                    data:Splitvalue[0]+'='+Splitvalue[1],
                    success: function(response){
                        window.location.reload();                           
                    }

                });
            } else {
                // Do nothing!
            }
        });
    </script>
</body>

</html>

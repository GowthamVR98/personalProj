<?php 
    include('../includes/smtp-mailer-function.php');
    include('../includes/project-constant.php');
    //DB CONNECTION USING PDO
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "alumni_mgmt_system";
    // $userName = 'id12382119_alumniportal';
    // $password = 'Goutham1998';
    try {
        $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } catch (PDOException $e) {
        echo "Connection failed: " . $e->getMessage();
    }
    include ('alumni-cron-job-class.php');  
    $cronJob = new mailCron($conn);
    $cronJob->callCronJobData();
?>
<?php
    include('../includes/admin-config.php');
    $json = array();
    $sqlQuery = "SELECT id,title,start,end,backgroundColor,borderColor FROM alumni_events ORDER BY :id";
    $query = $dbh->prepare($sqlQuery);
    $query->bindParam(':id',$id);
    $query->execute();
    $row = $query->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($row);
?>
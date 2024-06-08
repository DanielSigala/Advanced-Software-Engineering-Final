<?php
function log_action($log_type, $log_details) {
    $dblink = db_connect();

    $escaped_details = mysqli_real_escape_string($dblink, $log_details); // Basic sanitization
    $sql = "INSERT INTO DEVDATA.log_entries (log_type, log_details) VALUES ('$log_type', '$escaped_details')";
    $dblink->query($sql);

    $dblink->close();
}
?>
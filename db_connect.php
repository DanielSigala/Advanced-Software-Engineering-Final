<?php
function db_connect() {
    $un = "webuser"; 
    $pw = "JAyxJwC496U38Jt4"; 
    $db = "DEVDATA"; 
    $hostname = "localhost"; 

    // Create a new mysqli connection
    $dblink = new mysqli($hostname, $un, $pw, $db);

    // Check the connection
    if ($dblink->connect_error) {
        die("Connection failed: " . $dblink->connect_error);
    }

    return $dblink;
}
function redirect($url) {
    header("Location: $url");
    exit();
}
?>
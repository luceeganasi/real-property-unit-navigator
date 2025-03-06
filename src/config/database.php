<?php
    $servername = "bqmveegfxbsdpdoivajw-mysql.services.clever-cloud.com";
    $username = "ubtpof1rh25kwlpl";
    $password = "K1H1WLTClrQu4MRNE9MV";
    $db = "bqmveegfxbsdpdoivajw";

    // Create connection
    $conn = new mysqli($servername, $username, $password, $db);

    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
?>
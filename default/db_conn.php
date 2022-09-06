<?php
    $conn = oci_connect('m', 'm', '//localhost/XE');
    // Check connection
    if (!$conn) {
        echo 'Failed to connect to oracle' . "<br>";
    }
?>
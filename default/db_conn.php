<?php
    $conn = oci_connect('mccSara', '123', '//localhost/XE');
    // Check connection
    if (!$conn) {
        echo 'Failed to connect to oracle' . "<br>";
    }
    else{
        // echo 'Connected successfully';
        // ?>
        // <script>
        //     alert("Connection successful");
        // </script>
        // <?php
    }
?>
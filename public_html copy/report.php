<?php
    header('Content-type: text/html');
    session_name('LAST_ACTIVE');
    session_start();
    if(!array_key_exists('username', $_SESSION)) {
        require('../private_html/denied.html');
    } else {
        require('../private_html/loadTimeAndLocation.php');
    }
?>
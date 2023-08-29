<?php
    header('Content-type: text/html');
    session_name('LAST_ACTIVE');
    session_start();
    if(!array_key_exists('admin', $_SESSION) || $_SESSION['admin'] != 1) {
        require('../private_html/denied.html');
    } else {
        require('../private_html/users.html');
    }
?>
<?php
    header('Content-type: text/html');
    session_name('LAST_ACTIVE');
    session_start();
    if(!array_key_exists('username', $_SESSION)) {
        if(!array_key_exists('user', $_POST)) {
            require('../private_html/login.html');
            die();
        } else {
            $db_user = "root";
            $db_password = "qazwsxEDCRFV123!@#";
            $db_host = "localhost";
            $db_name = "users";
            $dbc = mysqli_connect($db_host, $db_user, $db_password);
            if(!$dbc) {
                die("Could not open db");
            }
            mysqli_select_db($dbc, $db_name);
            $user = $_POST['user'];
            $password = $_POST['password'];
            $query = $dbc->prepare('SELECT * FROM users WHERE username=?');
            $query->bind_param("s", $user);
            $query->execute();
            $record = mysqli_fetch_assoc($query->get_result());
            if($record == null || !array_key_exists('hash', $record)) {
                $query = $dbc->prepare('SELECT * FROM users WHERE email=?');
                $query->bind_param("s", $user);
                $query->execute();
                $record = mysqli_fetch_assoc($query->get_result());
            }
            if($record == null || !array_key_exists('hash', $record)) {
                require('../private_html/login.html');
                print '<aside>Could not verify your login details</aside>';
                die();
            }
            if(hash('sha256', $password) == $record['hash']) {
                $_SESSION['username'] = $record['username'];
                $_SESSION['admin'] = $record['admin'];
                #Redirect syntax from https://stackoverflow.com/a/768472
                #Here I am using the redirect to prevent back button breakage.
                header("Location: /index.php");
                die();
            } else {
                require('../private_html/login.html');
                print '<aside>Could not verify your login details</aside>';
                die();
            }
        }
    } else {
        require('../private_html/index.php');
        die();
    }
?>
<?php
    header('Content-type: application/json');
    date_default_timezone_set('America/Los_Angeles');
    $date = date('m/y/d \a\t h:m:s', time());
    $address = $_SERVER['REMOTE_ADDR'];
    print json_encode(array('title' => 'Hello, PHP!', 'heading' => 'Hello, PHP!', 'message' => 'This page was generated with the PHP programming language', 'time' => $date, 'IP' => $address));
?>
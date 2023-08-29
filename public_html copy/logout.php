<?php
    header('Content-type: text/html');
    session_name('LAST_ACTIVE');
    session_start();
    session_destroy();
    require('../private_html/login.html');
    print '<aside>Successfully logged out!</aside>';
    #printing below ensures the metadata of the page (like the icon) is still rendered
    #I correct visual ordering via CSS in login.php
?>
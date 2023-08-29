
<?php
        header('Content-type: application/json');
        session_name('LAST_ACTIVE');
        session_start();
        if(!array_key_exists('admin', $_SESSION) || $_SESSION['admin'] != 1) {
            die("{result: \"" . "Must be an admin to access users" . "\"}");
        }
        $db_user = "root";
        $db_password = "qazwsxEDCRFV123!@#";
        $db_host = "localhost";
        $db_name = "users";
        $dbc = mysqli_connect($db_host, $db_user, $db_password);
        if(!$dbc) {
            die("{result: \"" . "Could not open db" . "\"}");
        }
        mysqli_select_db($dbc, $db_name);
        $uri_path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $uri_segments = explode('/', $uri_path);
        $table = $uri_segments[2];
        $id = '';
        if(count($uri_segments) == 4) {
            $id = $uri_segments[3];
        }
        if(count($uri_segments) > 4 || count($uri_segments) < 3) {
            die("{result: \"" . "Incorrect path!" . "\"}");
        }
        if($table != 'users') {
            die("{result: \"" . "Invalid table!" . "\"}");
        }
        $mode = $_SERVER['REQUEST_METHOD'];
        //https://stackoverflow.com/a/39508364
        $_POST = json_decode(file_get_contents('php://input'), true);
        if($mode == 'GET') {
            handleGet($id, $table, $dbc);
        } else if($mode == 'PUT') {
            handlePut($id, $table, $dbc);
        } else if($mode == 'PATCH') {
            handlePatch($id, $table, $dbc);
        } else if($mode == 'POST') {
            handlePost($id, $table, $dbc);
        } else if($mode == 'DELETE') {
            handleDelete($id, $table, $dbc);
        } else {
            die("{result: \"" . "Invalid method!" . "\"}");
        }

        function handlePost($id, $table, $dbc) {
            if($id == '') {
                if(!array_key_exists('username', $_POST)) {
                    insertBlank($table, $dbc);
                } else {
                    $username = $_POST['username'];
                    $admin = $_POST['admin'];
                    $email = $_POST['email'];
                    if((!preg_match('/^.{1,64}$/', $username)) || (!preg_match('/^[01]$/', $admin)) || (!preg_match('/^.+\@.+$/', $email))) {
                        die("{result: \"" . "Invalid parameters!" . "\"}");
                    }
                    $password = $_POST['hash'];
                    $hash = hash('sha256', $password);
                    $query = $dbc->prepare('INSERT INTO users (username, email, hash, admin) VALUES (?, ?, ?, ?)');
                    $query->bind_param("ssss", $username, $email, $hash, $admin);
                    $query->execute();
                    die("{result: \"" . "Ok" . "\"}");
                }
            } else {
                die("{result: \"" . "Cannot specify id!" . "\"}");
            }
        }

        function insertBlank($table, $dbc) {
            $query = 'INSERT INTO users (username, email, hash, admin) VALUES (\'-\', \'-\', \'-\', \'0\')';
            $result = mysqli_query($dbc, $query);
            die("{result: \"" . "Ok" . "\"}");
        }

        function handlePatch($id, $table, $dbc) {
            if($id == '') {
                die("{result: \"" . "Needs id!" . "\"}");
            } else {
                #validate id
                if(!preg_match('/^[0-9]{1,8}$/', $id)) {
                    die("{result: \"" . "Invalid id!" . "\"}");
                }
                $query = $dbc->prepare('SELECT * FROM users WHERE BINARY id=?');
                $query->bind_param("s", $id);
                $query->execute();
                $current = mysqli_fetch_assoc($query->get_result());
                $username = array_key_exists('username', $_POST) ? $_POST['username'] : $current['username'];
                $email = array_key_exists('email', $_POST) ? $_POST['email'] : $current['email'];
                $admin = array_key_exists('admin', $_POST) ? $_POST['admin'] : $current['admin'];
                if((!preg_match('/^.{1,64}$/', $username)) || (!preg_match('/^[01]$/', $admin)) || (!preg_match('/^.+\@.+$/', $email))) {
                    die("{result: \"" . "Invalid parameters!" . "\"}");
                }
                $hash = array_key_exists('hash', $_POST) ? hash('sha256', $_POST['hash']) : $current['hash'];
                $query = $dbc->prepare('UPDATE users SET username=?, email=?, hash=?, admin=? WHERE id=?');
                $query->bind_param("ssssi", $username, $email, $hash, $admin, $id);
                $query->execute();
                die("{result: \"" . "Ok" . "\"}");
            }
        }

        function handleDelete($id, $table, $dbc) {
            $query = null;
            if($id != '') {
                #validate id
                if(!preg_match('/^[0-9]{1,8}$/', $id)) {
                    die("{result: \"" . "Invalid id!" . "\"}");
                }
            } else {
                die("{result: \"" . "Needs id!" . "\"}");
            }
            $query = $dbc->prepare('DELETE FROM users WHERE BINARY id=?');
            $query->bind_param("i", $id);
            $query->execute();
            die("{result: \"" . "Ok" . "\"}");
        }

        function handlePut($id, $table, $dbc) {
            if($id != '') {
                #validate id
                if(!preg_match('/^[0-9]{1,8}$/', $id)) {
                    die("{result: \"" . "Invalid id!" . "\"}");
                }
            } else {
                die("{result: \"" . "Needs id!" . "\"}");
            }
            $username = $_POST['username'];
            $admin = $_POST['admin'];
            $email = $_POST['email'];
            if((!preg_match('/^.{1,64}$/', $username)) || (!preg_match('/^[01]$/', $admin)) || (!preg_match('/^.+\@.+$/', $email))) {
                die("{result: \"" . "Invalid parameters!" . "\"}");
            }
            $hash = $_POST['hash'];
            if(!preg_match('/^[a-f0-9]{64}$/', $hash)) {
                $hash = hash('sha256', $hash);
            }
            $query = $dbc->prepare('UPDATE users SET username=?, email=?, hash=?, admin=? WHERE id=?');
            $query->bind_param("ssssi", $username, $email, $hash, $admin, $id);
            $query->execute();
            die("{result: \"" . "Ok" . "\"}");
        }

        function handleGet($id, $table, $dbc) {
            $query = null;
            $result = null;
            if($id != '') {
                #validate id
                if(!preg_match('/^[0-9]{1,8}$/', $id)) {
                    die("{result: \"" . "Invalid id!" . "\"}");
                }
                $query = $dbc->prepare('SELECT * FROM users WHERE BINARY id=?');
                $query->bind_param("i", $id);
                $query->execute();
                $result = $query->get_result();
            } else {
                $query = 'SELECT * FROM users';
                $result = mysqli_query($dbc, $query);
            }
            #https://stackoverflow.com/questions/383631/json-encode-mysql-results
            $rarray = array();
            while($key = mysqli_fetch_assoc($result)) {
                $rarray[] = $key;
            }
            print json_encode($rarray);
        }
    ?>
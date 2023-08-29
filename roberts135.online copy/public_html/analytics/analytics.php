
    <?php
        header('Content-type: application/json');
        $db_user = "root";
        $db_password = "qazwsxEDCRFV123!@#";
        $db_host = "localhost";
        $db_name = "analytics";
        $dbc = mysqli_connect($db_host, $db_user, $db_password);
        if(!$dbc) {
            die("Could not open db");
        }
        mysqli_select_db($dbc, $db_name);
        $uri_path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $uri_segments = explode('/', $uri_path);
        $table = $uri_segments[3];
        $id = '';
        if(count($uri_segments) == 5) {
            $id = $uri_segments[4];
        }
        if(count($uri_segments) > 5 || count($uri_segments) < 4) {
            die('Incorrect Path!');
        }
        if(!($table == 'static' || $table == 'performance' || $table == 'activity')) {
            die("Invalid Table!" . $table);
        }
        $mode = $_SERVER['REQUEST_METHOD'];
        if($mode == 'GET') {
            if($_GET['noscript'] == 'true') {
                logNoScript($dbc);
            } else {
                handleGet($id, $table, $dbc);
            }
        } else if($mode == 'PUT') {
            handlePut($id, $table, $dbc);
        } else if($mode == 'POST') {
            handlePost($id, $table, $dbc);
        } else if($mode == 'PATCH') {
            handlePatch($id, $table, $dbc);
        } else if($mode == 'DELETE') {
            handleDelete($id, $table, $dbc);
        } else {
            die('Invalid method!');
        }

        function handlePost($id, $table, $dbc) {
            if($id == '') {
                handlePut(generateID(), $table, $dbc);
            } else if ($table == 'activity') {
                handlePatch($id, $table, $dbc);
            } else {
                die('Cannot specify id!');
            }
        }

        function handleDelete($id, $table, $dbc) {
            $query = null;
            if($id != '') {
                #$id = $_GET['id'];
                #validate id
                if(!preg_match('/^[A-Za-z0-9\_\*]+$/', $id)) {
                    die("Invalid id!");
                }
            } else {
                die('Needs id!');
            }
            switch ($table) {
                case "static":
                    $query = $dbc->prepare('DELETE FROM static WHERE BINARY id=?');
                    $query->bind_param("s", $id);
                    break;
                case "performance":
                    $query = $dbc->prepare('DELETE FROM performance WHERE BINARY id=?');
                    $query->bind_param("s", $id);
                    break;
                case "activity":
                    $query = $dbc->prepare('DELETE FROM activity WHERE BINARY id=?');
                    $query->bind_param("s", $id);
                    break;
            } 
            $query->execute();
        }

        function handlePatch($id, $table, $dbc) {
            if($id != '') {
                #validate id
                if(!preg_match('/^[A-Za-z0-9\_\*]+$/', $id)) {
                    die("Invalid id!");
                }
            } else {
                die("Needs id!");
            }
            if($table != 'activity') {
                die('Method not allowed!');
            }
            $requestBody = fopen('php://input', 'r');
            $parameters = explode('|', stream_get_contents($requestBody));
            $query = $dbc->prepare('SELECT activity FROM activity WHERE BINARY id=?');
            $query->bind_param("s", $id);
            $query->execute();
            $current = mysqli_fetch_assoc($query->get_result());
            $data = null;
            if($current == null) {
                die('Record not found!');
            }
            $data = $current['activity'] . validateActivity($parameters);
            $query = $dbc->prepare('UPDATE activity SET activity=? WHERE id=?');
            $query->bind_param("ss", $data, $id);
            $query->execute();
        }

        function handlePut($id, $table, $dbc) {
            if($id != '') {
                #validate id
                if(!preg_match('/^[A-Za-z0-9\_\*]+$/', $id)) {
                    die("Invalid id!");
                }
            } else {
                die("Needs id!");
            }
            $requestBody = fopen('php://input', 'r');
            $query = "REPLACE INTO " . $table . " VALUES ";
            $parameters = explode('|', stream_get_contents($requestBody));
            switch ($table) {
                case "static":
                    $query .= processStatic($parameters, $id) . ';';
                    print $query;
                    $result = mysqli_query($dbc, $query);
                    #add ip address lookup
                    $query = $dbc->prepare('REPLACE INTO locations VALUES (?, ?)');
                    $ip = $_SERVER['REMOTE_ADDR'];
                    #from https://stackoverflow.com/a/17864552, https://stackoverflow.com/a/26893233
                    $loc = explode(",", json_decode(file_get_contents("http://ipinfo.io/{$ip}/json"))->loc);
                    $rounded_loc = round($loc[0]) . ',' . round($loc[1]);
                    $query->bind_param("ss", $ip, $rounded_loc);
                    $query->execute();
                    break;
                case "performance":
                    $query .= processPerformance($parameters, $id) . ';';
                    print $query;
                    $result = mysqli_query($dbc, $query);
                    break;
                case "activity":
                    $query .= processActivity($parameters, $id) . ';';
                    $result = mysqli_query($dbc, $query);
                    break;
            }
        }

        function handleGet($id, $table, $dbc) {
            $query = null;
            $result = null;
            if($id != '') {
                #$id = $_GET['id'];
                #validate id
                if(!preg_match('/^[A-Za-z0-9\_\*]+$/', $id)) {
                    die("Invalid id!");
                }
                switch ($table) {
                    case "static":
                        $query = $dbc->prepare('SELECT * FROM static WHERE BINARY id=?');
                        $query->bind_param("s", $id);
                        break;
                    case "performance":
                        $query = $dbc->prepare('SELECT * FROM performance WHERE BINARY id=?');
                        $query->bind_param("s", $id);
                        break;
                    case "activity":
                        $query = $dbc->prepare('SELECT * FROM activity WHERE BINARY id=?');
                        $query->bind_param("s", $id);
                        break;
                }
                $query->execute();
                $result = $query->get_result();
            } else {
                switch ($table) {
                    case "static":
                        $query = 'SELECT * FROM static';
                        break;
                    case "performance":
                        $query = 'SELECT * FROM performance';
                        break;
                    case "activity":
                        $query = 'SELECT * FROM activity';
                        break;
                }
                $result = mysqli_query($dbc, $query);
            }
            #https://stackoverflow.com/questions/383631/json-encode-mysql-results
            $rarray = array();
            while($key = mysqli_fetch_assoc($result)) {
                $rarray[] = $key;
            }
            print json_encode($rarray);
        }

        function logNoScript($dbc) {
            $query = 'INSERT INTO static VALUES (\'' . generateID() . '\', null, null, null, \'0\', null, null, null, null, null, null, null, null, null, \'' . $_SERVER['REMOTE_ADDR'] . '\');';
            mysqli_query($dbc, $query);
        }

        function validateActivity($parameters) {
            $result = '';
            for($x = 0; $x < count($parameters); $x++) {
                //check for quotes
                if(preg_match('/\'/', $parameters[$x])) {
                    continue;
                }

                if(!preg_match('/^[a-z]~.+$/', $parameters[$x])) {
                    print $parameters[$x];
                    continue;
                }

                $result .= '|' . $parameters[$x];
            }
            return $result;
        }

        function processActivity($parameters, $id) {
            $result = '';
            for($x = 0; $x < count($parameters); $x++) {
                //check for quotes
                if(preg_match('/\'/', $parameters[$x])) {
                    continue;
                }

                if(!preg_match('/^[a-z]~.+$/', $parameters[$x])) {
                    print $parameters[$x];
                    continue;
                }

                $result .= '|' . $parameters[$x];
            }
            return '(\'' . $id . '\', \'' . $result . '\')';
        }
        
        function processPerformance($parameters, $id) {
            if(count($parameters) != 4) {
                die("Wrong number of parameters!");
            }
            $filters = array(
                0  => "/^[A-Za-z0-9\{\}\,\: \n\"]{0,65535}$/",
                1  => "/^[0-9]{0,19}$/",
                2  => "/^[0-9]{0,19}$/",
                3  => "/^[0-9]{0,8}$/"
            );
            if(!checkParameters($filters, $parameters)) {
                die("Invalid entry!");
            }
            return '(\'' . $id . '\', \'' . implode('\', \'', $parameters) . '\')';
        }

        function processStatic($parameters, $id) {
            if(count($parameters) != 13) {
                die("Wrong number of parameters!");
            }
            $filters = array(
                0  => "/^.{0,255}$/",
                1  => "/^.{0,32}$/",
                2  => "/^[01]$/",
                3  => "/^[01]$/",
                4  => "/^[01]$/",
                5  => "/^[01]$/",
                6  => "/^[0-9]{1,6}$/",
                7  => "/^[0-9]{1,6}$/",
                8  => "/^[0-9]{1,6}$/",
                9  => "/^[0-9]{1,6}$/",
                10  => "/^[0-9]{1,6}$/",
                11  => "/^[0-9]{1,6}$/",
                12  => "/^.{0,32}$/"
            );
            if(!checkParameters($filters, $parameters)) {
                die("Invalid entry!");
            }
            return '(\'' . $id . '\', \'' . implode('\', \'', $parameters) . '\', \'' . $_SERVER['REMOTE_ADDR'] . '\')';
        }

        function checkParameters($filters, $parameters) {
            for($x = 0; $x < count($parameters); $x++) {
                //check for quotes
                if(preg_match('/\'/', $parameters[$x])) {
                    return false;
                }
                //check formatting
                if(!preg_match($filters[$x], $parameters[$x])) {
                    print $parameters[$x];
                    return false;
                }
            }
            return true;
        }

        function generateID() {
            $strID = '';
            for($x = 0; $x < 51; $x++) {
                $strID .= substr('ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789_*', rand(0, 63), 1);
            }
            return $strID;
        }





        #$myfile = fopen("analogfile.txt", "a") or die("Unable to open file!");
    	#$txt = "";
        #$keys = array_keys($_GET);
        #foreach ($keys as $key) {
        #   $txt .= "$key: $_GET[$key], ";
        #}
        #$requestBody = fopen('php://input', 'r');
        #fwrite($myfile, stream_get_contents($requestBody));
    	#fwrite($myfile, $txt);
        #fwrite($myfile, $uri_segments[3]);
    	#fclose($myfile);

	    /*$myfile = fopen("analogfile.txt", "a") or die("Unable to open file!");
    	$txt = "";
        $keys = array_keys($_GET);
        foreach ($keys as $key) {
           $txt .= "$key: $_GET[$key], ";
        }
        $requestBody = fopen('php://input', 'r');
        fwrite($myfile, stream_get_contents($requestBody));
    	fwrite($myfile, $txt);
        fwrite($myfile, $uri_segments[3]);
    	fclose($myfile);*/
    ?>
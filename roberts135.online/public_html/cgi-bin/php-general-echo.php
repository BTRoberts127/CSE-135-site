<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <link rel="icon" href="images/favicon.svg">
  <title>Hello, PHP HTML World!</title>
</head>

<body>
    <h1 align="center">General Echo</h1>
    <hr />
    <p><b>Request Method:</b> 
        <?php
            print $_SERVER['REQUEST_METHOD'];
        ?>
    </p>
    <p><b>Protocol:</b> 
        <?php
            print $_SERVER['SERVER_PROTOCOL'];
        ?>
    </p>
    <h2>Query:</h2>
    <ul>
        <?php
            $keys = array_keys($_GET);
            foreach ($keys as $key) {
                print "<li><b>$key:</b> $_GET[$key]</li>";
            }
        ?>
    </ul>
    <h2>Message Body:</h2>
    <ul>
        <?php
            $keys = array_keys($_POST);
            foreach ($keys as $key) {
                print "<li><b>$key:</b> $_POST[$key]</li>";
            }
        ?>
    </ul>
</body>

</html>

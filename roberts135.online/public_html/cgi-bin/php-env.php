<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <link rel="icon" href="images/favicon.svg">
  <title>Hello, PHP HTML World!</title>
</head>

<body>
    <h1 align="center">Environment Variables</h1>
    <hr />
    <h2>Environment Variables:</h2>
    <ul>
        <?php
            $keys = array_keys($_ENV);
            foreach ($keys as $key) {
                print "<li><b>$key:</b> $_ENV[$key]</li>";
            }
        ?>
    </ul>
    <h2>Server Variables:</h2>
    <ul>
        <?php
            $keys = array_keys($_SERVER);
            foreach ($keys as $key) {
                print "<li><b>$key:</b> $_SERVER[$key]</li>";
            }
        ?>
    </ul>
</body>

</html>

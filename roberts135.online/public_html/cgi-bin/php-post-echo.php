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

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <link rel="icon" href="images/favicon.svg">
  <title>Hello, PHP HTML World!</title>
</head>

<body>
    <h1>Hello, PHP HTML World!</h1>
    <hr />
    <p>This page was generated with PHP.</p>
    <p>This program was run at: 
        <?php
            date_default_timezone_set('America/Los_Angeles');
            print date('m/y/d \a\t h:m:s', time());
        ?>
    </p>
    <p>Your current IP Address is: 
        <?php
            print $_SERVER['REMOTE_ADDR'];
        ?>
    </p>
</body>

</html>
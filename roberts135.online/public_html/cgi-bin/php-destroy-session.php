<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <link rel="icon" href="images/favicon.svg">
  <title>Hello, PHP HTML World!</title>
</head>

<body>
    <h1 align="center">Session Destroyed</h1>
    <hr>

    <?php
        session_name('LAST_ACTIVE');
        session_start();
        session_destroy();
    ?>
    <p><b>Name:</b></p>
    <a href="/php-cgiform.html">Back to CGI Form</a><br>
    <a href="/cgi-bin/php-sessions-1.php">Back to Session Page 1</a>
    <a href="/cgi-bin/php-sessions-2.php">Back to Session Page 2</a>

</body>

</html>

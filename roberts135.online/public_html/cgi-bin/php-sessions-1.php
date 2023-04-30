<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <link rel="icon" href="images/favicon.svg">
  <title>Hello, PHP HTML World!</title>
</head>

<body>
    <h1 align="center">PHP Sessions Page 1</h1>
    <hr>

    <p><b>Name:</b> 
        <?php
            session_name('LAST_ACTIVE');
            session_start();
            if($_POST['username']) {
                $_SESSION['username'] = $_POST['username'];
            }
            print $_SESSION['username'];
        ?>
    </p>
    <a href="/php-cgiform.html">CGI Form</a><br>
    <a href="/cgi-bin/php-sessions-2.php">Session Page 2</a>
    <form style="margin-top:30px" action="/cgi-bin/php-destroy-session.php" method="get">
        <button type="submit">Destroy Session</button>
    </form>

</body>

</html>

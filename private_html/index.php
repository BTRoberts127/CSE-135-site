<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <link rel="icon" href="images/favicon.svg">
  <title>Reporting for CSE 135</title>
  <script type="module">
    import '/zinggrid-master/index.js';
  </script>
  <!-- fallback for no module support -->
  <script nomodule src="/zinggrid-master/dist/zinggrid.min.js" defer></script>
  <script src="/zingchart/zingchart.min.js" defer></script>
  <style>
    body {
      display: grid;
      grid-template-areas: "heading report logout"
        "grid grid bar"
        "grid grid box";
      grid-template-columns: 25% 25% 50%;
      grid-template-rows: 5% 20% 15%;
    }
    a {
      text-decoration: none;
      color: white;
      background-color: blue;
      border-radius: 10px;
    }
    #uerrChart {
      grid-area: bar;
    }
    #loadTimeChart {
      grid-area: box;
    }
    zing-grid {
      grid-area: grid;
    }
  </style>
</head>

<body>
  <h1>General Dashboard</h1>

  <a id="logout" href="/logout.php">Logout</a>
  <a id="report" href="/report.php">Generate Report</a>

  <?php
    session_name('LAST_ACTIVE');
    session_start();
    if(array_key_exists('admin', $_SESSION) && $_SESSION['admin'] == 1) {
        print '<a id="manageUsers" href="/manageusers.php">User Management</a>';
    }
    $db_user = "root";
    $db_password = "qazwsxEDCRFV123!@#";
    $db_host = "localhost";
    $db_name = "analytics";
    $dbc = mysqli_connect($db_host, $db_user, $db_password);
    if(!$dbc) {
        die("Could not open db");
    }
    mysqli_select_db($dbc, $db_name);
    $uerr_query = 'SELECT REGEXP_REPLACE(user_agent, "[;].+ ", ") ") AS "text", AVG(LENGTH(REGEXP_REPLACE(REGEXP_REPLACE(activity, "^e~|[|]e~", "`"), "[^`]", ""))) AS "value" FROM activity INNER JOIN performance ON activity.id = performance.id INNER JOIN static ON activity.id = static.id WHERE start_load > ' . (time() - 604800) . '000 GROUP BY user_agent;';
    $uerr_result = mysqli_query($dbc, $uerr_query);
    $uerr_rarray = array();
    #https://stackoverflow.com/questions/383631/json-encode-mysql-results
    while($key = mysqli_fetch_assoc($uerr_result)) {
      $uerr_rarray[] = $key;
    }
    print_ua_grid($dbc);
  ?>
  <div id="uerrChart"></div>
  <div id="loadTimeChart"></div>

</body>

<pre>
    Note: I added the two charts after the deadline.

    I chose to add the barchart of error rate by browser because I wanted to know
    which user agents my site not support properly. I chose a barchart to quickly
    visualize if any categories had uncharacteristically high error rates, which
    I could then investigate in more detail and run my own testing on to determine
    the problem. I chose to limit the data to the last week to allow me to look for
    new errors, and hopefully fix them, as they occur.

    I chose to add the box and whisker plot of performance over the last week as a
    way to quickly check the health of the page. Like the previous chart, it is designed
    to act as a fast way of determining if I need to check more closely in the records
    in case of abnormally high load times. I specifically chose a box and whisker plot
    because I was interested in quickly seeing what most users were experiencing for load
    times (approximately, the third quartile) as well as the worst-case load times (the
    maximum). The box and whisker plot gives a familiar and easy-to-read way of presenting
    this, since it encodes the information as a relatively easy-to-understand position along
    a scale.
  </pre>

</html>
<?php
  function print_ua_grid($dbc) {
    $query = 'SELECT REGEXP_REPLACE(user_agent, "[)].+ ", ") ") AS user_agent, count(user_agent) AS count, CONCAT(avg(load_time), " ms") AS avg_load_time, CONCAT(FORMAT(avg(cookies)*100, 0), "%") AS "%_allowing_cookies", CONCAT(FORMAT(avg(images)*100, 0), "%") AS "%_allowing_images", CONCAT(FORMAT(avg(css)*100, 0), "%") AS "%_allowing_css" FROM static INNER JOIN performance ON static.id = performance.id GROUP BY user_agent;';
    $result = mysqli_query($dbc, $query);
    #https://stackoverflow.com/questions/383631/json-encode-mysql-results
    $rarray = array();
    while($key = mysqli_fetch_assoc($result)) {
        $rarray[] = $key;
    }
    print '<zing-grid caption="User Agents" sort search pager page-size="15" page-size-options="5,15,30" layout="row" viewport-stop data=\'';
    print json_encode($rarray);
    print '\'></zing-grid>';
  }
  function print_uerr_chart_data($uerr_rarray) {
    #https://stackoverflow.com/questions/383631/json-encode-mysql-results
    $rarray = array();
    foreach($uerr_rarray as $element) {
      $rarray[] = $element['value'] + 0;
    }
    print json_encode($rarray);
  }
  function print_uerr_chart_labels($uerr_rarray) {
    #https://stackoverflow.com/questions/383631/json-encode-mysql-results
    $rarray = array();
    foreach($uerr_rarray as $element) {
      $rarray[] = $element['text'];
    }
    print json_encode($rarray);
  }
  function printBoxData($dbc) {
    $query = 'SELECT load_time FROM performance WHERE start_load > ' . (time() - 604800) . '000;';
    $result = mysqli_query($dbc, $query);
    #https://stackoverflow.com/questions/383631/json-encode-mysql-results
    $rarray = array();
    while($key = mysqli_fetch_assoc($result)) {
        $rarray[] = $key['load_time'] + 0;
    }
    sort($rarray);
    $count = count($rarray);
    $data = array();
    $data[] = $rarray[0];
    $data[] = ($rarray[floor(($count + 0)/4)] + $rarray[floor(($count + 1)/4)] + $rarray[floor(($count + 2)/4)] + $rarray[floor(($count + 3)/4)])/4;
    $data[] = ($rarray[floor(($count + 0)/2)] + $rarray[floor(($count + 1)/2)])/2;
    $data[] = ($rarray[floor(($count + 0)*3/4)] + $rarray[floor(($count + 1)*3/4)] + $rarray[floor(($count + 2)*3/4)] + $rarray[floor(($count + 3)*3/4)])/4;
    $data[] = $rarray[$count - 1];
    print json_encode($data);
  }
?>
<script>
  window.onload = renderCharts;
  function renderCharts() {
    let uerrConfig = {
  type: 'bar',
  title: {
    text: 'Error rate by user agent, past week',
    fontSize: 24,
  },
  legend: {
    draggable: true,
  },
  scaleX: {
    // Set scale label
    label: { text: 'User agent' },
    labels: <?php print_uerr_chart_labels($uerr_rarray); ?>
  },
  scaleY: {
    // Set scale label
    label: { text: 'Average errors per session' }
  },
  series: [{text: 'Error rate', values: <?php print_uerr_chart_data($uerr_rarray); ?>}]
};
let boxConfig = {
  type: 'hboxplot',
  title: {
    text: 'Load times summary, past week',
    fontSize: 24,
  },
  series: [{"data-box": [<?php printBoxData($dbc); ?>]}]
};
  zingchart.render({
  id: 'uerrChart',
  data: uerrConfig
  });
  zingchart.render({
  id: 'loadTimeChart',
  data: boxConfig
  });
  }
</script>
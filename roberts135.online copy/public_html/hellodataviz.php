<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <link rel="icon" href="images/favicon.svg">
  <title>Hello Data Viz</title>
  <script nonce="undefined" src="https://cdn.zingchart.com/zingchart.min.js"></script>
  <style>
  
 #myChart, #pieChart, #lineChart {
   width: 100%;
   height: 100%;
   min-height: 250px;
 }
  
 .zc-ref {
   display: none;
 }
  </style>
</head>

<body>
  <h1>Hello Data Viz</h1>
  <div id='lineChart'><a class="zc-ref" href="https://www.zingchart.com/">Charts by ZingChart</a></div>
  <div id='pieChart'><a class="zc-ref" href="https://www.zingchart.com/">Charts by ZingChart</a></div>
  <div id='myChart'><a class="zc-ref" href="https://www.zingchart.com/">Charts by ZingChart</a></div>
</body>

</html>

<?php
    $db_user = "root";
    $db_password = "qazwsxEDCRFV123!@#";
    $db_host = "localhost";
    $db_name = "analytics";
    $dbc = mysqli_connect($db_host, $db_user, $db_password);
    if(!$dbc) {
        die("Could not open db");
    }
    mysqli_select_db($dbc, $db_name);
?>

<script>
  window.onload = renderCharts;
  function renderCharts () {
    renderPie();
    renderLine();
    renderBar();
  }
  function renderBar() {
    //adapted from https://www.zingchart.com/docs/chart-types/bar
    let myConfig = {
  "graphset": [{
    "type": "bar",
    "background-color": "white",
    "title": {
      "text": "Enabled features by browser",
      "font-color": "#7E7E7E",
      "backgroundColor": "none",
      "font-size": "22px",
      "alpha": 1,
      "adjust-layout": true,
    },
    "plotarea": {
      "margin": "dynamic"
    },
    "legend": {
      "layout": "x3",
      "overflow": "page",
      "alpha": 0.05,
      "shadow": false,
      "align": "center",
      "adjust-layout": true,
      "marker": {
        "type": "circle",
        "border-color": "none",
        "size": "10px"
      },
      "border-width": 0,
      "toggle-action": "hide"
    },
    "plot": {
      "bars-space-left": 0.15,
      "bars-space-right": 0.15
    },
    "scale-y": {
      "line-color": "#7E7E7E",
      "item": {
        "font-color": "#7e7e7e"
      },
      "values": "0:100",
      "guide": {
        "visible": true
      },
      "label": {
        "text": "% enabled",
        "font-family": "arial",
        "bold": true,
        "font-size": "14px",
        "font-color": "#7E7E7E",
      },
    },
    "scaleX": {
      "values": <?php printBarScale($dbc) ?>,
      "placement": "default",
      "tick": {
        "size": 58,
        "placement": "cross"
      },
      "itemsOverlap": true,
      "item": {
        "offsetY": -55
      }
    },
    "tooltip": {
      "visible": false
    },
    "crosshair-x": {
      "line-width": "100%",
      "alpha": 0.18,
      "plot-label": {
        "header-text": "%kv Sales"
      }
    },
    "series": [{
        "values": <?php printBarCookies($dbc) ?>,
        "alpha": 0.95,
        "background-color": "purple",
        "text": "Cookies",
      },
      {
        "values": <?php printBarImages($dbc) ?>,
        "alpha": 0.95,
        "background-color": "orange",
        "text": "Images"
      }
        ]
  }]
};
 
zingchart.render({
  id: 'myChart',
  data: myConfig,
  height: '100%',
  width: '100%'
});
  }
  function renderLine() {
    //adapted from https://www.zingchart.com/docs/chart-types/line
    let lineConfig = {
  "type": "line",
  "utc": true,
  "title": {
    "text": "Webpage Activity over Time",
    "font-size": "24px",
    "adjust-layout": true
  },
  "plotarea": {
    "margin": "dynamic 45 60 dynamic",
  },
  "legend": {
    "layout": "float",
    "background-color": "none",
    "border-width": 0,
    "shadow": 0,
    "align": "center",
    "adjust-layout": true,
    "toggle-action": "remove",
    "item": {
      "padding": 7,
      "marginRight": 17,
      "cursor": "hand"
    }
  },
  "scale-x": {
    labels: <?php printLineDataScale($dbc) ?>,
    "transform": {
      "type": "date",
      "all": "%D, %d %M<br />%h:%i %A",
      "item": {
        "visible": false
      }
    },
    "label": {
      "visible": false
    },
    "minor-ticks": 0
  },
  "crosshair-x": {
    "line-color": "#efefef",
    "plot-label": {
      "border-radius": "5px",
      "border-width": "1px",
      "border-color": "#f6f7f8",
      "padding": "10px",
      "font-weight": "bold"
    },
    "scale-label": {
      "font-color": "#000",
      "background-color": "#f6f7f8",
      "border-radius": "5px"
    }
  },
  "tooltip": {
    "visible": false
  },
  "plot": {
    "highlight": true,
    "tooltip-text": "%t views: %v<br>%k",
    "shadow": 0,
    "line-width": "2px",
    "marker": {
      "type": "circle",
      "size": 3
    },
    "highlight-state": {
      "line-width": 3
    }
  },
  "series": [{
      "values": <?php printLineDataPerformance($dbc) ?>,
      "text": "Load time (ms*10)",
      "line-color": "#007790",
      "legend-item": {
        "background-color": "#007790",
        "borderRadius": 5,
        "font-color": "white"
      },
      "legend-marker": {
        "visible": false
      },
      "marker": {
        "background-color": "#007790",
        "border-width": 1,
        "shadow": 0,
        "border-color": "#69dbf1"
      },
      "highlight-marker": {
        "size": 6,
        "background-color": "#007790",
      }
    },
    {
      "values": <?php printLineDataActivity($dbc) ?>,
      "text": "Activity (activities logged)",
      "line-color": "#009872",
      "legend-item": {
        "background-color": "#009872",
        "borderRadius": 5,
        "font-color": "white"
      },
      "legend-marker": {
        "visible": false
      },
      "marker": {
        "background-color": "#009872",
        "border-width": 1,
        "shadow": 0,
        "border-color": "#69f2d0"
      },
      "highlight-marker": {
        "size": 6,
        "background-color": "#009872",
      }
    },
    {
      "values": <?php printLineDataSize($dbc) ?>,
      "text": "Device size (screen width/20)",
      "line-color": "#da534d",
      "legend-item": {
        "background-color": "#da534d",
        "borderRadius": 5,
        "font-color": "white"
      },
      "legend-marker": {
        "visible": false
      },
      "marker": {
        "background-color": "#da534d",
        "border-width": 1,
        "shadow": 0,
        "border-color": "#faa39f"
      },
      "highlight-marker": {
        "size": 6,
        "background-color": "#da534d",
      }
    }
  ]
};
 
zingchart.render({
  id: 'lineChart',
  data: lineConfig,
  height: '100%',
  width: '100%'
});
  }
  //adapted from https://www.zingchart.com/docs/chart-types/pie
  function renderPie() {
    let onRate = <?php printPieData($dbc); ?>;
    let pieConfig = {
  type: "pie",
  plot: {
    valueBox: {
      placement: 'out',
      text: '%t\n%npv%',
      fontFamily: "sans-serif"
    },
    tooltip: {
      fontSize: '18',
      fontFamily: "sans-serif",
      padding: "5 10",
      text: "%npv%"
    },
  },
  title: {
    fontColor: "#8e99a9",
    text: 'Java Script Enable Rate',
    align: "left",
    offsetX: 10,
    fontFamily: "sans-serif",
    fontSize: 25
  },
  plotarea: {
    margin: "20 0 0 0"
  },
  series: [
    {
      values: [1 - onRate],
      text: "Off",
      backgroundColor: '#FF7965'
    },
    {
      text: 'On',
      values: [onRate],
      backgroundColor: '#6FB07F'
    }
  ]
};
 
zingchart.render({
  id: 'pieChart',
  data: pieConfig,
  height: '100%',
  width: '100%'
});
  }
</script>

<?php
  function printPieData($dbc) {
    $query = 'SELECT avg(js) AS js FROM static;';
    print mysqli_fetch_assoc(mysqli_query($dbc, $query))['js'];
  }
  function printLineDataActivity($dbc) {
    $query = 'SELECT LENGTH(REGEXP_REPLACE(activity, "[^|]", "")) AS activity_count FROM activity INNER JOIN performance ON activity.id = performance.id ORDER BY start_load;';
    #https://stackoverflow.com/questions/383631/json-encode-mysql-results
    $result = mysqli_query($dbc, $query);
    $rarray = array();
    while($key = mysqli_fetch_assoc($result)) {
        $rarray[] = $key['activity_count'] + 0;
    }
    print json_encode($rarray);
  }
  function printLineDataPerformance($dbc) {
    $query = 'SELECT load_time FROM activity INNER JOIN performance ON activity.id = performance.id ORDER BY start_load;';
    #https://stackoverflow.com/questions/383631/json-encode-mysql-results
    $result = mysqli_query($dbc, $query);
    $rarray = array();
    while($key = mysqli_fetch_assoc($result)) {
        $rarray[] = $key['load_time'] * 10;
    }
    print json_encode($rarray);
  }
  function printLineDataSize($dbc) {
    $query = 'SELECT screen_width FROM static INNER JOIN performance ON static.id = performance.id INNER JOIN activity ON static.id = activity.id ORDER BY start_load;';
    #https://stackoverflow.com/questions/383631/json-encode-mysql-results
    $result = mysqli_query($dbc, $query);
    $rarray = array();
    while($key = mysqli_fetch_assoc($result)) {
        $rarray[] = $key['screen_width'] / 20;
    }
    print json_encode($rarray);
  }
  function printLineDataScale($dbc) {
    $query = 'SELECT start_load FROM activity INNER JOIN performance ON activity.id = performance.id ORDER BY start_load;';
    #https://stackoverflow.com/questions/383631/json-encode-mysql-results
    $result = mysqli_query($dbc, $query);
    $rarray = array();
    while($key = mysqli_fetch_assoc($result)) {
        $rarray[] = $key['start_load'] + 0;
    }
    print json_encode($rarray);
  }
  function printBarScale($dbc) {
    $query = 'SELECT MAX(REGEXP_REPLACE(user_agent, "[)].+ ", ") ")) AS user_agent FROM static GROUP BY user_agent;';
    #https://stackoverflow.com/questions/383631/json-encode-mysql-results
    $result = mysqli_query($dbc, $query);
    $rarray = array();
    while($key = mysqli_fetch_assoc($result)) {
        $rarray[] = $key['user_agent'];
    }
    print json_encode($rarray);
  }
  function printBarCookies($dbc) {
    $query = 'SELECT AVG(cookies) AS cookies FROM static GROUP BY user_agent;';
    #https://stackoverflow.com/questions/383631/json-encode-mysql-results
    $result = mysqli_query($dbc, $query);
    $rarray = array();
    while($key = mysqli_fetch_assoc($result)) {
        $rarray[] = $key['cookies'] * 100;
    }
    print json_encode($rarray);
  }
  function printBarImages($dbc) {
    $query = 'SELECT AVG(images) AS images FROM static GROUP BY user_agent;';
    #https://stackoverflow.com/questions/383631/json-encode-mysql-results
    $result = mysqli_query($dbc, $query);
    $rarray = array();
    while($key = mysqli_fetch_assoc($result)) {
        $rarray[] = $key['images'] * 100;
    }
    print json_encode($rarray);
  }
?>
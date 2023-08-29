<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <link rel="icon" href="images/favicon.svg">
  <title>Detailed report</title>
  <script type="module">
    import '/zinggrid-master/index.js';
  </script>
  <!-- fallback for no module support -->
  <script nomodule src="/zinggrid-master/dist/zinggrid.min.js" defer></script>
  <script src="https://cdn.zingchart.com/zingchart.min.js"></script>
  <style>
    .zc-body {
      background-color: #fff;
    }

    .zc-grid {
      margin: 0 auto;
      padding: 1rem;
      width: 100%;
      max-width: 1400px;
      box-sizing: border-box;
      background: #fff;
    }

    .zc-chart {
      height: 500px;
      width: 650px;
    }

    .zc-ref {
      display: none;
    }

    /* TABLET: PORTRAIT+ */
    @media screen and (min-width:768px) {
      .zc-grid {
        display: grid;
        grid-template-columns: 500px 1fr;
        grid-column-gap: 2rem;
      }
    }
  </style>
</head>

<body>
  <h1>Detailed report on performance</h1>

</body>

<body class="zc-body">

  <div class="zc-grid">

    <div id="zcmap" class="zc-chart">
      <a href="https://www.zingchart.com/" rel="noopener" class="zc-ref">Powered by ZingChart</a>
    </div>
    <div id="zcline" class="zc-chart"></div>

  </div>
<script>
//Adapted from https://www.zingchart.com/gallery/distribution-map-scatter-plot
//load data
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
    printBubbleChart($dbc);
  ?>
  data = JSON.parse(data);
  let max = 0;
  for (let i = 0; i < data.length; i++) {
    if(data[i].loadTime > max) {
      max = data[i].loadTime;
    }
  }
    ZC.LICENSE = ["569d52cefae586f634c54f86dc99e6a9", "b55b025e438fa8a98e32482b5f768ff5"];
// INIT
// -----------------------------
// Define Module Location
zingchart.MODULESDIR = "https://cdn.zingchart.com/modules/";
// Load Maps
zingchart.loadModules('maps, maps-world-continents', render);
let gmap = {
  type: 'null',
  height: '100%',
  width: '100%',
  x: '10px',
  y: 0,
  legend: {
    borderWidth: 0,
    header: {
      text: 'Usage and response time by location',
      align: 'left',
      fontSize: '11px'
    },
    layout: '1x',
    margin: 'auto auto 10px 10px',
    toggleAction: 'none'
  },
  tooltip: {
    align: 'left',
    borderRadius: '3px',
    callout: true,
    calloutWidth: '16px',
    calloutHeight: '8px',
    fontSize: '13px',
    fontWeight: 'bold',
    padding: '10px 20px',
    shadow: true,
    shadowAlpha: 0.7,
    shadowColor: '#333',
    shadowDistance: 2
  },
  shapes: [
    {
      type: 'zingchart.maps',
      options: {
        name: 'world.continents',
        scale: true,
        style: {
          borderAlpha: 0.1,
          borderColor: '#666',
          hoverState: {
            backgroundColor: 'none',
            shadow: true,
            shadowAlpha: 0.1,
            shadowColor: '#369',
            shadowDistance: 0
          },
          label: {
            visible: false
          }
        }
      }
    }
  ],
  series: [
    {
      text: 'Response time 1ms',
      legendMarker: {
        type: 'circle',
        backgroundColor: '#00ff00',
        size: '5px'
      },
    },
    {
      text: 'Response time ' + max + 'ms',
      legendMarker: {
        type: 'circle',
        backgroundColor: '#ff0000',
        size: '5px'
      },
    },
    {
      text: '1 hit',
      legendMarker: {
        type: 'circle',
        backgroundColor: '#26596A',
        size: '2px'
      },
    },
    {
      text: '25 hits',
      legendMarker: {
        type: 'circle',
        backgroundColor: '#26596A',
        size: '10px'
      },
    }

  ]
};

// DEFINE CHART LOCATIONS (IDS)
// -----------------------------
// Main chart render location(s)
let map = 'zcmap';
  
  
  for (let i = 0; i < data.length; i++) {
    let coords = data[i].coords.split(',');
    gmap.shapes.push({
        id: 'datapoint' + i,
        type: 'circle',
        size: 2 * Math.sqrt(data[i].count),
        backgroundColor: numToColor(data[i].loadTime, max),
        y: 390 - (parseInt(coords[0]) + 90) * (5/3),
        x: 25 + (parseInt(coords[1]) + 180) * (625/360),
        map: 'maps-world-continents',
        hoverState: {
          backgroundColor: 'none',
          borderColor: numToColor(data[i].loadTime, max),
          borderWidth: 2,
          size: Math.sqrt(data[i].count) + 4,
        },
        tooltip: {
          text: data[i].coords
        }
      });
  }

  function numToColor(num, max) {
    let red = Math.round(num/max * 255);
    let green = 255 - red;
    return `rgb(${red}, ${green}, 0)`;
  }
    zingchart.DEV.RESOURCES = false;

  function render() {
  zingchart.render({
    id: map,
    width: '100%',
    height: '100%',
    output: 'svg',
    data: gmap
  });
}
</script>
</body>
</html>

<?php
print_grid($dbc);
    function printBubbleChart($dbc) {
      $query = 'SELECT coords, count(static.ip) AS count, avg(load_time) AS loadTime FROM static INNER JOIN performance ON static.id = performance.id INNER JOIN locations ON static.ip = locations.ip GROUP BY coords;';
      $result = mysqli_query($dbc, $query);
      #https://stackoverflow.com/questions/383631/json-encode-mysql-results
      $rarray = array();
      while($key = mysqli_fetch_assoc($result)) {
          $rarray[] = $key;
      }
      print 'let data = \'' . json_encode($rarray) . '\';';
    }
    function print_grid($dbc) {
      $query = 'SELECT coords, count(static.ip) AS count, avg(load_time) AS loadTime FROM static INNER JOIN performance ON static.id = performance.id INNER JOIN locations ON static.ip = locations.ip GROUP BY coords;';
      $result = mysqli_query($dbc, $query);
      #https://stackoverflow.com/questions/383631/json-encode-mysql-results
      $rarray = array();
      while($key = mysqli_fetch_assoc($result)) {
          $rarray[] = $key;
      }
      print '<zing-grid caption="Locations and load times" sort search pager page-size="15" page-size-options="5,15,30" layout="row" viewport-stop data=\'';
      print json_encode($rarray);
      print '\'></zing-grid>';
    }
  ?>
<!DOCTYPE html>
<html lang="en">

<head>
    
    <meta charset="utf-8" />
    <link rel="apple-touch-icon" sizes="76x76" href="assets/img/icon.png">
    <link rel="icon" type="image/png" href="assets/img/icon.png">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
    <title>stream_tracker</title>
    <meta content='width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0, shrink-to-fit=no' name='viewport' />
    <!--     Fonts and icons     -->
    <link href="https://fonts.googleapis.com/css?family=Montserrat:400,700,200" rel="stylesheet" />
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/latest/css/font-awesome.min.css" />
    <!-- CSS Files -->
    <link href="assets/css/bootstrap.min.css" rel="stylesheet" />
    <link href="assets/css/light-bootstrap-dashboard.css?v=2.0.0 " rel="stylesheet" />
    <!-- CSS Just for demo purpose, don't include it in your project -->
    <link href="assets/css/demo.css" rel="stylesheet" />
    <script src="https://d3js.org/d3.v6.js"></script>
</head>

<body style="background-color: burlywood;">
    <div class="wrapper">
        <div class="sidebar" data-image="assets/img/sidebar-4.jpg" data-color="blue">
            <!--
        Tip 1: You can change the color of the sidebar using: data-color="purple | blue | green | orange | red"

        Tip 2: you can also add an image using data-image tag
    -->
            <div class="sidebar-wrapper">
                <div class="logo">
                    <div class="simple-text" style="font-size: 25px; font-weight: 700; font-family: cursive; font-style: italic; color: gold;">
                        Stream Tracker
                    </div>
                </div>
                <ul class="nav">
                    <li>
                        <a class="nav-link" href="index.php">
                            <i class="nc-icon nc-icon nc-bulb-63"></i>
                            <p>Relation</p>
                        </a>
                    </li>
                    <li>
                        <a class="nav-link" href="second.php">
                            <i class="nc-icon nc-bullet-list-67"></i>
                            <p>Ranking Relation</p>
                        </a>
                    </li>
                    <li>
                        <a class="nav-link" href="third.php">
                            <i class="nc-icon nc-map-big"></i>
                            <p>Movie Distribution</p>
                        </a>
                    </li>
                    <li class="nav-item active">
                        <a class="nav-link" href="fourth.php">
                            <i class="nc-icon nc-chart-bar-32"></i>
                            <p>Score Distribution</p>
                        </a>
                    </li>
                </ul>
            </div>
        </div>
        <div class="main-panel">
            <div class="content">
                <div class="container-fluid">
                    <div class="section">
                        <div style="font-size: 20px; font-weight: bold;">The score distribution on each platform</div>
                        <!-- Add 2 buttons -->
                        <button type="button" class="btn btn-outline-success" style="border-color: #28a745; color: #28a745;"onclick="update('imdb_score')">IMDB_Score</button>
                        <button type="button" class="btn btn-outline-info"  style="border-color: #17a2b8; color: #17a2b8;"onclick="update('tmdb_score')">TMDB_Score</button> 
                        <!-- Create a div where the graph will take place -->
                        <div id="my_dataviz"></div> 
                    </div>
                </div>
            </div>
        </div>
    </div>
   
</body>
<script>
    // set the dimensions and margins of the graph
    const margin = {top: 10, right: 30, bottom: 20, left: 40},
        width = 500 - margin.left - margin.right,
        height = 440 - margin.top - margin.bottom;
    
    // append the svg object to the body of the page
    const svg = d3.select("#my_dataviz")
      .append("svg")
        .attr("width", width + margin.left + margin.right)
        .attr("height", height + margin.top + margin.bottom)
      .append("g")
        .attr("transform",
              `translate(${margin.left},${margin.top})`);
    
    // X axis: scale and draw:
    const x = d3.scaleLinear()
    .domain([0,10])     // can use this instead of 1000 to have the max of data: d3.max(data, function(d) { return +d.price })
    .range([0, width]);

        // Y axis: scale and draw:
      const y = d3.scaleLinear()
      .range([height, 0]);
      y.domain([0, 2000]);   // d3.hist has to be called before the Y axis obviously
  svg.append("g")
      .call(d3.axisLeft(y));

    
     // A function that create / update the plot for a given variable:
    function update(selectedplatform) {
      d3.csv("data.csv").then( function(data) {
    

    svg.append("g")
        .attr("transform", `translate(0, ${height})`)
        .call(d3.axisBottom(x));
    
    // set the parameters for the histogram
    const histogram = d3.histogram()
        .value(function(d) { return +d[selectedplatform];})   // I need to give the vector of value
        .domain(x.domain())  // then the domain of the graphic
        .thresholds(x.ticks(10)); // then the numbers of bins
  
    // And apply twice this function to data to get the bins.
    const bins1 = histogram(data.filter( function(d){return d.platform === 'd';}));
    const bins2 = histogram(data.filter( function(d){return d.platform === 'n';}));


  svg.selectAll("rect")
      .data(bins1)
      .join("rect")
      .transition()
      .duration(1000)
        .attr("x", 1)
        .attr("transform", function(d) { return `translate(${x(d.x0)} , ${y(d.length)})`})
        .attr("width", function(d) { return x(d.x1) - x(d.x0) -1; })
        .attr("height", function(d) { return height - y(d.length); })
        .style("fill", "#69b3a2")
        .style("opacity", 0.6)
        

  // append the bars for series 2
  svg.selectAll("rect2")
      .data(bins2)
      .enter()
      .append("rect")
      .transition()
      .duration(1000)
        .attr("x", 1)
        .attr("transform", function(d) { return `translate(${x(d.x0)}, ${y(d.length)})`})
        .attr("width", function(d) { return x(d.x1) - x(d.x0) -1 ; })
        .attr("height", function(d) { return height - y(d.length); })
        .style("fill", "#404080")
        .style("opacity", 0.6)

        // Handmade legend
        svg.append("circle").attr("cx",350).attr("cy",30).attr("r", 6).style("fill", "#69b3a2")
        svg.append("circle").attr("cx",350).attr("cy",60).attr("r", 6).style("fill", "#404080")
        svg.append("text").attr("x", 370).attr("y", 30).text("Disney+").style("font-size", "15px").attr("alignment-baseline","middle")
        svg.append("text").attr("x", 370).attr("y", 60).text("Netflix").style("font-size", "15px").attr("alignment-baseline","middle")
      }
    )};
    // Initialize plot
    update('imdb_score');
</script>
<!--   Core JS Files   -->
<!DOCTYPE html>
<meta charset="utf-8">

<!-- Load d3.js -->
<script src="https://d3js.org/d3.v6.js"></script>

<!-- Add 2 buttons -->
<button onclick="update('imdb_score')">imdb_score</button>
<button onclick="update('tmdb_score')">tmdb_score</button> 

<!-- Create a div where the graph will take place -->
<div id="my_dataviz"></div>


<script src="assets/js/core/jquery.3.2.1.min.js" type="text/javascript"></script>
<script src="assets/js/core/popper.min.js" type="text/javascript"></script>
<script src="assets/js/core/bootstrap.min.js" type="text/javascript"></script>
<!--  Plugin for Switches, full documentation here: http://www.jque.re/plugins/version3/bootstrap.switch/ -->
<script src="assets/js/plugins/bootstrap-switch.js"></script>
<!--  Google Maps Plugin    -->
<script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?key=YOUR_KEY_HERE"></script>
<!--  Chartist Plugin  -->
<script src="assets/js/plugins/chartist.min.js"></script>
<!--  Notifications Plugin    -->
<script src="assets/js/plugins/bootstrap-notify.js"></script>
<!-- Control Center for Light Bootstrap Dashboard: scripts for the example pages etc -->
<script src="assets/js/light-bootstrap-dashboard.js?v=2.0.0 " type="text/javascript"></script>
<!-- Light Bootstrap Dashboard DEMO methods, don't include it in your project! -->
<script src="assets/js/demo.js"></script>

</html>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <link rel="apple-touch-icon" sizes="76x76" href="../assets/img/apple-icon.png">
    <link rel="icon" type="image/png" href="../assets/img/favicon.ico">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
    <title>test</title>
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

<body>
    <div class="wrapper">
        <div class="sidebar" data-image="../assets/img/sidebar-5.jpg">
            <!--
        Tip 1: You can change the color of the sidebar using: data-color="purple | blue | green | orange | red"

        Tip 2: you can also add an image using data-image tag
    -->
            <div class="sidebar-wrapper">
                <div class="logo">
                    <a href="javascript:;" class="simple-text">
                      Your Logo
                    </a>
                </div>
                <ul class="nav">
                    <li>
                        <a class="nav-link" href="index.php">
                            <i class="nc-icon nc-icon nc-paper-2"></i>
                            <p>First picture</p>
                        </a>
                    </li>
                    <li>
                        <a class="nav-link" href="second.php">
                            <i class="nc-icon nc-bell-55"></i>
                            <p>Second picture</p>
                        </a>
                    </li>
                    <li class="nav-item active">
                        <a class="nav-link" href="third.php">
                            <i class="nc-icon nc-bell-55"></i>
                            <p>Third picture</p>
                        </a>
                    </li>
                    <li>
                        <a class="nav-link" href="fourth.php">
                            <i class="nc-icon nc-bell-55"></i>
                            <p>Fourth picture</p>
                        </a>
                    </li>
                </ul>
            </div>
        </div>
        <div class="main-panel">
            <div class="content">
                <div class="container-fluid">
                    <div class="section">
                        <label for="pet-select">Choose a platform:</label>
                        <select name="pets" id="pSelect", onchange="getValue();">
                            <option value="">--Please choose the platform</option>
                            <option value="n">Netflix</option>
                            <option value="d">Disney+</option>
                        </select>
                        <div class="d-flex justify-content-center" id="third"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
   
</body>

<script>
    // set the dimensions and margins of the graph
    const margin = { top: 20, right: 30, bottom: 60, left: 60 },
            width = 800 - margin.left - margin.right,
            height = 450 - margin.top - margin.bottom;

    function getValue() {
        let p = document.getElementById("pSelect").value
        if(p == "n"){
            document.getElementById('third').innerHTML = ""
            const svg = d3.select("#third")
                    .append("svg")
                    .attr("width", width + margin.left + margin.right)
                    .attr("height", height + margin.top + margin.bottom)
                    .append("g")
                    .attr("transform", `translate(${margin.left}, ${margin.top})`);
            
            // Map and projection
            const path = d3.geoPath();
            const projection = d3.geoMercator()
                            .scale(70)
                            .center([0,20])
                            .translate([width / 2, height / 2]);
            
            // Data and color scale
            const data = new Map();
            const colorScale = d3.scaleThreshold()
                            .domain([20, 40, 60, 100, 200, 1000])
                            .range(d3.schemeReds[7]);
            
            // Load external data and boot
            Promise.all([
                d3.json("https://raw.githubusercontent.com/holtzy/D3-graph-gallery/master/DATA/world.geojson"),
                d3.csv("countData.csv", function(d) {
                    if(d.platform == "n"){
                        data.set(d.code, +d.count)
                    }
                })]).then(function(loadData){
                    let topo = loadData[0]

                    let Tooltip = d3.select("#third")
                        .append("div")
                        .style("opacity", 0)
                        .attr("class", "tooltip")
                        .style("background-color", "white")
                        .style("border", "solid")
                        .style("border-width", "2px")
                        .style("border-radius", "5px")
                        .style("padding", "5px")

                    let mouseOver = function(event, d) {
                        let name = d.properties.name
                        let total = d.total
                        d3.select(this)
                            .transition()
                            .duration(200)
                            .style("opacity", 1)
                            .style("stroke", "black")
                        Tooltip
                            .style("opacity", 1)
                            .html("Name: " + name + "<br>total: " + total)
                            .style("top", (event.clientY) + "px")
                    }

                    let mouseLeave = function(d) {
                        d3.select(this)
                            .transition()
                            .duration(200)
                            .style("opacity", .5)
                            .style("stroke", "transparent")
                        Tooltip
                            .style("opacity", 0)
                    }

                    // Draw the map
                    svg.append("g")
                        .selectAll("path")
                        .data(topo.features)
                        .enter()
                        .append("path")
                        // draw each country
                        .attr("d", d3.geoPath()
                            .projection(projection)
                        )
                        // set the color of each country
                        .attr("fill", function (d) {
                            d.total = data.get(d.id) || 0;
                            return colorScale(d.total);
                        })
                        .style("stroke", "transparent")
                        .attr("class", function(d){ return "Country" } )
                        .style("opacity", .5)
                        .on("mouseover", mouseOver )
                        .on("mouseleave", mouseLeave )

                    svg.append("text")
                        .attr("x", (width / 2))             
                        .attr("y", margin.height)
                        .attr("text-anchor", "middle")  
                        .style("font-size", "20px") 
                        .text("The movie distribution in Netflix");
                })
        }
        else{
            document.getElementById('third').innerHTML = ""
            // The svg
            const svg = d3.select("#third")
                    .append("svg")
                    .attr("width", width + margin.left + margin.right)
                    .attr("height", height + margin.top + margin.bottom)
                    .append("g")
                    .attr("transform", `translate(${margin.left}, ${margin.top})`);
            
            // Map and projection
            const path = d3.geoPath();
            const projection = d3.geoMercator()
                            .scale(70)
                            .center([0,20])
                            .translate([width / 2, height / 2]);
            
            // Data and color scale
            const data = new Map();
            const colorScale = d3.scaleThreshold()
                            .domain([0, 5, 10, 40, 1000])
                            .range(d3.schemeBlues[6]);
            
            // Load external data and boot
            Promise.all([
                d3.json("https://raw.githubusercontent.com/holtzy/D3-graph-gallery/master/DATA/world.geojson"),
                d3.csv("countData.csv", function(d) {
                    if(d.platform == "d"){
                        data.set(d.code, +d.count)
                    }
                })]).then(function(loadData){
                    let topo = loadData[0]
                    let Tooltip = d3.select("#third")
                        .append("div")
                        .style("opacity", 0)
                        .attr("class", "tooltip")
                        .style("background-color", "white")
                        .style("border", "solid")
                        .style("border-width", "2px")
                        .style("border-radius", "5px")
                        .style("padding", "5px")

                    let mouseOver = function(event, d) {
                        let name = d.properties.name
                        let total = d.total
                        d3.select(this)
                            .transition()
                            .duration(200)
                            .style("opacity", 1)
                            .style("stroke", "black")
                        Tooltip
                            .style("opacity", 1)
                            .html("Name: " + name + "<br>total: " + total)
                            .style("top", (event.clientY) + "px")
                    }

                    let mouseLeave = function(d) {
                        d3.select(this)
                            .transition()
                            .duration(200)
                            .style("opacity", .5)
                            .style("stroke", "transparent")
                        Tooltip
                            .style("opacity", 0)
                    }

                    // Draw the map
                    svg.append("g")
                        .selectAll("path")
                        .data(topo.features)
                        .enter()
                        .append("path")
                        // draw each country
                        .attr("d", d3.geoPath()
                            .projection(projection)
                        )
                        // set the color of each country
                        .attr("fill", function (d) {
                            d.total = data.get(d.id) || 0;
                            return colorScale(d.total);
                        })
                        .style("stroke", "transparent")
                        .attr("class", function(d){ return "Country" } )
                        .style("opacity", .5)
                        .on("mouseover", mouseOver )
                        .on("mouseleave", mouseLeave )

                    svg.append("text")
                        .attr("x", (width / 2))             
                        .attr("y", margin.height)
                        .attr("text-anchor", "middle")  
                        .style("font-size", "20px") 
                        .text("The movie distribution in Disney+");
                    

                })
        }
    }        
    
</script>
<!--   Core JS Files   -->
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

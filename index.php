<!DOCTYPE html>
<meta charset="utf-8">

<!-- Load d3.js -->
<script src="https://d3js.org/d3.v6.js"></script>

<!-- Create a div where the graph will take place -->


<html lang="en">

<head>
    <meta charset="utf-8" />
    <link rel="apple-touch-icon" sizes="76x76" href="assets/img/icon.png">
    <link rel="icon" type="image/png" href="assets/img/icon.png">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
    <title>stream_tracker</title>
    <meta content='width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0, shrink-to-fit=no'
        name='viewport' />
    <!--     Fonts and icons     -->
    <link href="https://fonts.googleapis.com/css?family=Montserratetail:400,700,200" rel="stylesheet" />
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/latest/css/font-awesome.min.css" />
    <!-- CSS Files -->
    <link href="assets/css/bootstrap.min.css" rel="stylesheet" />
    <link href="assets/css/light-bootstrap-dashboard.css?v=2.0.0 " rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://d3js.org/d3.v6.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>


<style>

    #year-slider {
        position: relative;
        width: 300px;
        height: 10px;
        /* margin: 20px; */
    }

    .left-range,
    .right-range {
        -webkit-appearance: none;
        -moz-appearance: none;
        appearance: none;
        width: 100%;
        /* height: 10px;
        border-radius: 5px; */
        outline: none;
        position: absolute;
    }

    .left-range {
        left: 0;
    }

    .right-range {
        right: 0;
    }

    .left-range::-webkit-slider-thumb,
    .right-range::-webkit-slider-thumb {
        -webkit-appearance: none;
        appearance: none;
        /* width: 20px;
        height: 20px; */
        /* border-radius: 50%; */
        cursor: pointer;
        position: relative;
        z-index: 2;
    }

    .left-range::-moz-range-thumb,
    .right-range::-moz-range-thumb {
        /* width: 20px;
        height: 20px; */
        /* border-radius: 50%; */
        cursor: pointer;
        position: relative;
        z-index: 2;
    }

    #year-slider::before {
        content: '';
        position: absolute;
        top: 8px;
        left: 0px;
        width: 100%;
        height: 10px;
        border-radius: 50%;
        background: #fff;
        z-index: 1;
    }
</style>
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
                    <li class="nav-item active">
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
                    <li>
                        <a class="nav-link" href="fourth.php">
                            <i class="nc-icon nc-chart-bar-32"></i>
                            <p>Score Distribution</p>
                        </a>
                    </li>
                </ul>
            </div>
        </div>
        <div class="main-panel">
            
            <div class="p-3">
                <label id="slider-value" class="form-label" for="year-slider" data-toggle="tooltip"
                    data-placement="right" title="Filter movies based on the release year.">
                    Release year: 1980 - 2022</label>
                <div id="year-slider">
                    <input type="range" class="left-range form-range" min="1937" max="2022"
                        value="1980">
                    <input type="range" class="right-range form-range" min="1937" max="2022"
                        value="2022">
                </div>
            </div>
            <div id="my_dataviz"></div>
            </div>
    <script>

        // set the dimensions and margins of the graph
        const margin = {top: 30, right: 50, bottom: 10, left: 100},
        width = 660 - margin.left - margin.right,
        height = 600 - margin.top - margin.bottom;
        
        // append the svg object to the body of the page
        const svg = d3.select("#my_dataviz")
        .append("svg")
        .attr("width", width + margin.left + margin.right)
        .attr("height", height + margin.top + margin.bottom)
        .append("g")
        .attr("transform",
                `translate(${margin.left},${margin.top})`);
        
        // Parse the Data
        d3.csv("data.csv").then( function(data) {
            // console.log(data)
        
            
        // Here I set the list of dimension manually to control the order of axis:
        dimensions = ["type","release_year","imdb_score", "imdb_votes","platform"]
        const color = d3.scaleOrdinal()
                .domain(["n", "d"])
                .range(["#E50914", "#113CCF"])
        // For each dimension, I build a linear scale. I store all in a y object
        const y = {}

            name = dimensions[0]
            y[name] = d3.scaleOrdinal()
            .domain( "","" ) // --> Same axis range for each group
            // --> different axis range for each group --> .domain( [d3.extent(data, function(d) { return +d[name]; })] )
            .range([height, 0])
            name = dimensions[1]
            y[name] = d3.scaleLinear()
            .domain( d3.extent(data, function(d) { return +d[name]; } )) // --> Same axis range for each group
            // --> different axis range for each group --> .domain( [d3.extent(data, function(d) { return +d[name]; })] )
            .range([height, 0])
            name = dimensions[2]
            y[name] = d3.scaleLinear()
            .domain( d3.extent(data, function(d) { return +d[name]; } )) // --> Same axis range for each group
            // --> different axis range for each group --> .domain( [d3.extent(data, function(d) { return +d[name]; })] )
            .range([height, 0])
            name = dimensions[3]
            y[name] = d3.scaleLinear()
            .domain( d3.extent(data, function(d) { return +d[name]; } )) // --> Same axis range for each group
            // --> different axis range for each group --> .domain( [d3.extent(data, function(d) { return +d[name]; })] )
            .range([height, 0])
            name = dimensions[4]
            y[name] = d3.scaleOrdinal()
            .domain( "","" ) // --> Same axis range for each group
            // --> different axis range for each group --> .domain( [d3.extent(data, function(d) { return +d[name]; })] )
            .range([height, 0])
            
        
        
        
        // Build the X scale -> it find the best position for each Y axis
        x = d3.scalePoint()
            .range([0, width])
            .domain(dimensions);
        // Highlight the specie that is hovered
        const highlight = function(event, d){

            selected_specie = d.platform

            // first every group turns grey
            d3.selectAll(".line")
            .transition().duration(200)
            .style("stroke", "lightgrey")
            .style("opacity", "0")
            // Second the hovered specie takes its color
            d3.selectAll("." + selected_specie)
            .transition().duration(200)
            .style("stroke", color(selected_specie))
            .style("opacity", "1")
            }

            // Unhighlight
            const doNotHighlight = function(event, d){
            d3.selectAll(".line")
            .transition().duration(200).delay(1000)
            .style("stroke", function(d){ return( color(d.platform))} )
            .style("opacity", "1")
            }
        // The path function take a row of the csv as input, and return x and y coordinates of the line to draw for this raw.
        function path(d) {
            return d3.line()(dimensions.map(function(p) { return [x(p), y[p](d[p])]; }));
        }
        
        // Draw the lines
        svg
            .selectAll("myPath")
            .data(data)
            .join("path")
            .attr("class", function (d) { return "line " + d.platform } )
            .attr("d",  path)
            .style("fill", "none")
            .style("stroke", function (d) { return color(d.platform); })
            .style("opacity", 0.5)
            .on("mouseover", highlight)
            .on("mouseleave", doNotHighlight )
            
            
      

        // Draw the axis:
            svg.selectAll("myAxis")
                // For each dimension of the dataset I add a 'g' element:
                .data(dimensions).enter()
                .append("g")
                // I translate this element to its right position on the x axis
                .attr("transform", function(d) { return "translate(" + x(d) + ")"; })
                // And I build the axis with the call function
                .each(function(d) { d3.select(this).call(d3.axisLeft().scale(y[d])); })
                // Add axis title
                .append("text")
                .style("text-anchor", "middle")
                .attr("y", -9)
                .text(function(d) { return d; })
                .style("fill", "black")
            
            function updatePlot() {
                var leftValue = parseInt(d3.select('.left-range').property("value"));
                var rightValue = parseInt(d3.select('.right-range').property("value"));
                // var selectedValue = d3.select("#platform-select").property("value");

                var filteredData;
                filteredData = data.filter(function (d) {
                        return (parseInt(d.release_year) >= leftValue && parseInt(d.release_year) <= rightValue);
                    });



                var lines = svg.selectAll("path")
                    .data(filteredData)
                
                lines.remove();
                
                lines.enter()
                    .append("path")
                    .merge(lines)
                    .attr("class", function (d) { return "line " + d.platform } )
                    .attr("d",  path)
                    .style("fill", "none")
                    .style("stroke", function (d) { return color(d.platform); })
                    .style("opacity", 0.5)
                    .on("mouseover", highlight)
                    .on("mouseleave", doNotHighlight )
                    

            }
            // update the plot for the given range of release year
            d3.select('.left-range').on('input', updatePlot);
            d3.select('.right-range').on('input', updatePlot);

            $(function () {
            

                var leftRange = $('.left-range');
                var rightRange = $('.right-range');

                leftRange.on('input', handleRangeInput);
                rightRange.on('input', handleRangeInput);

                function handleRangeInput() {
                    var leftValue = parseInt(leftRange.val());
                    var rightValue = parseInt(rightRange.val());

                    if (leftValue >= rightValue) {
                        if ($(this).hasClass('left-range')) {
                            leftRange.val(rightValue);
                        } else {
                            rightRange.val(leftValue );
                        }
                    }

                }
                $('#year-slider input[type="range"]').on('input', function () {
                    var leftValue = leftRange.val();
                    var rightValue = rightRange.val();

                    $('#slider-value').text('Release year: ' + leftValue + ' - ' + rightValue);
                });

            });
        
})
</script>
</body>
<!--   Core JS Files   -->
<script src="assets/js/core/jquery.3.2.1.min.js" type="text/javascript"></script>
<script src="assets/js/core/popper.min.js" type="text/javascript"></script>
<script src="assets/js/core/bootstrap.min.js" type="text/javascript"></script>
<!--  Plugin for Switches, full documentation here: http://www.jque.re/plugins/version3/bootstrap.switch/ -->
<script src="assets/js/plugins/bootstrap-switch.js"></script>
<!--  Google Maps Plugin    -->
<!-- <script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?key=YOUR_KEY_HERE"></script> -->
<!--  Chartist Plugin  -->
<!-- <script src="../assets/js/plugins/chartist.min.js"></script> -->
<!--  Notifications Plugin    -->
<script src="assets/js/plugins/bootstrap-notify.js"></script>
<!-- Control Center for Light Bootstrap Dashboard: scripts for the example pages etc -->
<script src="assets/js/light-bootstrap-dashboard.js?v=2.0.0 " type="text/javascript"></script>
<!-- Light Bootstrap Dashboard DEMO methods, don't include it in your project! -->
<!-- <script src="../assets/js/demo.js"></script> -->

</html>
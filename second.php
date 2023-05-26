<!DOCTYPE html>
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
        .movie-info {
            position: absolute;
            padding: 8px;
            background-color: #fff;
            border: 1px solid #ccc;
            border-radius: 5px;
            pointer-events: none;
            opacity: 0;
            transition: opacity 0.3s;
            z-index: 3;
        }

        /* .form-range::-webkit-slider-runnable-track {
            background-color: #fff;
        } */

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
    <div>
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
                    <li class="nav-item active">
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
            <div class="content">
                <div class="container-fluid">
                    <div class="section w-75">
                        <div class="d-flex justify-content-between">
                            <div class="p-3">
                                <label for="xlimRange" class="form-label" data-toggle="tooltip" data-placement="right"
                                    title="Control the upper limit of IMDb votes on the X-axis.">IMDb Votes</label>
                                <input type="range" class="form-range" min="1000" max="2300000" step="100000"
                                    id="xlimRange" value="2300000">
                            </div>
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
                            <div class="p-3">
                                <label for="platform-select" class="form-label" data-toggle="tooltip"
                                    data-placement="right"
                                    title="Filter movies based on the platform they are available on.">
                                    Available on</label>
                                <select id="platform-select" class="form-select">
                                    <option selected>All</option>
                                    <option value="n">Netflix</option>
                                    <option value="d">Disney+</option>
                                </select>
                            </div>
                        </div>
                        <!-- Create a div where the graph will take place -->
                        <div id="second"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>

        // set the dimensions and margins of the graph
        const margin = { top: 20, right: 30, bottom: 60, left: 60 },
            width = 860 - margin.left - margin.right,
            height = 500 - margin.top - margin.bottom;

        // append the svg object to the body of the page
        const svg = d3.select("#second")
            .append("svg")
            .attr("width", width + margin.left + margin.right)
            .attr("height", height + margin.top + margin.bottom)
            .append("g")
            .attr("transform", `translate(${margin.left}, ${margin.top})`);

        //Read the data
        d3.csv("data.csv").then(function (data) {
            // Add X axis
            const x = d3.scaleLinear()
                .domain([0, 2300000])
                .range([0, width]);
            const xAxis = svg.append("g")
                .attr("transform", `translate(0, ${height})`)
                .call(d3.axisBottom(x));

            // Add Y axis
            const y = d3.scaleLinear()
                .domain([0, 10])
                .range([height, 0]);
            svg.append("g")
                .call(d3.axisLeft(y));

            // Add X axis label:
            svg.append("text")
                .attr("text-anchor", "end")
                .attr("x", width)
                .attr("y", height + margin.top + 30)
                .text("IMDb Votes");

            // Y axis label:
            svg.append("text")
                .attr("text-anchor", "end")
                .attr("transform", "rotate(-90)")
                .attr("y", -margin.left + 20)
                .attr("x", -margin.top + 20)
                .text("IMDb Ratings")

            const color = d3.scaleOrdinal()
                .domain(["n", "d"])
                .range(["#E50914", "#113CCF"])

            const movieInfo = d3.select("#second")
                .append("div")
                .style("opacity", 0)
                .attr("class", "movie-info")
                .style("background-color", "white")
                .style("border", "solid")
                .style("border-width", "1px")
                .style("border-radius", "5px")
                .style("padding", "10px")

            const showMovieInfo = function (event, d) {
                movieInfo.style("opacity", 1);
                d3.select(this)
                    .style("opacity", 1)
                    .style("stroke", "#fff");
            };

            const moveMovieInfo = function (event, d) {
                movieInfo
                    .html(`${d.title} (${d.release_year})<br>Rating: ${d.imdb_score}<br>Votes: ${d.imdb_votes}`)
                    .style("left", (event.pageX + 90) / 2 + "px")
                    .style("top", (event.pageY) / 2 + "px")
            };

            const hideMovieInfo = function (event, d) {
                movieInfo.style("opacity", 0);
                d3.select(this)
                    .style("opacity", 0.6)
                    .style("stroke", "none");
            };

            function updatePlot() {
                var leftValue = parseInt(d3.select('.left-range').property("value"));
                var rightValue = parseInt(d3.select('.right-range').property("value"));
                var selectedValue = d3.select("#platform-select").property("value");

                var filteredData;
                if (selectedValue === "n") {
                    filteredData = data.filter(function (d) {
                        return (d.platform === "n" && parseInt(d.release_year) >= leftValue && parseInt(d.release_year) <= rightValue);
                    });
                } else if (selectedValue === "d") {
                    filteredData = data.filter(function (d) {
                        return (d.platform === "d" && parseInt(d.release_year) >= leftValue && parseInt(d.release_year) <= rightValue);
                    });
                } else {
                    filteredData = data.filter(function (d) {
                        return (parseInt(d.release_year) >= leftValue && parseInt(d.release_year) <= rightValue);
                    });
                }

                var dots = svg.selectAll("circle")
                    .data(filteredData);

                dots.exit()
                    .transition()
                    .duration(500)
                    .attr("r", 0)
                    .remove();

                dots.enter()
                    .append("circle")
                    .merge(dots)
                    .attr("r", 4)
                    .style("opacity", 0.6)
                    .style("fill", function (d) { return color(d.platform); })
                    .on("mouseover", showMovieInfo)
                    .on("mousemove", moveMovieInfo)
                    .on("mouseleave", hideMovieInfo)

                    .transition()
                    .duration(500)
                    .attr("cx", function (d) { return x(d.imdb_votes); })
                    .attr("cy", function (d) { return y(d.imdb_score); });

            }
            // Add dots
            updatePlot();

            // update the plot for a given platform
            d3.select("#platform-select").on("change", updatePlot);
            // update the plot for a given xlim value
            d3.select("#xlimRange").on("input", function () {
                var xlim = d3.select(this).property("value");
                // Update X axis
                x.domain([0, xlim])
                xAxis.transition().duration(500).call(d3.axisBottom(x))

                updatePlot();
            });
            // update the plot for the given range of release year
            d3.select('.left-range').on('input', updatePlot);
            d3.select('.right-range').on('input', updatePlot);

            $(function () {
                $('#xlimRange').on('input change', function () {
                    var element = $('#xlimRange'),
                        value = element.val(),
                        rangeDiff = 2400000 - 1000,
                        step;

                    if (value > 200000) {
                        step = Math.floor(rangeDiff / 20);
                    } else {
                        step = Math.floor(rangeDiff / 200);
                    }

                    element.attr('step', step);
                });

                var leftRange = $('.left-range');
                var rightRange = $('.right-range');

                leftRange.on('input', handleRangeInput);
                rightRange.on('input', handleRangeInput);

                function handleRangeInput() {
                    var leftValue = parseInt(leftRange.val());
                    var rightValue = parseInt(rightRange.val());

                    if (leftValue >= rightValue) {
                        if ($(this).hasClass('left-range')) {
                            leftRange.val(rightValue - 1);
                        } else {
                            rightRange.val(leftValue + 1);
                        }
                    }

                }
                $('#year-slider input[type="range"]').on('input', function () {
                    var leftValue = leftRange.val();
                    var rightValue = rightRange.val();

                    $('#slider-value').text('Release year: ' + leftValue + ' - ' + rightValue);
                });
                $('[data-toggle="tooltip"]').tooltip()
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
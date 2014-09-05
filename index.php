<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>plexWatch</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">
    <meta name="author" content="">

    <!-- Le styles -->
    <link href="css/plexwatch.css" rel="stylesheet">
    <link href="css/font-awesome.min.css" rel="stylesheet" >

    <style type="text/css">
        body {
            padding-top: 60px;
            padding-bottom: 40px;
        }
        .sidebar-nav {
            padding: 9px 0;
        }
        .spinner {
            padding-bottom: 25px;
            position: relative;
        }
    </style>

    <!-- touch icons -->
    <link rel="shortcut icon" href="images/favicon.ico">
    <link rel="apple-touch-icon" href="images/icon_iphone.png">
    <link rel="apple-touch-icon" sizes="72x72" href="images/icon_ipad.png">
    <link rel="apple-touch-icon" sizes="114x114" href="images/icon_iphone@2x.png">
    <link rel="apple-touch-icon" sizes="144x144" href="images/icon_ipad@2x.png">
</head>

<body>



<div class="container">
    <div class="navbar navbar-fixed-top">
        <div class="navbar-inner">
            <a href="index.php"><div class="logo hidden-phone"></div></a>
            <ul class="nav">

                <li class="active"><a href="index.php"><i class="icon-2x icon-home icon-white" data-toggle="tooltip" data-placement="bottom" title="Home" id="home"></i></a></li>
                <li><a href="history.php"><i class="icon-2x icon-calendar icon-white" data-toggle="tooltip" data-placement="bottom" title="History" id="history"></i></a></li>
                <li><a href="stats.php"><i class="icon-2x icon-tasks icon-white" data-toggle="tooltip" data-placement="bottom" title="Stats" id="stats"></i></a></li>
                <li><a href="users.php"><i class="icon-2x icon-group icon-white" data-toggle="tooltip" data-placement="bottom" title="Users" id="users"></i></a></li>
                <li><a href="charts.php"><i class="icon-2x icon-bar-chart icon-white" data-toggle="tooltip" data-placement="bottom" title="Charts" id="charts"></i></a></li>
                <li><a href="settings.php"><i class="icon-2x icon-wrench icon-white" data-toggle="tooltip" data-placement="bottom" title="Settings" id="settings"></i></a></li>

            </ul>

        </div>
    </div>
</div>



<div class="container-fluid">
    <div class='row-fluid'>
        <div class='span12'>

        </div>
    </div>
    <div class='row-fluid'>
        <div class='span12'>
            <?php
            $guisettingsFile = "config/config.php";
            if (file_exists($guisettingsFile)) {
                require_once(dirname(__FILE__) . '/config/config.php');
            }else{
                header("Location: settings.php");
            }

            echo "<div class='wellbg'>";
            echo "<div class='wellheader'>";
            echo "<div class='dashboard-wellheader'>";
            echo "<h3>Statistics</h3>";
            echo "</div>";
            echo "</div>";
            echo "<div id='library-stats' class='stats'>";
            echo "<div id='stats-spinner' class='spinner'></div>";
            echo "</div>";

            echo "</div>";
            echo "</div>";
            echo "<div class='row-fluid'>";
            echo "<div class='span12'>";
            echo "<div class='wellbg'>";
            echo "<div class='wellheader'>";
            echo "<div class='dashboard-wellheader'>";
            echo "<div id='currentActivityHeader'>";
            require("includes/current_activity_header.php");
            echo "</div>";
            echo "</div>";
            echo "</div>";
            echo "<div id='currentActivity'>";
            require("includes/current_activity.php");
            echo "</div>";
            echo "</div>";
            echo "</div>";
            echo "</div>";

            echo "</div>";

            /* recently added rows -- dynamic */
            echo "<div class='row-fluid'>";
            echo "<div class='wellbg'>";
            echo "<div class='wellheader'>";
            echo "<div class='dashboard-wellheader'>";
            echo "<h3>Recently Added</h3>";
            echo "</div>";
            echo "</div>";
            echo "<div id='recentlyAdded'></div>";
            echo "</div>";
            ?>



            <footer>

            </footer>

        </div><!--/.fluid-container-->

        <!-- javascript
        ================================================== -->
        <!-- Placed at the end of the document so the pages load faster -->
        <script src="js/jquery-2.0.3.js"></script>
        <script src="js/bootstrap.js"></script>
        <script src="js/spin.min.js"></script>
        <script>

            function currentActivityHeader() {
                $('#currentActivityHeader').load('includes/current_activity_header.php');
            }
            setInterval('currentActivityHeader()', 15000);

        </script>
        <script>

            function currentActivity() {
                $('#currentActivity').load('includes/current_activity.php');
            }
            setInterval('currentActivity()', 15000);

        </script>
        <script>
            function recentlyAdded() {
                var widthVal= $('body').find(".container-fluid").width();
                $('#recentlyAdded').load('includes/recently_added.php?width=' + widthVal);
            }

            $(document).ready(function () {
                recentlyAdded()
                $(window).resize(function() {
                    recentlyAdded()
                });
            });
        </script>
        <script>
            var opts = {
                lines: 8, // The number of lines to draw
                length: 8, // The length of each line
                width: 4, // The line thickness
                radius: 5, // The radius of the inner circle
                corners: 1, // Corner roundness (0..1)
                rotate: 0, // The rotation offset
                direction: 1, // 1: clockwise, -1: counterclockwise
                color: '#fff', // #rgb or #rrggbb or array of colors
                speed: 1, // Rounds per second
                trail: 60, // Afterglow percentage
                shadow: false, // Whether to render a shadow
                hwaccel: false, // Whether to use hardware acceleration
                className: 'spinner', // The CSS class to assign to the spinner
                zIndex: 2e9, // The z-index (defaults to 2000000000)
                top: '0', // Top position relative to parent
                left: '50%' // Left position relative to parent
            };
            var target = document.getElementById('stats-spinner');
            var spinner = new Spinner(opts).spin(target);
        </script>

        <script>
            $(document).ready(function() {
                $.post("datafactory/get-library-stats.php",
                    function(data) {
                        if(data)
                        {
                            $("#library-stats").html(data);
                        }
                        else
                        {
                            $("#library-stats").html("Error retrieving library statistics.");
                        }
                    }
                );
            } );
        </script>

        <script>
            $(document).ready(function() {
                $('#home').tooltip();
            });
            $(document).ready(function() {
                $('#history').tooltip();
            });
            $(document).ready(function() {
                $('#users').tooltip();
            });
            $(document).ready(function() {
                $('#charts').tooltip();
            });
            $(document).ready(function() {
                $('#settings').tooltip();
            });
            $(document).ready(function() {
                $('#stats').tooltip();
            });
        </script>


</body>
</html>
<?php
include 'includes/admin-config.php';
include_once 'auth.php';
?>
<!DOCTYPE html>
<html>
<head>
    <?php include (ALUM_TEMPLATES.'metatag.php');?>
    <link rel="stylesheet" href="css/fullcalendar.min.css" />
</head>

<body class="theme-red">
    <?php
        // <!-- Page Loader -->
        include ALUM_TEMPLATES.'loader.php';
        // <!-- #END# Page Loader -->
        // <!-- Top Bar -->
            include ALUM_TEMPLATES.'top-navigation.php';
        // <!-- #Top Bar -->
        // <!-- Left Sidebar -->
            include ALUM_TEMPLATES.'left-links.php';
        // <!-- #Left Sidebar -->
    ?>
    <section class="content">
        <div class="container-fluid">
            <!-- Calender -->
            <div class="row clearfix">
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                    <div class="card">
                        <div class="header">
                            <h2>Latest Events</h2>
                            <ul class="header-dropdown m-r--5">
                                <li class="dropdown">
                                    <span class="badge bg-pink">Event For Alumni</span>
                                    <span class="badge bg-cyan">Event For Student</span>
                                    <span class="badge bg-orange">Event For Faculty</span>
                                    <span class="badge bg-purple">Event For All</span>
                                </li>
                            </ul>
                        </div>
                        <div class="body">
                        <div id='calendar'></div>
                        <div class="clearfix"></div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- #END# Calender -->
        </div> 
    </section>
    <?php include ALUM_TEMPLATES.'footer.php';?>
    <script src="js/moment.min.js"></script>
    <script src="js/fullcalendar.min.js"></script>
<script>
    $(document).ready(function () {
        var calendar = $('#calendar').fullCalendar({
            editable: true,
            events: "ajax/ajax-fetch-event.php",
            displayEventTime: false,
            eventRender: function (event, element, view) {
              console.log(event);
              console.log(element);
              console.log(view);
                if (event.allDay === 'true') {
                    event.allDay = true;
                } else {
                    event.allDay = false;
                }
            },
            selectable: false,
            selectHelper: true,
            editable: false,
        });
    });
</script>
</body>

</html>

<?php
include 'includes/admin-config.php';
include_once 'auth.php';
?>
<!DOCTYPE html>
<html>
<head>
    <?php include (ALUM_TEMPLATES.'metatag.php');?>
    <link rel="stylesheet" href="css/fullcalendar.min.css" />
    <link href="plugins/bootstrap-select/css/bootstrap-select.css" rel="stylesheet" />
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
                            <h2>Add Event</h2>
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
        <div class="clearfix"></div>
            <div class="modal fade" id="CalenderModal" tabindex="-1" role="dialog">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h4 class="modal-title" id="defaultModalLabel">Add Event</h4>
                        </div>
                        <div class="modal-body">
                            <form id="form_validation" method="POST" enctype="multipart/form-data">
                            <div class="body">
                                <!-- Inline Layout -->
                                <div class="form-group form-float">
                                    <div class="form-line">
                                        <input type="text" class="form-control" name="txtAlbumName" required maxlength="60">
                                        <label class="form-label">Event Title</label>
                                    </div>
                                </div>
                                <div class="form-group form-float">
                                    <div class="form-line">
                                        <select class="form-control" name="slctEventType" required>
                                            <option value="no-select">Select Event Type</option>
                                            <option value="alumni">Alumni</option>
                                            <option value="student">Student</option>
                                            <option value="staff">Faculty</option>
                                            <option value="all">All</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group form-float">
                                    <div class="form-line">
                                        <textarea class="form-control" name="txtDescription" required maxlength="360"></textarea>
                                        <label class="form-label">Description</label>
                                    </div>
                                </div>
                                <div class="clearfix"></div>
                                <!-- #END# Inline Layout -->
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-default waves-effect" data-dismiss="modal">CLOSE</button>
                                <button type="button" name="btnEvent" class="btn btn-danger waves-effect">Add</button>
                            </div>
                        </form>
                        </div>
                    </div>
                </div>
            </div>
    </section>
    <?php include ALUM_TEMPLATES.'footer.php';?>
    <script src="js/moment.min.js"></script>
    <script src="js/fullcalendar.min.js"></script>
    <script src="plugins/bootstrap-select/js/bootstrap-select.js"></script>
<script>
    $(document).ready(function () {
         var calendar = $('#calendar').fullCalendar({
            editable: true,
            events: "ajax/ajax-fetch-event.php",
            displayEventTime: false,
            eventRender: function (event, element, view) {
                if (event.allDay === 'true') {
                    event.allDay = true;
                } else {
                    event.allDay = false;
                }
            },
            selectable: true,
            selectHelper: true,
            select: function (start, end, allDay) {
                $('#CalenderModal').modal();
                    var start = $.fullCalendar.formatDate(start, "Y-MM-DD HH:mm:ss");
                    var end = $.fullCalendar.formatDate(end, "Y-MM-DD HH:mm:ss");
                $('body').on('click','[name="btnEvent"]',function(){
                    var title = $('[name="txtAlbumName"]').val();
                    var description = $('[name="txtDescription"]').val();
                    var userType = $('[name="slctEventType"]').val();
                    $.ajax({
                        url: 'ajax/ajax-add-event.php',
                        data: 'title=' + title +'&description='+ description +'&eventType='+userType+'&start=' + start + '&end=' + end,
                        type: "POST",
                        success: function (data) {
                            $('#CalenderModal').modal('hide');
                            $('[name="txtAlbumName"]').val('');
                            $('[name="txtDescription"]').val('');
                            var response = JSON.parse(data);
                            if(response.status == true){
                                location.reload();
                                showNotification('alert-success', response.msg, 'top', 'center', '', '');
                                calendar.fullCalendar('renderEvent',
                                    {
                                        title: title,
                                        start: start,
                                        end: end,
                                        allDay: allDay
                                    }, true);
                            }else{
                                showNotification('alert-danger', response.msg, 'top', 'center', '', '');
                            }
                        }
                    });
                });
                calendar.fullCalendar('unselect');
            },
            
            editable: true,
            eventDrop: function (event, delta) {
                        var start = $.fullCalendar.formatDate(event.start, "Y-MM-DD HH:mm:ss");
                        var end = $.fullCalendar.formatDate(event.end, "Y-MM-DD HH:mm:ss");
                        $.ajax({
                            url: 'ajax/ajax-edit-event.php',
                            data: 'title=' + event.title + '&start=' + start + '&end=' + end + '&id=' + event.id,
                            type: "POST",
                            success: function (data) {
                                location.reload();
                            }
                        });
                    },
            eventClick: function (event) {
                var deleteMsg = confirm("Do you really want to delete?");
                if (deleteMsg) {
                    $.ajax({
                        type: "POST",
                        url: "ajax/ajax-delete-event.php",
                        data: "&id=" + event.id,
                        success: function (data) {
                            location.reload();
                        }
                    });
                }
            }

        });
    });
</script>
</body>

</html>

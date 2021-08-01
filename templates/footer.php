
<!-- Jquery Core Js -->
<script src="plugins/jquery/jquery.min.js"></script>
<script src="script/common-function.js"></script>
<!-- Bootstrap Core Js -->
<script src="plugins/bootstrap/js/bootstrap.js"></script>
<!-- Toast Plugin Js -->
<!-- <script type="text/javascript" src="js/jquery.toast.js"></script> -->
<!-- Select Plugin Js -->
<script src="plugins/bootstrap-select/js/bootstrap-select.js"></script>

<!-- Slimscroll Plugin Js -->
<script src="plugins/jquery-slimscroll/jquery.slimscroll.js"></script>

<!-- Waves Effect Plugin Js -->
<script src="plugins/node-waves/waves.js"></script>
<!-- Moment Plugin Js -->
<script src="plugins/momentjs/moment.js"></script>
<!-- Jquery Validation Plugin Css -->
<script src="plugins/jquery-validation/jquery.validate.js"></script>
<!-- Bootstrap Material Datetime Picker Plugin Js -->
<script src="plugins/bootstrap-material-datetimepicker/js/bootstrap-material-datetimepicker.js"></script>
<!-- Bootstrap Datepicker Plugin Js -->
<script src="plugins/bootstrap-datepicker/js/bootstrap-datepicker.js"></script>
<script src="plugins/morrisjs/morris.js"></script>
<script src="plugins/bootstrap-notify/bootstrap-notify.js"></script>

<!-- Jquery DataTable Plugin Js -->
<script src="plugins/jquery-datatable/jquery.dataTables.js"></script>
<script src="plugins/jquery-datatable/skin/bootstrap/js/dataTables.bootstrap.js"></script>
<script src="plugins/jquery-datatable/extensions/export/dataTables.buttons.min.js"></script>
<script src="plugins/jquery-datatable/extensions/export/buttons.flash.min.js"></script>
<script src="plugins/jquery-datatable/extensions/export/jszip.min.js"></script>
<script src="plugins/jquery-datatable/extensions/export/pdfmake.min.js"></script>
<script src="plugins/jquery-datatable/extensions/export/vfs_fonts.js"></script>
<script src="plugins/jquery-datatable/extensions/export/buttons.html5.min.js"></script>
<script src="plugins/jquery-datatable/extensions/export/buttons.print.min.js"></script>
<!-- Custom Js -->
<script src="js/admin.js"></script>
<script src="js/pages/forms/form-validation.js"></script>
<script type="text/javascript">
    var PROJECT_NAME = 'Alumni Portal Says';
</script>
<!-- Demo Js -->
<script src="js/demo.js"></script>
<script src="js/pages/tables/jquery-datatable.js"></script>
<script type="text/javascript" src="js/alertify.js"></script>
<script>
    $('body').on('click', '#SearchBluetooth', function () {
        navigator.bluetooth.requestDevice({filters: [{services: ['battery_service']}]}).then(device => { 
            /* … */
        }).catch(error => {
            console.error(error);
        });
    });

    // Online or offline check Start 
    if (navigator.onLine) {
        $('#UserOnlineStatus').html('Online')
    } else {
        $('#UserOnlineStatus').html('<i class="material-icons" style="vertical-align: bottom;">cloud_off</i> Offline')
    }
    // Online or offline check End
    function confirmAlertify(alertifyObj, title, msg, okCallback, cancelCallback) {
        if (alertifyObj.type == 'confirm') {
            alertify.okBtn(alertifyObj.okBtnText).cancelBtn(alertifyObj.cancelBtnText).confirm(title + '<div class="col-sm-12 alert_msg">' + msg + '</div>', okCallback, cancelCallback);
        }
    }
    function showNotification(colorName, text, placementFrom, placementAlign, animateEnter, animateExit) {
        if (colorName === null || colorName === '') {
            colorName = 'bg-black';
        }
        if (text === null || text === '') {
            text = 'Turning standard Bootstrap alerts';
        }
        if (animateEnter === null || animateEnter === '') {
            animateEnter = 'animated fadeInDown';
        }
        if (animateExit === null || animateExit === '') {
            animateExit = 'animated fadeOutUp';
        }
        var allowDismiss = true;

        $.notify({
            message: text
        },
                {
                    type: colorName,
                    allow_dismiss: allowDismiss,
                    newest_on_top: true,
                    timer: 1000,
                    placement: {
                        from: placementFrom,
                        align: placementAlign
                    },
                    animate: {
                        enter: animateEnter,
                        exit: animateExit
                    },
                    template: '<div data-notify="container" class="bootstrap-notify-container alert alert-dismissible {0} ' + (allowDismiss ? "p-r-35" : "") + '" role="alert">' +
                            '<button type="button" aria-hidden="true" class="close" data-notify="dismiss">×</button>' +
                            '<span data-notify="icon"></span> ' +
                            '<span data-notify="title">{1}</span> ' +
                            '<span data-notify="message">{2}</span>' +
                            '<div class="progress" data-notify="progressbar">' +
                            '<div class="progress-bar progress-bar-{0}" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width: 0%;"></div>' +
                            '</div>' +
                            '<a href="{3}" target="{4}" data-notify="url"></a>' +
                            '</div>'
                });
    }
    $.ajax({
        type: 'POST',
        url: './ajax/ajax-load-theme.php',
        data: 'loadTheme=true',
        success: function (response, data) {
            var data = JSON.parse(response);
            $('.right-sidebar .demo-choose-skin li').removeClass('active');
            $('body').addClass('theme-' + data.theme);
            $('.right-sidebar .demo-choose-skin').find('.' + data.theme).parent().addClass('active');
        }
    });
</script>
<?php
if (isset($_SESSION['error']) && !empty($_SESSION['error'])) {
    echo "<script>
                showNotification('alert-danger', '" . $_SESSION['error'] . "', 'top', 'center', '', '');
            </script>";
    unset($_SESSION['error']);
} else if (isset($_SESSION['success']) && !empty($_SESSION['success'])) {
    echo "<script>
            showNotification('alert-success', '" . $_SESSION['success'] . "', 'top', 'center', '', '');
            </script>";
    unset($_SESSION['success']);
} else if (isset($_SESSION['warning'])) {
    echo "<script>
            showNotification('alert-warning', '" . $_SESSION['warning'] . "', 'top', 'center', '', '');
            </script>";
    unset($_SESSION['warning']);
}
?>
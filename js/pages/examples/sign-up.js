$(function () {
     $('#bs_datepicker_container input').datepicker({
        format: "yyyy",
        viewMode: "years", 
        minViewMode: "years",
        autoclose: true,
        container: '#bs_datepicker_container'
    });
    $('#sign_up').validate({
        rules: {
            'terms': {
                required: true
            },
            'confirm': {
                equalTo: '[name="password"]'
            }
        },
        highlight: function (input) {
            console.log(input);
            $(input).parents('.form-line').addClass('error');
        },
        unhighlight: function (input) {
            $(input).parents('.form-line').removeClass('error');
        },
        errorPlacement: function (error, element) {
            $(element).parents('.input-group').append(error);
            $(element).parents('.form-group').append(error);
        }
    });
});
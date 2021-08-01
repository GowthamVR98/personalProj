$(document).ready(function(){
    $(function(){ 
        var filename = document.location.href.match(/[^\/]+$/)[0] 
        var fileNameReplace = filename.replace(".php", "");
        $('.menu ul').find('.active').removeClass('active'); 
        $('.menu ul').find('[href="'+fileNameReplace+'.php"]').addClass('toggled').parent('li').addClass('active').parent().parent('li').addClass('active').find('.menu-toggle').addClass('toggled').parent().find('.ml-menu').show(); 
        $('.menu ul').find('[href="'+fileNameReplace+'"]').addClass('toggled').parent('li').addClass('active').parent().parent('li').addClass('active').find('.menu-toggle').addClass('toggled').parent().find('.ml-menu').show(); 
    });
    $('#ShowHide').click(function(){
        $('#hideShow').show(500);
        $('#ShowHide').hide();
        $('#hideOptionBlock').show();
    });
    $('#hideOptionBlock').click(function(){
        $('#hideShow').hide(500);
        $('#hideOptionBlock').hide();
        $('#ShowHide').show();
    });

});
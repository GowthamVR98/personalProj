<?php
include 'includes/admin-config.php';
include_once 'auth.php';
?>
<!DOCTYPE html>
<html>
<head>
    <?php include (ALUM_TEMPLATES.'metatag.php');?>
     <!-- Light Gallery Plugin Css -->
    <link href="plugins/light-gallery/css/lightgallery.css" rel="stylesheet">
    <style>
        .carousel-control:hover, .carousel-control:focus {
        color: #fff;
        text-decoration: none;
        filter: alpha(opacity=90);
        outline: 0;
        opacity: .5;
        background:#1e5b79;
        }
    </style>
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
        <div class="container-fluid card">
            <!-- Calender -->
            <!-- Media Alignment -->
            <div class="row clearfix">
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                    <div class="">
                        <div class="header">
                            <h2>
                                Latest Achievements
                            </h2>
                        </div>
                        <div class="body">
                            <div class="bs-example appendAchievement" data-example-id="media-alignment">
                                
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- #END# Media Alignment -->
            <div class="clearfix"></div>
        </div>
    </section>
    <?php include ALUM_TEMPLATES.'footer.php';?> 
    <script>
        $(document).ready(function(){
            var pageno = 1;
            $.ajax({
                type:'POST',
                url:'ajax/ajax-manage-achievement.php',
                data:'pageno='+pageno,
                success: function(response){
                    var data = JSON.parse(response);
                    $('.appendAchievement').html(data.htmlContent); 
                }

            });
            $('body').on('click','.pagination .page-link',function(){
                var pageno = $(this).attr('id');
                $.ajax({
                    type:'POST',
                    url:'ajax/ajax-manage-achievement.php',
                    data:'pageno='+pageno,
                    success: function(response){
                        var data = JSON.parse(response);
                        $('.appendAchievement').html(data.htmlContent); 
                    }

                });
            });
            $('body').on('click','.pagination .previous-link',function(){
                var pageno = parseInt($(this).attr('id')) - 1;
                $.ajax({
                    type:'POST',
                    url:'ajax/ajax-manage-achievement.php',
                    data:'pageno='+pageno,
                    success: function(response){
                        var data = JSON.parse(response);
                        $('.appendAchievement').html(data.htmlContent); 
                    }

                });
            });
            $('body').on('click','.pagination .next-link',function(){
                var pageno = parseInt($(this).attr('id')) + 1;
                $.ajax({
                    type:'POST',
                    url:'ajax/ajax-manage-achievement.php',
                    data:'pageno='+pageno,
                    success: function(response){
                        var data = JSON.parse(response);
                        $('.appendAchievement').html(data.htmlContent); 
                    }

                });
            }); 
        });
    </script>
</body>

</html>

<!DOCTYPE HTML>
<html lang="ru">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">
    <meta name="viewport" content="width=device-width, initial-scale=1, minimum-scale=1, maximum-scale=1, viewport-fit=cover" />
    <title><?php echo $title; ?></title>
    <script>
        var base_url = '<?php echo base_url(); ?>';
        var percent = <?php echo PERCENT_SYSTEM; ?>;
    </script>
    <link rel="stylesheet" type="text/css" href="<?php echo site_url('assets/styles/all_area/mobile/style.css'); ?>?v2">
    <link rel="stylesheet" type="text/css" href="<?php echo site_url('assets/styles/all_area/mobile/framework.css'); ?>?v3 ">
    <link href="https://fonts.googleapis.com/css?family=Open+Sans&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.8.2/css/all.css" integrity="sha384-oS3vJWv+0UjzBfQzYUhtDYW+Pj2yciDJxpsK1OYPAYjqT085Qq/1cq5FLXAZQ7Ay" crossorigin="anonymous">
    <link rel="apple-touch-icon" sizes="180x180" href="images/ath.png">

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    <script src="https://api-maps.yandex.ru/2.1/?lang=ru_RU" type="text/javascript"></script>
    <script defer type="text/javascript" src="<?php echo site_url('assets/js/mobile/custom.js'); ?>"></script>
    <script src="<?php echo site_url('assets/js/all_area/vex.combined.js'); ?>?v1"></script>
    <link href="<?php echo site_url('assets/styles/all_area/vex.css'); ?>" rel="stylesheet" type="text/css"/>
    <link href="<?php echo site_url('assets/styles/all_area/vex-theme-default.css'); ?>" rel="stylesheet" type="text/css"/>
    <script>vex.defaultOptions.className = 'vex-theme-default'</script>
    <script src="https://cdn.jsdelivr.net/npm/vue"></script>
    <script src="https://cdn.jsdelivr.net/npm/vue-socket.io@3.0.5/dist/vue-socketio.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/socket.io/2.1.1/socket.io.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.24.0/moment.min.js"
            integrity="sha256-4iQZ6BVL4qNKlQ27TExEhBN1HFPvAvAMbFavKKosSWQ=" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.24.0/locale/ru.js"
            integrity="sha256-qR4pdhxtx7dwKGJuYGoYjfnCQBPXv47hzLLU8jPLVUY=" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/vue-moment-lib@1.2.2/dist/vue-moment-lib.umd.min.js"
            integrity="sha256-9MBfgrCHOT7a4rpY02Q58SiVHa940X93T80zjZVwV5s=" crossorigin="anonymous"></script>

    <script src="https://unpkg.com/vue-router"></script>
    <style>
        @font-face{font-family:'Museo Sans Cyrl 500';
            src:url(<?php echo site_url('assets/fonts/MuseoSansCyrl-500.eot'); ?>);
            src:local(https://barter.new/assets/fonts/MuseoSansCyrl-500),url(<?php echo site_url('assets/fonts/MuseoSansCyrl-500.woff'); ?>) format("woff");
            font-weight:400;font-style:normal}@font-face{font-family:'Museo Sans Cyrl 700';
            src:url(<?php echo site_url('assets/fonts/MuseoSansCyrl-700.eot'); ?>);
            src:local(https://barter.new/assets/fonts/MuseoSansCyrl-700),url(<?php echo site_url('assets/fonts/MuseoSansCyrl-700.woff'); ?>) format("woff");
            font-weight:400;font-style:normal}@font-face{font-family:'Museo Sans Cyrl 300';src:url(<?php echo site_url('assets/fonts/MuseoSansCyrl-300.eot'); ?>);
            src:local(MuseoSansCyrl-300),url(<?php echo site_url('assets/fonts/MuseoSansCyrl-300.woff'); ?>) format("woff");font-weight:300;font-style:normal}
    </style>
</head>


<body class="theme-light" data-highlight="blue2">

<div id="page">
    <div id="page-preloader">
        <div class="loader-main loader-inactive"><div class="preload-spinner border-highlight"></div></div>
    </div>
    

    <div class="header header-fixed header-logo-left ">
        <a href="/" class="back-button header-title">Barter-Business</a>
        <a href="<?php echo site_url('company/search'); ?>" class="header-icon header-icon-1" ><i class="fas fa-search"></i></a>
    </div>

    <div class="footer-menu footer-5-icons footer-menu-center-icon">
        <a href="<?php echo site_url('company/category/detail'); ?>"><i class="fas fa-align-left"></i><span>Категории</span></a>
        <a href="<?php echo site_url('company/product/all'); ?>"><i class="fas fa-shopping-bag"></i><span>Товары</span></a>
        <a href="<?php echo site_url('company/cabinet'); ?>"><i class="fas fa-home"></i><span>Главная</span></a>
        <a href="<?php echo site_url('company/chat'); ?>"><i class="fas fa-comment"></i><span>Чат</span></a>
        <a href="<?php echo site_url('company/menu'); ?>" ><i class="fa fa-bars"></i><span>Меню</span></a>
        <div class="clear"></div>
    </div>
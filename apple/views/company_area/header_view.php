<!DOCTYPE html>
<html lang="ru">
<head>

    <!--META START -->
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">

    <title><?php echo $title; ?></title>

    <!-- CSS -->
    <link rel="stylesheet" href="<?php echo site_url('assets/styles/all_area/plugins.bundle.css'); ?>">
    <link rel="stylesheet" href="<?php echo site_url('assets/styles/all_area/style.bundle.css'); ?>?v15">


    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/7.28.11/sweetalert2.css">

    <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i,800,800i&display=swap"
          rel="stylesheet">

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>

    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/suggestions-jquery@17.10.0/dist/js/jquery.suggestions.min.js?"></script>
    <!-- Da-data подсказки -->
    <script src="https://cdn.jsdelivr.net/npm/promise-polyfill"></script> <!-- Da-data подсказки -->
    <script>
        var base_url = '<?php echo base_url(); ?>';
        var percent = <?php echo PERCENT_SYSTEM; ?>;
    </script>

</head>
<body class="kt-page--loading-enabled kt-page--loading kt-quick-panel--right kt-demo-panel--right kt-offcanvas-panel--right kt-header--fixed kt-header--minimize-topbar kt-header-mobile--fixed kt-subheader--enabled kt-subheader--transparent kt-page--loading">
<!-- Load Facebook SDK for JavaScript -->

<div id="kt_header_mobile" class="kt-header-mobile  kt-header-mobile--fixed ">
    <div class="kt-header-mobile__logo">
        <a href="/">
            <img alt="Logo" src="<?php echo site_url('uploads/companys_logo/' . $company_data['logo']); ?>"/>
        </a>
    </div>
    <div class="kt-header-mobile__toolbar">

        <button class="kt-header-mobile__toolbar-toggler" id="kt_header_mobile_toggler"><span></span></button>
        <button class="kt-header-mobile__toolbar-topbar-toggler" id="kt_header_mobile_topbar_toggler"><i
                    class="flaticon-more-1"></i></button>
    </div>
</div>
<!-- end:: Header Mobile -->
<div class="kt-grid kt-grid--hor kt-grid--root">
    <div class="kt-grid__item kt-grid__item--fluid kt-grid kt-grid--ver kt-page">
        <div class="kt-grid__item kt-grid__item--fluid kt-grid kt-grid--hor kt-wrapper" id="kt_wrapper">
            <!-- begin:: Header -->
            <div id="kt_header" class="kt-header kt-grid__item  kt-header--fixed " data-ktheader-minimize="on">
                <div class="kt-header__top">
                    <div class="kt-container ">
                        <!-- begin:: Brand -->
                        <div class="kt-header__brand   kt-grid__item" id="kt_header_brand">
                            <div class="kt-header__brand-logo">
                                <a href="/">
                                    <img alt="Logo" width="60px" height="60px"
                                         src="<?php echo site_url('assets/images/img/logo.png'); ?>"
                                         class="kt-header__brand-logo-default"/>
                                    <img alt="Logo" width="40px" height="40px"
                                         src="<?php echo site_url('assets/images/img/logo.png'); ?>"
                                         class="kt-header__brand-logo-sticky"/>
                                </a>
                            </div>
                            <div class="kt-header__brand-nav">
                                <div class="dropdown">
                                    <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown"
                                            aria-haspopup="true" aria-expanded="true">
                                        Баланс: <span style="font-weight: 600"><?= ($company_data['barter_balance'] - $reserved_for_deals) / 100 ?></span>
                                    </button>
                                    <div class="dropdown-menu dropdown-menu-md">
                                        <ul class="kt-nav kt-nav--bold kt-nav--md-space">
                                            <?php if ($company_data['credit_balance'] > 0): ?>
                                            <li class="kt-nav__item">
                                                <a class="kt-nav__link active" href="#">

                                                    <span class="kt-nav__link-text">Кредит: <?php echo $company_data['credit_balance'] / 100; ?></span>
                                                </a>
                                            </li>
                                            <?php endif; ?>
                                            <li class="kt-nav__item">
                                                <span class="kt-nav__link" href="index.html#">
                                                    <span class="kt-nav__link-text">Лимит: <?php echo $company_data['month_limit'] / 100; ?></span>
                                                </span>
                                            </li>
                                            <li class="kt-nav__item">
                                                <span class="kt-nav__link" href="index.html#">

                                                    <span class="kt-nav__link-text">Продажи за месяц: <?= ($month_sales['total'] > 0) ? $month_sales['total'] / 100 : 0 ?></span>
                                                </span>
                                            </li>
                                            <li class="kt-nav__separator"></li>
                                            <li class="kt-nav__item">
                                                <a class="kt-nav__link" data-toggle="modal" data-target=".credit">

                                                    <span class="kt-nav__link-text">Взять кредит</span>
                                                </a>
                                            </li>
                                            <li class="kt-nav__item">
                                                <a class="kt-nav__link" data-toggle="modal" data-target="#btnwallet" id="credit_pol">

                                                    <span class="kt-nav__link-text">Пополнить баланс</span>
                                                </a>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- end:: Brand -->            <!-- begin:: Header Topbar -->
                        <div class="kt-header__topbar">
                            <!--begin: Search -->
                            <div class="kt-header__topbar-item kt-header__topbar-item--search dropdown kt-hidden-desktop"
                                 id="kt_quick_search_toggle">
                                <div class="kt-header__topbar-wrapper" data-toggle="dropdown" data-offset="10px,10px">
			<span class="kt-header__topbar-icon"></span>
                                </div>
                                <div class="dropdown-menu dropdown-menu-fit dropdown-menu-right dropdown-menu-anim dropdown-menu-lg">
                                    <div class="kt-quick-search kt-quick-search--dropdown kt-quick-search--result-compact"
                                         id="kt_quick_search_dropdown">
                                        <form method="get" class="kt-quick-search__form">
                                            <div class="input-group">
                                                <div class="input-group-prepend"><span class="input-group-text"><i
                                                                class="flaticon2-search-1"></i></span></div>
                                                <input type="text" class="form-control kt-quick-search__input"
                                                       placeholder="Search...">
                                                <div class="input-group-append"><span class="input-group-text"><i
                                                                class="la la-close kt-quick-search__close"></i></span></div>
                                            </div>
                                        </form>
                                        <div class="kt-quick-search__wrapper kt-scroll" data-scroll="true"
                                             data-height="325" data-mobile-height="200">

                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!--end: Search -->


                            <!--begin: User bar -->
                            <div class="kt-header__topbar-item kt-header__topbar-item--user">
                                <div class="kt-header__topbar-wrapper" data-toggle="dropdown" data-offset="10px,10px"
                                     aria-expanded="false">

                                    <img  alt="Pic"
                                         src="<?php echo site_url('uploads/companys_logo/' . $company_data['logo']); ?>">

                                    <!--use below badge element instead the user avatar to display username's first letter(remove kt-hidden class to display it) -->
                                </div>
                                <div class="dropdown-menu dropdown-menu-fit dropdown-menu-right dropdown-menu-anim dropdown-menu-xl">
                                    <!--begin: Head -->
                                    <div class="kt-user-card kt-user-card--skin-dark kt-notification-item-padding-x"
                                         style="background: rgba(0,0,0,0.8)">
                                        <div class="kt-user-card__avatar">
                                            <img alt="Pic"
                                                 src="<?php echo site_url('uploads/companys_logo/' . $company_data['logo']); ?>"/>
                                            <!--use below badge element instead the user avatar to display username's first letter(remove kt-hidden class to display it) -->

                                        </div>

                                    </div>
                                    <!--end: Head -->

                                    <!--begin: Navigation -->
                                    <div class="kt-notification">
                                        <a href="<?php echo site_url('company/cabinet/company_detail?company_id='); ?><?= $_SESSION['ses_company_data']['company_id'] ?>"
                                           class="kt-notification__item">
                                            <div class="kt-notification__item-icon">
                                                <i class="flaticon2-calendar-3 kt-font-success"></i>
                                            </div>
                                            <div class="kt-notification__item-details">
                                                <div class="kt-notification__item-title kt-font-bold">
                                                    Профиль
                                                </div>
                                            </div>
                                        </a>
                                        <a href="<?php echo site_url('company/profile'); ?>"
                                           class="kt-notification__item">
                                            <div class="kt-notification__item-icon">
                                                <i class="flaticon2-settings kt-font-warning"></i>
                                            </div>
                                            <div class="kt-notification__item-details">
                                                <div class="kt-notification__item-title kt-font-bold">
                                                    Настройки
                                                </div>

                                            </div>
                                        </a>
                                        <a href="<?php echo site_url('company/product'); ?>"
                                           class="kt-notification__item">
                                            <div class="kt-notification__item-icon">
                                                <i class="flaticon-shopping-basket kt-font-brand"></i>
                                            </div>
                                            <div class="kt-notification__item-details">
                                                <div class="kt-notification__item-title kt-font-bold">
                                                    Мои товары
                                                </div>

                                            </div>
                                        </a>
                                        <a href="<?php echo site_url('company/referrals'); ?>"
                                           class="kt-notification__item">
                                            <div class="kt-notification__item-icon">
                                                <i class="flaticon2-avatar kt-font-danger"></i>
                                            </div>
                                            <div class="kt-notification__item-details">
                                                <div class="kt-notification__item-title kt-font-bold">
                                                    Реферальная система
                                                </div>

                                            </div>
                                        </a>
                                        <a href="/"
                                           class="kt-notification__item">
                                            <div class="kt-notification__item-icon">
                                                <i class="flaticon2-help kt-font-brand"></i>
                                            </div>
                                            <div class="kt-notification__item-details">
                                                <div class="kt-notification__item-title kt-font-bold">
                                                    Помощь
                                                </div>

                                            </div>
                                        </a>


                                        <div class="kt-notification__custom kt-space-between">
                                            <a href="<?php echo site_url('publics/logout'); ?>"
                                               target="_blank" class="btn btn-label btn-label-brand btn-sm btn-bold">Выход</a>


                                        </div>
                                    </div>
                                    <!--end: Navigation -->
                                </div>
                            </div>
                            <!--end: User bar -->
                        </div>
                        <!-- end:: Header Topbar -->
                    </div>
                </div>

                <div class="kt-header__bottom">
                    <div class="kt-container ">
                        <!-- begin: Header Menu -->
                        <button class="kt-header-menu-wrapper-close" id="kt_header_menu_mobile_close_btn"><i
                                    class="la la-close"></i></button>
                        <div class="kt-header-menu-wrapper" id="kt_header_menu_wrapper">
                            <div id="kt_header_menu" class="kt-header-menu kt-header-menu-mobile ">
                                <ul class="kt-menu__nav ">
                                    <li class="kt-menu__item" aria-haspopup="true">
                                        <a href="/" class="kt-menu__link">
                                            <span class="kt-menu__link-text">Главная</span>
                                            <i class="kt-menu__ver-arrow la la-angle-right"></i>
                                        </a>
                                    </li>
                                    <li class="kt-menu__item" aria-haspopup="true">
                                        <a href="<?= site_url('company/orders/inbox') ?>" class="kt-menu__link">
                                            <span class="kt-menu__link-text">Сделки</span>
                                            <i class="kt-menu__ver-arrow la la-angle-right"></i>
                                        </a>
                                    </li>
                                    <li class="kt-menu__item" aria-haspopup="true">
                                        <a href="<?= site_url('company/product/all') ?>" class="kt-menu__link">
                                            <span class="kt-menu__link-text">Товары</span>
                                            <i class="kt-menu__ver-arrow la la-angle-right"></i>
                                        </a>
                                    </li>
                                    <li class="kt-menu__item" aria-haspopup="true">
                                        <a href="<?php echo site_url('company/fave'); ?>" class="kt-menu__link">
                                            <span class="kt-menu__link-text">Избранное</span>
                                            <i class="kt-menu__ver-arrow la la-angle-right"></i>
                                        </a>
                                    </li>
                                    <li class="kt-menu__item" aria-haspopup="true">
                                        <a href="<?php echo site_url('company/coupon'); ?>" class="kt-menu__link">
                                            <span class="kt-menu__link-text">Купоны</span>
                                            <i class="kt-menu__ver-arrow la la-angle-right"></i>
                                        </a>
                                    </li>
                                    <li class="kt-menu__item" aria-haspopup="true">
                                        <a href="<?php echo site_url('company/abon_plata'); ?>" class="kt-menu__link">
                                            <span class="kt-menu__link-text">Абонентская плата</span>
                                            <i class="kt-menu__ver-arrow la la-angle-right"></i>
                                        </a>
                                    </li>
                                </ul>
                            </div>
                            <div class="kt-header-toolbar">
                                <div class="kt-quick-search kt-quick-search--inline kt-quick-search--result-compact"
                                     id="kt_quick_search_inline">

                                            <a style="font-size: 22px" href="#" data-toggle="modal" data-target=".bd-example-modal-lg"><i class="flaticon2-search-1"></i></a>




                                </div>
                            </div>
                        </div>

                        <!-- end: Header Menu -->
                    </div>
                </div>
            </div>
            <!-- end:: Header -->
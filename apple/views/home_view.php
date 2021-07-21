<section class="content">
    <div class="container-fluid">

        <!-- Widgets -->
        <div class="row">
            <div class="col-lg-3 col-sm-6">
                <div class="counter-box text-center card shadow_material">
                    <ul class="header-dropdown m-r--5 box_home">
                        <li class="dropdown">
                            <a href="javascript:void(0);" class="dropdown-toggle" data-toggle="dropdown" role="button"
                               aria-haspopup="true" aria-expanded="false">
                                <i class="material-icons">more_vert</i>
                            </a>
                            <ul class="dropdown-menu pull-right">
                                <li>
                                    <a href="">Пополнить</a>
                                </li>
                                <li>
                                    <a href="">Снять</a>
                                </li>

                            </ul>
                        </li>
                    </ul>
                    <div class="text font-17 m-b-5 font__grad__bar">Баланс</div>
                    <h3 class="m-b-10"><?php echo ($company_data['barter_balance'] - ($sum_all_orders + ($sum_all_orders / 100) * PERCENT_SYSTEM)) / 100; ?></h3>
                </div>
            </div>
            <div class="col-lg-3 col-sm-6">
                <div class="counter-box text-center card shadow_material">
                    <div class="text font-17 m-b-5 font__grad__bar">Лимит</div>
                    <h3 class="m-b-10"><?php echo $company_data['month_limit'] / 100; ?></h3>
                </div>
            </div>
            <div class="col-lg-3 col-sm-6">
                <div class="counter-box text-center card shadow_material">
                    <div class="text font-17 m-b-5 font__grad__bar">Остаток лимита</div>
                    <h3 class="m-b-10"><?php echo $company_data['dostupno_dlya_sdelok'] / 100; ?></h3>
                </div>
            </div>
            <div class="col-lg-3 col-sm-6">
                <div class="counter-box text-center card shadow_material">
                    <ul class="header-dropdown m-r--5 box_home">
                        <li class="dropdown">
                            <a href="javascript:void(0);" class="dropdown-toggle" data-toggle="dropdown" role="button"
                               aria-haspopup="true" aria-expanded="false">
                                <i class="material-icons">more_vert</i>
                            </a>
                            <ul class="dropdown-menu pull-right">
                                <li>
                                    <a href="javascript:void(0);">Взять кредит</a>
                                </li>
                                <li>
                                    <a href="javascript:void(0);">Погасить досрочно</a>
                                </li>
                            </ul>
                        </li>
                    </ul>
                    <div class="text font-17 m-b-5 font__grad__bar ">Кредит</div>
                    <h3 class="m-b-10"><?php echo $company_data['credit_balance'] / 100; ?></h3>

                </div>
            </div>
        </div>
        <!-- #END# Widgets -->
        <div class="row">
            <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12">
                <div class="card shadow_material">
                    <div class="header">
                        <h2 style="font-size: 18px" class="align-center font-bold font__grad__bar">Отправь реферальную ссылку и подари другу 500 руб. за регистрацию в системе</h2>
                    </div>
                    <div class="body">

                            <div class="social align-center">
                                <div class="addthis_inline_share_toolbox"
                                     data-url="<?= site_url('/publics/reg?ref='
                                         .$company_data['company_id']) ?>"
                                     data-title="Я пользуюсь системой barter-business.ru. Кликни по ссылке, чтобы получить бонус 500 руб. для новых пользователей на первые покупки"
                                     data-description="Я пользуюсь системой barter-business.ru. Кликни по ссылке, чтобы получить бонус 500 руб. для новых пользователей на первые покупки"
                                     data-media="https://barter-business.ru/assets/images/public/home/top_image.png"></div>
                            </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="new_company m-t-20" style="position: relative;">
        <h4>Новые компании</h4>
        <?php if (!empty($last_new_companies)) { ?>
        <div class="row clearfix multiple-items">

            <?php foreach ($last_new_companies as $last_company)
            { ?>

            <?php if ($last_company['company_id'] != $_SESSION['ses_company_data']['company_id']) { ?>
            <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12">
                <div class="box-part text-center shadow_material">
                    <img src="https://barter-business.ru/uploads/companys_logo/<?php echo $last_company['logo']; ?>" height="100" width="100" class="img-circle logo_new_company img_border_shadow">
                    <div class="title p-t-15"><h5><?php echo mb_substr($last_company["company_name"], 0, 65, "UTF-8"); ?></h5></div>
                    <hr>
                    <div class="text p-b-10"><p style="overflow: hidden;text-align: left;"><?php echo mb_substr(str_replace("\n", '<br />', $last_company["description_company"]), 0, 120, "UTF-8"); ?>...</p></div>
                    <hr>
                    <a href="<?php echo site_url('company/cabinet/company_detail?company_id=' .$last_company['company_id']); ?>">Подробнее</a>
                </div>
            </div>
            <?php } ?>
            <?php } ?>

        </div>
            <div style="margin-top: -10px;text-align: center;">
            <button class="prev"><i class="material-icons" style="font-size: 50px; color: #d1d1d1;">navigate_before</i></button>
            <button class="next"> <i class="material-icons" style="font-size: 50px; color: #d1d1d1;">navigate_next</i> </button>
            </div>
        <?php } else { ?>
            <p>Пока что нет новых компаний...</p>
        <?php } ?>
    </div>
    </div>

</section>
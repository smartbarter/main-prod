<div class="page-content header-clear-medium">
    <?php if ($company_data['status'] == 1) { ?>
        <div class="content">
            <div class="content content-box bg-green2-dark round-medium shadow-large">
                <h4 class="color-white bold top-10">Поздравляем, Ваша компания зарегистрирована!</h4>
                <p class="color-white bottom-10">
                    Сейчас Вашу компанию проверяют менеджеры, в течении 24 часов будет доступен полный
                    функционал!<br> Ждите активации или позвоните по телефону -
                    <a href="tel:+79272365529">8 (927) 236-55-29</a>, если у Вас остались вопросы.
                </p>
            </div>
        </div>
    <?php }//if?>
    <div class="content" style="margin-bottom: 20px;">
        <a href="https://chat.whatsapp.com/L23VNKulx1qLmjAO3WzWY7"
           class="button button-s round-small shadow-large bg-highlight button-full bottom-10 font-13"><i class="fab fa-whatsapp font-18"></i> Написать менеджеру</a>
    </div>
    <div class="content">
        <div class="link-list link-list-3">
            <a href="#" class="round-medium shadow-tiny" data-menu="action-options">
                <i class="fas fa-money-bill-alt color-blue2-dark"></i>
                <span id="buyer">Баланс: <b><?php echo ($company_data['barter_balance'] - $reserved_for_deals) / 100 ?></b></span>
                <div id="buyer_balance"
                     data-buyer-balance="<?php echo ($company_data['barter_balance'] - $reserved_for_deals) / 100//($company_data['barter_balance'] - ($sum_all_orders + ($sum_all_orders / 100) * PERCENT_SYSTEM)) / 100; ?>"></div>
                <i class="fa fa-angle-right"></i>
            </a>
        </div>
    </div>
    <?php if ($company_data['credit_balance'] > 0): ?>
        <div class="content">
            <div class="link-list link-list-3">
                <a href="#" class="round-medium color-red2-dark shadow-tiny" data-menu="action-up_credit">
                    <i class="fas fa-credit-card color-red2-dark"></i>
                    <span id="credit">Кредит: <b><?php echo $company_data['credit_balance'] / 100; ?></b></span>
                    <div id="credit_balance"
                         data-credit-balance="<?php echo $company_data['credit_balance'] / 100; ?>"></div>
                    <i class="fa fa-angle-right"></i>
                </a>
            </div>
        </div>
    <?php endif; ?>


    <div class="content">
        <div class="content-title has-border border-highlight bottom-0">
            <h5>Последние обновления</h5>
        </div>
    </div>

    <div class="single-slider visible-slider2-small owl-carousel owl-dots-under bottom-10">
        <?php foreach ($all_news as $news): ?>
            <div class="caption round-medium shadow-small bottom-10">
                <div class="caption-bottom">
                    <a href="<?php echo site_url('/company/news/detail/news_detail?news_id=' . $news['news_id']); ?>">
                        <span class=" center-text uppercase ultrabold home__news__span"><?php echo $news['title']; ?></span>
                    </a>
                </div>
                <div class="caption-overlay bg-gradient"></div>
                <img class="caption-image owl-lazy"
                     data-src="<?php echo site_url('uploads/news_img/' . $news['img']); ?>">
            </div>
        <?php endforeach; ?>
    </div>

    <div class="divider divider-margins"></div>

    <div class="content">
        <div class="content-title has-border border-highlight bottom-0">
            <h5>Новые компании</h5>
        </div>
    </div>

    <div class="single-slider visible-slider1-small owl-carousel owl-no-dots">
        <?php if (!empty($last_new_companies)) { ?>
            <?php foreach ($last_new_companies as $last_company) { ?>
                <div class="content mod" data-menu="menu-instant-3" data-height="220"
                     onclick="open_company_detail(<?= $last_company['company_id'] ?>)">
                    <div class="content content-box round-medium shadow-small">
                        <div class="company__card">
                            <div class="company__company___img">
                                <img class="company__img"
                                     src="https://barter-business.ru/uploads/companys_logo/<?php echo $last_company['logo']; ?>"
                                     alt="">
                            </div>
                            <div class="company__company__title">
                                <span>
                                <?php echo mb_substr($last_company["company_name"],
                                    0, 65, "UTF-8"); ?></span>
                            </div>
                            <div class="company__company__desc">
                                <?php if (!empty($last_company['geo_code'])
                                    && !empty($company_data['geo_code'])
                                ): ?>
                                    <span class="font-16 ">Расстояние: <?php echo calculateTheDistance($company_data['geo_code'], $company['geo_code']) ?></span>
                                <?php endif; ?>
                                <div class="company__datail__footer">
                                    <?php
                                    $counter = 1;
                                    foreach ($last_new_companies_categories as $categories) {
                                        if ($counter > 5) break;
                                        if ($last_company['company_id'] == $categories['company_id']) {
                                            echo '<span class="category__company__view">' . $categories['category_title'] . ' </span> ';
                                            $counter++;
                                        }

                                    } ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php } ?>
        <?php } ?>
    </div>

    <div class="divider divider-margins"></div>

    <div class="content">
        <div class="content-title has-border border-highlight bottom-0">
            <h5>Рекомендуемые компании</h5>
        </div>
    </div>
    <?php if (!empty($recommended_company)) { ?>
        <?php foreach ($recommended_company as $company) { ?>

            <div style="padding: 10px" class="content round-medium shadow-small mod" data-menu="menu-instant-3"
                 onclick="open_company_detail(<?= $company['company_id'] ?>)">
                <div class="company__card">
                    <div class="company__company___img">
                        <img class="company__img"
                             src="https://barter-business.ru/uploads/companys_logo/<?php echo $company['logo']; ?>"
                             alt="">
                    </div>
                    <div class="company__company__title">

                                <span>
                                <?php echo mb_substr($company["company_name"],
                                    0, 65, "UTF-8"); ?></span>
                    </div>
                    <div class="company__company__desc">
                        <?php if (! empty($company['geo_code'])
                            && ! empty($company_data['geo_code'])
                        ): ?>
                            <span class="font-16 "><?php echo $company['adress'] ?></span><br>
                            <span class="font-16 ">Расстояние: <?php echo calculateTheDistance($company_data['geo_code'], $company['geo_code']) ?></span>
                        <?php endif; ?>
                        <div class="company__datail__footer">
                            <?php
                            $counter = 1;
                            foreach ($recommended_companies_categories as $categories) {
                                if ($counter > 5) break;
                                if ($company['company_id'] == $categories['company_id']) {
                                    echo '<span class="category__company__view">' . $categories['category_title'] . ' </span> ';
                                    $counter++;
                                }

                            } ?>
                        </div>

                    </div>
                </div>
            </div>
        <?php } ?>
    <?php } ?>
    <div class="demo-preloader" id="loader" style="display: none; width: 100%;">
        <div class="preload-spinner border-highlight"></div>
    </div>
    <div id="recom_comps"></div>
    <div class="divider divider-margins"></div>

</div>

<!-- Link to open the modal -->
<div class="menu-hider"></div>
<div id="action-options"
     class="menu-box menu-box-detached round-medium"
     data-menu-type="menu-box-bottom"
     data-menu-height="280"
     data-menu-effect="menu-parallax">

    <div class="page-title has-subtitle">
        <div class="page-title-left">
            <a href="#">Опции</a>
        </div>
        <div class="page-title-right">
            <a href="#" class="close-menu"><i class="fa fa-times-circle font-20 color-red2-dark"></i></a>
        </div>
    </div>

    <div class="content bottom-20">
        <div class="link-list link-list-1 link-list-icon-bg">
            <a href="#" data-menu="action-upbalance">

                <span>Пополнить баланс</span>
                <i class="fa fa-angle-right"></i>
            </a>
            <a href="#" data-menu="action-credit">

                <span>Взять кредит</span>
                <i class="fa fa-angle-right"></i>
            </a>

        </div>
    </div>
    <a href="#" class="close-menu button button-margins button-m round-medium bg-highlight shadow-small button-full">Закрыть</a>

</div>
<div id="action-credit"
     class="menu-box round-medium"
     data-menu-type="menu-box-modal"
     data-menu-height="250"
     data-menu-width="300">

    <div class="page-title has-subtitle">
        <div class="page-title-left">
            <a href="#">Кредит</a>

        </div>
        <div class="page-title-right">
            <a href="#" class="close-menu"><i class="fa fa-times-circle font-20 color-red2-dark"></i></a>
        </div>
    </div>

    <div class="content">
        <div class="input-style input-style-2 input-required">
            <span>Кредит</span>
            <input type="number" id="sum_credit" placeholder="Сумма кредит" min="10000">
        </div>
        <div class="clear"></div>
    </div>
    <a id="create_credit"
       onclick="take_credit(); return false;" href="#"
       class="close-menu button button-m button-full bg-highlight button-margins button-round-large shadow-small">Отправить</a>
</div>
<div id="action-upbalance"
     class="menu-box round-medium"
     data-menu-type="menu-box-modal"
     data-menu-height="250"
     data-menu-width="300">

    <div class="page-title has-subtitle">
        <div class="page-title-left">
            <a href="#">Пополнение</a>

        </div>
        <div class="page-title-right">
            <a href="#" class="close-menu"><i class="fa fa-times-circle font-20 color-red2-dark"></i></a>
        </div>
    </div>

    <div class="content">
        <div class="input-style input-style-2 input-required">
            <span>Сумма</span>
            <input id="restock_amount" type="number" placeholder="Сумма пополнения" min="10">
        </div>
        <div class="clear"></div>
    </div>
    <a id="create_credit"
       onclick="$(this).prop('disabled', true); $(this).text('Подождите...'); restock_balance();" href="#"
       class="button button-m button-full bg-highlight button-margins button-round-large shadow-small">Пополнить</a>
</div>
<div id="action-up_credit"
     class="menu-box round-medium"
     data-menu-type="menu-box-modal"
     data-menu-height="250"
     data-menu-width="300">

    <div class="page-title has-subtitle">
        <div class="page-title-left">
            <a href="#">Погасить кредит</a>

        </div>
        <div class="page-title-right">
            <a href="#" class="close-menu"><i class="fa fa-times-circle font-20 color-red2-dark"></i></a>
        </div>
    </div>

    <div class="content">
        <div class="input-style input-style-2 has-icon input-required">
            <i class="input-icon fas fa-minus"></i>
            <span>Сумма погашения</span>
            <input id="credit_rep" type="number" placeholder="">
            <div id="error_credit_rep"></div>
        </div>
        <div class="clear"></div>
    </div>
    <a href="#" id="submit_rep_credit"
       class="close-menu button button-m button-full bg-highlight button-margins button-round-large shadow-small"
       onclick="loan_repayment(this);">Отправить</a>
</div>

<script>

    $(document).ready(function () {
        if (parseInt(<?= $company_data['sub_status']?>) === 0 && parseInt(<?= $company_data['status']?>) === 2) {
            vex.dialog.open({
                message: 'Абонетская плата не оплачена!',
                buttons: [
                    $.extend({}, vex.dialog.buttons.YES, {
                        text: 'Оплатить',
                        className: 'create-deal button button-xs round-small shadow-large bg-highlight button-full bottom-10'
                    }),
                    $.extend({}, vex.dialog.buttons.NO, {
                        text: 'Отмена',
                        className: 'button button-xs round-small shadow-large button-border button-full border-highlight color-highlight bg-transparent'
                    })
                ],
                callback: function (data) {
                    if (!data) return;
                    window.location.replace("<?php echo site_url('company/abon_plata'); ?>");
                }
            });
        }
    });
</script>
<script>

    $(document).ready(function () {

        if (parseInt(<?= $company_data['hidden']?>) === 1) {
            vex.dialog.open({
                message: 'Ваша компания скрыта из каталога, Вы по-прежнему можете входить в свой аккаунт, просматривать каталог и пользоваться средствами своего счета на общих условиях, однако, ни найти вас, ни оплатить вам что-либо никто не сможет (кроме тех, кто уже добавил вас в Избранное). Для снятия данных ограничений - перезвоните по номеру 89677398359!',
                buttons: [
                    $.extend({}, vex.dialog.buttons.YES, {
                        text: 'Позвонить',
                        className: 'create-deal button button-xs round-small shadow-large bg-highlight button-full bottom-10'
                    }),
                    $.extend({}, vex.dialog.buttons.NO, {
                        text: 'Понятно',
                        className: 'button button-xs round-small shadow-large button-border button-full border-highlight color-highlight bg-transparent'
                    })
                ],
                callback: function (data) {
                    if (!data) return;
                    window.location.replace("tel:+79677398359");
                }
            });
        }
    });

    function load_recommended_comps() {

        $('#loader').show();

        $.ajax({
            url: base_url + 'company/company_ajax/ajax/recommended_companies_ajax',
            method: 'POST',
            data: {'token_form': $.cookie('csrf_barter')},
        }).done(function (data) {

            insert_csrf_hash(data);//функция берется из all_area_scripts!

            var result = validate_data_server_response(data);

            $('#loader').hide();

            if (result) {

                var company_geo = "<?= $company_data['geo_code'] ?>";

                $.each(data.data.comps, function (index, company) {

                    let comp = `<div style="padding: 10px" class="content round-medium shadow-small mod" data-menu="menu-instant-3"onclick="document.getElementById('comp_data_manual').click(); open_company_detail(${company.company_id});">
                    <div class="company__card">
                        <div class="company__company___img">
                            <img class="company__img" src="https://barter-business.ru/uploads/companys_logo/${company.logo}" alt="">
                        </div>
                    <div class="company__company__title"><span>${company.company_name.substr(0, 65)}</span></div>
                    <div class="company__company__desc">`;

                    if (company_geo != "" && company.geo_code != "" && company.geo_code != null) {
                        comp += `
                            <span class="font-16 ">${company.adress}</span><br>
                            <span class="font-16 ">Расстояние: ${calculateTheDistance(company_geo, company.geo_code)}</span>`
                    }

                    comp += '<div class="company__datail__footer">';

                    var counter = 0;
                    $.each(data.data.cats, function (index, cat) {
                        if (company.company_id == cat.company_id) {
                            comp += `<span class="category__company__view">${cat.category_title}</span>`;
                            counter++;
                        }
                        return (counter !== 5);
                    });//End each

                    comp += '</div></div></div></div>';

                    $('#recom_comps').append(comp);
                });//End each
            } else {
                $('#recom_comps').append(`<p class="text-center bolder font-14">${data.data.text_message}</p>`);
            }//end if
        });//end ajax
    }

    function calculateTheDistance(a, b) {
        let EARTH_RADIUS = 6372795;
        a = a.split(' ');
        b = b.split(' ');

        let lat1 = parseFloat(a[1]) * Math.PI / 180;
        let lat2 = parseFloat(b[1]) * Math.PI / 180;
        let long1 = parseFloat(a[0]) * Math.PI / 180;
        let long2 = parseFloat(b[0]) * Math.PI / 180;

        let cl1 = Math.cos(lat1);
        let cl2 = Math.cos(lat2);
        let sl1 = Math.sin(lat1);
        let sl2 = Math.sin(lat2);
        let delta = long2 - long1;
        let cdelta = Math.cos(delta);
        let sdelta = Math.sin(delta);

        let y = Math.sqrt(Math.pow(cl2 * sdelta, 2) + Math.pow(cl1 * sl2 - sl1 * cl2 * cdelta, 2));
        let x = sl1 * sl2 + cl1 * cl2 * cdelta;

        let dist = Math.atan2(y, x) * EARTH_RADIUS;

        if (dist > 1000) {
            dist = dist / 1000;
            dist = number_format(dist, '1', ',', ' ') + ' км.';
        } else {
            dist = number_format(dist, '1', ',', ' ') + " м.";
        }

        return dist;
    }
</script>

<?php
// Радиус земли

function calculateTheDistance($a, $b)
{
    define('EARTH_RADIUS', 6372795);
    define('M_PI', 3.14);
    $a = explode(' ', $a);
    $b = explode(' ', $b);
    // перевести координаты в радианы
    $lat1 = floatval($a[1] * M_PI / 180);
    $lat2 = floatval($b[1] * M_PI / 180);
    $long1 = floatval($a[0] * M_PI / 180);
    $long2 = floatval($b[0] * M_PI / 180);

    // косинусы и синусы широт и разницы долгот
    $cl1 = cos($lat1);
    $cl2 = cos($lat2);
    $sl1 = sin($lat1);
    $sl2 = sin($lat2);
    $delta = $long2 - $long1;
    $cdelta = cos($delta);
    $sdelta = sin($delta);

    // вычисления длины большого круга
    $y = sqrt(pow($cl2 * $sdelta, 2) + pow($cl1 * $sl2 - $sl1 * $cl2 * $cdelta,
            2));
    $x = $sl1 * $sl2 + $cl1 * $cl2 * $cdelta;

    //
    $ad = atan2($y, $x);
    $dist = $ad * EARTH_RADIUS;

    if ($dist > 1000) {
        $dist = $dist / 1000;
        $dist = number_format($dist, '1', ',', ' ') . ' км.';
    } else {
        $dist = number_format($dist, '1', ',', ' ') . " м.";
    }
    return $dist;
}
?>


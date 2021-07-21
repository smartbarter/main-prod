
<div class="kt-space-20"></div>
<div class="kt-body kt-grid__item kt-grid__item--fluid kt-grid kt-grid--hor kt-grid--stretch" id="kt_body">
    <div class="kt-content  kt-grid__item kt-grid__item--fluid kt-grid kt-grid--hor" id="kt_content">
        <div class="kt-container  kt-grid__item kt-grid__item--fluid">
            <div class="row">

                <div class="kt-subheader   kt-grid__item" id="kt_subheader">
                    <div class="kt-container ">
                        <div class="kt-subheader__main">
                            <?php if($company_detail['online_status']): ?>
                                <h3 class="kt-subheader__title text-success">
                                    Онлайн
                                </h3>
                            <?php else: ?>
                                <h3 class="kt-subheader__title text-danger">
                                    Оффлайн | Был(а) онлайн: <?= $company_detail['was_online'] ?> МСК
                                </h3>

                            <?php endif; ?>





                    </div>
                </div>
                </div>
                <div class="kt-container  kt-grid__item kt-grid__item--fluid">
                    <!--Begin:: Portlet-->
                    <div class="kt-portlet">
                        <div class="kt-portlet__body">
                            <div class="kt-widget kt-widget--user-profile-3">
                                <div class="kt-widget__top">
                                    <div class="kt-widget__media">
                                        <img src="https://barter-business.ru/uploads/companys_logo/<?php echo $company_detail['logo']; ?>" alt="image">
                                    </div>
                                    <div class="kt-widget__pic kt-widget__pic--danger kt-font-danger kt-font-bolder kt-font-light kt-hidden">
                                        JM
                                    </div>
                                    <div class="kt-widget__content">
                                        <div class="kt-widget__head">
                                            <div class="kt-widget__user">
                                                <a href="#" class="kt-widget__username">
                                                    <?php echo mb_substr($company_detail['company_name'], 0, 45,
                                                        "UTF-8"); ?>
                                                </a>
                                            </div>
                                            <?php

                                            if ($_SESSION['ses_company_data']['company_id'] !== $company_detail['company_id']) { ?>
                                            <div class="kt-widget__action">
                                                <button data-toggle="modal"
                                                   data-target=".deal_go" class="btn btn-brand btn-sm btn-upper">Заключить сделку</button>

                                            </div>
                                            <?php } ?>
                                        </div>



                                        <div class="kt-widget__info">
                                            <div class="kt-widget__desc">
                                                <?php if ($company_categories) {
                                                    $count = count($company_categories); ?>
                                                    <ul class="list_unstyled" style="list-style-type: none; display: flex; padding-left: 0;">

                                                        <?php for ($i = 0; $i < $count; $i++) { ?>

                                                            <li style="margin-right: 10px;">
                                                                <a href="<?php echo site_url('company/category?id=' . $company_categories[$i]['category_id']); ?>">
                                                                    <?php echo $company_categories[$i]['category_title']; ?>
                                                                </a>
                                                            </li>

                                                        <?php }//for ?>

                                                    </ul>

                                                <?php }//if ?></div>

                                        </div>
                                        <div class="kt-widget__bottom">
                                            <div class="kt-widget__item">
                                                <div class="kt-widget__icon">
                                                    <i class="flaticon-piggy-bank"></i>
                                                </div>
                                                <div class="kt-widget__details">
                                                    <span class="kt-widget__title">Лимит</span>
                                                    <span class="kt-widget__value"><span><?= $month_sales_detail['total'] > 0 ? ($month_sales_detail['total'] / 100) : 0 ?>/<?php echo $company_detail['month_limit'] / 100; ?></span>
                                                </div>
                                            </div>

                                            <div class="kt-widget__item">
                                                <div class="kt-widget__icon">
                                                    <i class="flaticon-confetti"></i>
                                                </div>
                                                <div class="kt-widget__details">
                                                    <span class="kt-widget__title">В избраном</span>
                                                    <span class="kt-widget__value"><span><?= $fave_count ?></span>
                                                </div>
                                            </div>
                                            <div class="kt-widget__item">
                                                <div class="kt-widget__icon">
                                                    <i class="flaticon2-reload"></i>
                                                </div>
                                                <div class="kt-widget__details">
                                                    <span class="kt-widget__title">Сделок</span>
                                                    <span class="kt-widget__value"><span><?= $company_detail['num_deals']; ?></span>
                                                </div>
                                            </div>
                                            <div class="kt-widget__item">
                                                <div class="kt-widget__icon">
                                                    <i class="flaticon-medical"></i>
                                                </div>
                                                <div class="kt-widget__details">
                                                    <span class="kt-widget__title">Просмотров</span>
                                                    <span class="kt-widget__value"><span><?= $views['total'] . ' (+' . $views['today'] . ')' ?></span>
                                                </div>
                                            </div>



                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                    <!--End:: Portlet-->

                    <div class="row">
                        <div class="col-xl-4">

                            <div class="kt-portlet">
                                <div class="kt-portlet__head">
                                    <div class="kt-portlet__head-label">
                                        <h3 class="kt-portlet__head-title">
                                            О компании
                                        </h3>
                                    </div>
                                    <div class="kt-portlet__head-toolbar">

                                        <div class="dropdown-menu dropdown-menu-right dropdown-menu-fit dropdown-menu-md">

                                        </div>
                                    </div>
                                </div>
                                <div class="kt-form kt-form--label-right">
                                    <div class="kt-portlet__body">
                                        <?php

                                        //преобразуем дату в нормальный вид
                                        $myDateTime = DateTime::createFromFormat('Y-m-d H:i:s', $company_detail['registr_date']);
                                        $newDateString = $myDateTime->format('d.m.Y г.');
                                        ?>
                                        <div class="form-group form-group-xs row">
                                            <label class="col-6 col-form-label">Дата регистрации:</label>
                                            <div class="col-6">
                                                <span class="form-control-plaintext kt-font-bolder"><?php echo $newDateString; ?></span>
                                            </div>
                                        </div>
                                        <div class="form-group form-group-xs row">
                                            <label class="col-6 col-form-label">Контактное лицо:</label>
                                            <div class="col-6">
                                                <span class="form-control-plaintext kt-font-bolder"><?php echo mb_convert_case($company_detail["contact_name"], MB_CASE_TITLE,
                                                        "UTF-8"); ?></span>
                                            </div>
                                        </div>
                                        <div class="form-group form-group-xs row">
                                            <label class="col-6 col-form-label">Контактный телефон:</label>
                                            <div class="col-6">
                                                <span class="form-control-plaintext"><span class="kt-font-bolder"><a style="color: black" href="tel:+<?php echo $company_detail['company_phone']; ?>">
                                +<?php echo $company_detail['company_phone']; ?></a></span> &nbsp;</span>
                                            </div>
                                        </div>
                                        <?php if (! empty($company_detail['company_site'])) { ?>
                                        <div class="form-group form-group-xs row">
                                            <label class="col-6 col-form-label">Сайт:</label>
                                            <div class="col-6">
                                                <span class="form-control-plaintext kt-font-bolder"><a href="<?php echo $company_detail['company_site']; ?>"><?php echo $company_detail['company_site']; ?></a></span>
                                            </div>
                                        </div>
                                        <?php } ?>
                                        <div class="form-group form-group-xs row">
                                            <label class="col-4 col-form-label">Адрес:</label>
                                            <div class="col-8">
                                            <span class="form-control-plaintext kt-font-bolder">
                                                    <a href="#"><?php echo $company_detail['adress']; ?></a>
                                                </span>
                                            </div>
                                        </div>

                                    </div>
                                    <div class="kt-portlet__foot">
                                        <div class="kt-form__actions kt-space-between">
                                            <a href="#" class="btn btn-label-brand btn-sm btn-bold" onclick="add_fave(<?= $company_detail['company_id'] ?>); return false;">Добавить в избранное</a>

                                            <a href="#" class="btn btn-clean btn-sm btn-bold" data-toggle="modal" data-target=".complain_detail  ">Пожаловаться</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!--End:: Portlet-->
                        </div>
                        <div class="col-xl-8">
                            <!--Begin:: Portlet-->
                            <div class="kt-portlet kt-portlet--tabs">
                                <div class="kt-portlet__head">
                                    <div class="kt-portlet__head-toolbar">
                                        <ul class="nav nav-tabs nav-tabs-space-lg nav-tabs-line nav-tabs-bold nav-tabs-line-3x nav-tabs-line-brand" role="tablist">
                                            <li class="nav-item">
                                                <a class="nav-link active" data-toggle="tab" href="#kt_apps_contacts_view_tab_1" role="tab">
                                                    Описание
                                                </a>
                                            </li>
                                            <li class="nav-item">
                                                <a class="nav-link" data-toggle="tab" href="#kt_apps_contacts_view_tab_2" role="tab">
                                                     Отзывы
                                                </a>
                                            </li>


                                        </ul>
                                    </div>
                                </div>
                                <div class="kt-portlet__body">
                                    <div class="tab-content kt-margin-t-20">
                                        <!--Begin:: Tab Content-->
                                        <div class="tab-pane " id="kt_apps_contacts_view_tab_2" role="tabpanel">

                                            <form>
                                                <div class="form-group">
                                                    <textarea class="form-control" id="review_comp" rows="3" placeholder="Текст отзыва"></textarea>
                                                </div>
                                                <div class="row">
                                                    <div class="col">
                                                        <a onclick="write_review_comp(<?= $company_detail['company_id'] ?>)" href="#" class="btn btn-label-brand btn-bold">Отправить</a>
                                                    </div>
                                                </div>
                                            </form>

                                            <div class="kt-separator kt-separator--space-lg kt-separator--border-dashed"></div>


                                            <div class="kt-notes kt-scroll kt-scroll--pull ps ps--active-y" data-scroll="true" style="height: 700px; overflow: hidden;">
                                                <div class="kt-notes__items">
                                                    <?php if (count($reviews) > 0): ?>
                                                    <?php foreach ($reviews as $review): ?>
                                                    <div class="kt-notes__item">
                                                        <div class="kt-notes__media">
                                                            <img class="kt-hidden-" src="<?= site_url('/uploads/companys_logo/' . $review['logo']) ?>" alt="image">
                                                        </div>
                                                        <div class="kt-notes__content">
                                                            <div class="kt-notes__section">
                                                                <div class="kt-notes__info">
                                                                    <a href="#" class="kt-notes__title">
                                                                        <?= $review['company_name'] ?>
                                                                    </a>

                                                                </div>

                                                            </div>
                                                            <span class="kt-notes__body">
                                                           <?= $review['text_rev'] ?>
                                                            </span>
                                                        </div>
                                                    </div>
                                                        <?php endforeach; ?>
                                                    <?php endif; ?>

                                                </div>
                                                <div class="ps__rail-x" style="left: 0px; bottom: 0px;"><div class="ps__thumb-x" tabindex="0" style="left: 0px; width: 0px;"></div></div><div class="ps__rail-y" style="top: 0px; height: 700px; right: -2px;"><div class="ps__thumb-y" tabindex="0" style="top: 0px; height: 300px;"></div></div></div>
                                        </div>

                                        <div class="tab-pane active" id="kt_apps_contacts_view_tab_1" role="tabpanel">
                                            <h5><?php echo str_replace("\n", '<br />',
                                                    $company_detail['description_company']); ?></h5>
                                        </div>

                                    </div>
                                </div>
                            </div>
                            <!--End:: Portlet-->
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>



<div class="modal fade deal_go" tabindex="-1" role="dialog"
     aria-labelledby="myLargeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered ">
        <div class="modal-content">
            
            <?php if ($company_data['sub_status'] == 0) {
                ?>
                <div class="row margin-b-30">
                    <div class="col-sm-12 col-md-3"></div>
                    <div class="col-sm-12 col-md-12">
                        <div class="widget bg_light widget bg_light ">
                            <div class="alert bg-danger alert-dismissible txt_center">
                                <strong>Внимание!</strong>
                                Вам необходимо произвести оплату сервиса!
                            </div>
                            <div class="padding-15">
                                <p class="font-18">
                                    <?php echo mb_convert_case($company_data['contact_name'],
                                        MB_CASE_TITLE, 'UTF-8'); ?>,
                                    уведомляем вас о том, что необходимо произвести оплату сервиса. Функционал
                                    вашего аккаунта ограничен. Сумма для оплаты <?php echo COST_SERVICE; ?> рублей,
                                    нажмите на кнопку чтобы оплатить сервис!

                                </p>
                                <a class="btn btn-teal btn-block" href="<?= site_url('/company/abon_plata/') ?>"
                                   target="_blank">Оплатить</a>
                            </div>
                        </div>
                    </div>
                </div>

                <?php
            } else { ?>
                <div class="modal-body">
                    <div id="deal_form">

                        <div class="input-group">
                            <div class="col-sm-12">
                                <div class="form-group form-float">
                                    <div class="form-line">
                                        <label class="form-label">Сумма заказа</label>
                                        <input class="form-control" id="sum_deal" name="sum_deal" type="number">

                                    </div>
                                    <div id="itog_sum_block">С учетом комиссии сервиса: <span id="itog_sum" class="my_info">0.00</span>
                                    </div>
                                    <div class="error" id="error_sum_deal"></div>
                                </div>
                            </div>
                            <div class="col-sm-12">
                                <div class="form-group form-float">
                                    <div class="form-line">
                                        <label class="form-label">Комментарий к заказу</label>
                                        <input type="text" id="comment_deal" name="comment_deal" maxlength="50"
                                               class="form-control">

                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-12">

                                <div>Использовать купон</div>
                                <select class="form-control" id="coupon" name="coupon" onchange="document.getElementById('coupon_alert').style.display = 'block'; checkBalance();">
                                    <option value="0" data-sum="0" selected="">Без купона</option>
                                    <?php foreach ($coupons as $coupon): ?>
                                        <option value="<?= $coupon['coupon_id'] ?>" data-sum="<?= $coupon['summa'] ?>">
                                            Сумма: <?= $coupon['summa']/100 ?> руб. Активен до: <?= (new DateTime($coupon['date_expire']))->format('d.m.Y H:i'); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <div id="coupon_alert" style="display: none">Внимание! Если сумма купона больше суммы заказа, остаток по купону сгорает!</div>
                            </div>
                            <div id="buyer_balance"
                                 data-buyer-balance="<?php echo ($company_data['barter_balance'] - ($sum_all_orders + ($sum_all_orders / 100) * PERCENT_SYSTEM)) / 100; ?>"></div>
                        </div><!-- /input-group -->

                    </div>
                </div>
                <div class="modal-footer" style="background: #374afb;     margin-top: 15px; padding: 0;">
                    <button class="btn btn-border-radius "  style="width: 100%; font-size: 16px; color: white" type="button" id="create_deal"
                            onclick="create_new_deal('<?php echo $company_detail['for_deals_id']; ?>', <?php echo $company_detail['company_id']; ?>); return false;">
                        Оформить заказ
                    </button>
                </div>
            <?php } ?>
        </div>
    </div>
</div>
<div class="modal fade complain_detail" tabindex="-1" role="dialog"
     aria-labelledby="myLargeModalLabel" aria-hidden="true">
    <div class="modal-dialog ">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"
                        aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>

            </div>
            <div class="modal-body">
                <div>
                    <h3>Пожаловаться на компанию</h3>

                    <textarea name="review" id="review" rows="4" class="form-control no-resize"
                              placeholder="Опишите причину жалобы"></textarea>
                </div>
            </div>
            <div class="modal-footer">

                <button type="button"
                        onclick="write_review(<?= $company_detail['company_id'] ?>)"
                        class="btn btn-primary">Отправить отзыв
                </button>
            </div>
        </div>
    </div>
</div>



























<script>
    ymaps.ready(init);
    <?php
    $code = explode(' ', $company_detail['geo_code']);
    $code = array_reverse($code);
    $code = implode(',', $code);
    ?>
    function init() {
        var myMap = new ymaps.Map('map', {
            center: [<?= $code ?>],
            zoom: 16,
        }, {
            searchControlProvider: 'yandex#search',
        });
        myMap.geoObjects.add(new ymaps.Placemark([<?= $code ?>], {
            balloonContentHeader: "<?= str_replace('"', "'", $company_detail['company_name']) ?>",
            balloonContentFooter: "<?= str_replace('"', "'", $company_detail['adress']) ?>",
            preset: 'islands#icon',
            iconColor: '#0095b6',
        }));
    };
</script>

<script>
    var revInProgress = false;
    function write_review(to) {
        if(!revInProgress) {
            let data = {
                'text': $('#review').val(),
                'token_form': $('#token_form').val(),
                'to': to
            };

            $.ajax({
                url: base_url + 'company/company_ajax/ajax/review',
                type: 'POST',
                dataType: 'JSON',
                data: data,
                cache: false,
                beforeSend: function () {
                    revInProgress = true;
                },
                success: function (data) {
                    revInProgress = false;
                    if (data.status === 'success') {
                        swal("Отлично!", data.text_message, "success");
                        setTimeout(function () {
                            reload_page();
                        }, 1500);
                    } else {
                        swal("Ошибка!", data.text_message, "error");
                    }

                    //вставляем хэш
                    insert_csrf_hash(data);
                }
            });
        }
        else console.log("memes");
    }
</script>
<script type="text/javascript">VK.init({apiId: 6667108, onlyWidgets: true});</script>


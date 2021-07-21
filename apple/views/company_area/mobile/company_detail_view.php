<section class="content">
    <?php $sdelki_sum = ($company_detail['month_limit'] / 100)
        / ($company_detail['dostupno_dlya_sdelok'] / 100);
    $proc_sdelok = 100 / $sdelki_sum;
    $proc_sdelok = 100 - $proc_sdelok;
    ?>

    <div class="row clearfix">
        <div class="col-lg-4 col-md-12 order-2">
            <div class="card shadow_material">
                <div class="m-b-20">
                    <div class="contact-grid">
                        <div class="profile-header bg-dark">

                            <div class="user-name"><?php echo mb_substr($company_detail['company_name'], 0, 45,
                                    "UTF-8"); ?></div>
                            <div class="name-center"><?php if ($company_categories) {
                                    $count = count($company_categories); ?>
                                    <ul class="list_unstyled">

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
                        <img src="https://barter-business.ru/uploads/companys_logo/<?php echo $company_detail['logo']; ?>"
                             class="user-img" alt="">
                        <p>
                            <?php echo $company_detail['adress']; ?>
                        </p>
                        <div>
                                    <span class="phone">
                                        <i class="material-icons">phone</i><a style="color: black"
                                                                              href="tel:+<?php echo $company_detail['company_phone']; ?>"> +<?php echo $company_detail['company_phone']; ?></a></span>
                        </div>
                        <div class="row">
                            <div class="col-4">
                                <h5><?= $fave_count ?></h5>
                                <small>Лайков</small>
                            </div>
                            <div class="col-4">
                                <h5><?= $company_detail['num_deals']; ?></h5>
                                <small>Сделок</small>
                            </div>
                            <div class="col-4">
                                <h5><?= $views['total'] . ' (+' . $views['today'] . ')' ?></h5>
                                <small>Просмотров</small>
                            </div>
                        </div>
                        <?php

                        if ($_SESSION['ses_company_data']['company_id'] !== $company_detail['company_id']) { ?>
                            <button class="btn-hover btn-border-radius color-3"
                                    style="width: 80%; margin: 10px 0px 0px 0px;" data-toggle="modal"
                                    data-target=".deal_go">Заключить сделку
                            </button>

                        <?php } ?>
                    </div>
                </div>
            </div>
            <div class="card shadow_material col-12">
                <div class="m-b-20 profile-tab-box font-18" style="text-align: center">
                    <span class="text font-17 m-b-5"">Сумма сделок за месяц:</span><span
                            class="m-b-5 font__grad__bar"> <?= $month_sales_detail['total'] > 0 ? ($month_sales_detail['total'] / 100) : 0 ?></span><br>
                    <span class="text font-17">Лимит:</span><span
                            class="m-b-5 font__grad__bar font-17"> <?php echo $company_detail['month_limit'] / 100; ?></span>
                </div>
            </div>
            <div class="card shadow_material col-12" style="padding: 10px">
                <span class="text font-20 m-b-5 font__grad__bar align-center">Поделиться компанией</span>
                <div class="sharethis-inline-share-buttons"></div>
            </div>

            <div class="card shadow_material">
                <ul class="nav nav-tabs">
                    <li class="nav-item m-l-10">
                        <a class="nav-link" data-toggle="tab" href="#about">О компании</a>
                    </li>
                    <li class="nav-item m-l-10">
                        <a class="nav-link active" data-toggle="tab" href="#skills">Связь</a>
                    </li>
                </ul>
                <div class="tab-content">
                    <div class="tab-pane body" id="about">
                        <?php

                        //преобразуем дату в нормальный вид
                        $myDateTime = DateTime::createFromFormat('Y-m-d H:i:s', $company_detail['registr_date']);
                        $newDateString = $myDateTime->format('d.m.Y г.');
                        ?>
                        <small class="text-muted">Дата регистрации:</small>
                        <p><?php echo $newDateString; ?></p>
                        <hr>
                        <small class="text-muted">Контактное лицо:</small>
                        <p><?php echo mb_convert_case($company_detail["contact_name"], MB_CASE_TITLE,
                                "UTF-8"); ?></p>
                        <hr>
                        <small class="text-muted">Контактный телефон:</small>
                        <p><a style="color: black" href="tel:+<?php echo $company_detail['company_phone']; ?>">
                                +<?php echo $company_detail['company_phone']; ?></a></p>
                        <hr>
                        <?php if (! empty($company_detail['company_site'])) { ?>
                            <small class="text-muted">Сайт:</small>
                            <p>
                                <a href="<?php echo $company_detail['company_site']; ?>"><?php echo $company_detail['company_site']; ?></a>
                            </p>
                            <hr>
                        <?php } ?>

                        <small class="text-muted">Адрес:</small>
                        <p><?php echo $company_detail['adress']; ?></p>
                        <hr>
                        <small class="text-muted">Сверх лимита:</small>
                        <p class="col-green"><?php echo $company_detail['sverh_limit']; ?>%</p>
                        <hr>
                        <?php

                        if ($company_detail['status'] == 2) {
                            $class = "col-green";
                            $status = "Активна";
                        } else {
                            if ($company_detail['status'] == 3) {
                                $class = "col-red";
                                $status = "Не оплаченная";
                            } else {
                                $class = "col-red";
                                $status = "Не активна";
                            }
                        }
                        ?>
                        <small class="text-muted">Статус компании:</small>
                        <p class="<?php echo $class; ?>"><?php echo $status; ?></p>
                        <hr>
                        <button style="padding: 0;"
                                class="m-b-15 width-per-100 btn btn-outline-warning btn-border-radius" id="add_fave"
                                onclick="add_fave(<?= $company_detail['company_id'] ?>); return false;">
                            Добавить в избранное
                        </button>

                        <button style="padding: 0;" class="width-per-100 btn btn-outline-danger btn-border-radius"
                                data-toggle="modal" data-target=".complain_detail  ">
                            Пожаловаться
                        </button>
                    </div>
                    <div class="tab-pane body active" id="skills">
                        <a href="tel:+<?php echo $company_detail['company_phone']; ?>">
                            <button type="button" class="btn-border-radius btn bg-green waves-effect m-b-15"
                                    style="height: 36px;width: 100%;line-height: 36px;">
                                <i class="material-icons">phone</i>
                                <span>Позвонить</span>
                            </button>
                        </a>

                        <a href="<?php echo site_url('company/chat?im=' . $company_detail['company_id']); ?>">
                            <button type="button" class="btn-border-radius btn bg-light-green waves effect"
                                    style="height: 36px;width: 100%;line-height: 36px;">
                                <i class="material-icons">chat</i>
                                <span>Написать</span>
                            </button>
                        </a>
                        <div class="col-12" style="display: flex">
                            <div class="col-4"><a
                                        href="https://api.whatsapp.com/send?phone=<?php echo $company_detail['company_phone']; ?>"><img
                                            src="<?= base_url() . '/assets/images/all_area/whatsapp.svg' ?>"
                                            height="150px"></a>
                            </div>
                            <div class="col-4">
                                <a href="<?= $company_detail['social_inst'] ?>">
                                    <img src="<?= base_url() . '/assets/images/all_area/instagram.svg' ?>"
                                         height="150px"></a>
                            </div>
                            <div class="col-4"><a href="<?= $company_detail['social_vk'] ?>">
                                    <img src="<?= base_url() . '/assets/images/all_area/vk.svg' ?>" height="150px"></a>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-8 col-md-12 order-1">
            <div class="card shadow_material">
                <div class="profile-tab-box">
                    <div class="p-l-20">
                        <ul class="nav ">
                            <li class="nav-item tab-all">
                                <a class="nav-link active show" href="#project" data-toggle="tab">Описание</a>
                            </li>
                            <?php if (! empty($products)): ?>
                                <li class="nav-item tab-all">
                                    <a class="nav-link" href="#userproduct" data-toggle="tab">Товары</a>
                                </li>
                            <?php endif; ?>
                            <li class="nav-item tab-all">
                                <a class="nav-link" href="#userreviews" data-toggle="tab">Отзывы</a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="tab-content">
                <div role="tabpanel" class="tab-pane active" id="project" aria-expanded="true">
                    <div class="row clearfix">
                        <div class="col-lg-12 col-md-12 col-sm-12">
                            <div class="card shadow_material project_widget">
                                <div class="header">
                                    <h2>Описание</h2>
                                </div>
                                <div class="body">

                                    <p><?php echo str_replace("\n", '<br />',
                                            $company_detail['description_company']); ?>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-12 col-md-12 col-sm-12" style="    margin-bottom: 330px;">
                            <div class="card shadow_material project_widget">
                                <div id="map" style="width: 100%; height: 400px"></div>
                            </div>
                        </div>

                    </div>
                </div>
                <div role="tabpanel" class="tab-pane" id="userreviews" aria-expanded="false">
                    <div class="card shadow_material">
                        <div class="header">
                            <h2><strong>Оставить отзыв</strong></h2>
                        </div>
                        <div class="body">
                            <textarea class="advert_comp shadow_material" name="review_comp" id="review_comp" cols="60"
                                      rows="30"></textarea>
                            <button class="btn btn-default float-right m-t-10"
                                    onclick="write_review_comp(<?= $company_detail['company_id'] ?>)">Отпрвить
                            </button>
                        </div>

                    </div>
                    <div class="card shadow_material">
                        <div class="header">
                            <h2><strong>Отзывы</strong></h2>
                        </div>
                        <?php if (count($reviews) > 0): ?>
                            <?php foreach ($reviews as $review): ?>
                        <div class="body">
                            <div class="review-block">
                                <div class="row">


                                            <div class="review-img" style="padding-top: 0">
                                                <img src="<?= site_url('/uploads/companys_logo/' . $review['logo']) ?>"
                                                     alt="" width="50px" height="50px">
                                            </div>
                                            <div class="col">
                                                <h6 class="m-b-15"><?= $review['company_name'] ?>
                                                </h6>

                                                <p class="m-t-15 m-b-15"><?= $review['text_rev'] ?></p>
                                                </a>

                                            </div>


                                </div>


                            </div>
                        </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>

                </div>
                <div role="tabpanel" class="tab-pane" id="userproduct" aria-expanded="false">

                    <?php if (! empty($products['products'])): ?>

                    <div class="tab-content" id="products_area">

                        <div class="row clearfix ">

                            <?php foreach ($products['products'] as $product): ?>
                                <div class="col-lg-4 col-md-4 col-sm-6 col-xs-6">
                                    <div class="box-part text-center shadow_material">
                                        <div class="product_image">
                                            <?php if (isset($product['image'])): ?>
                                                <img src="https://barter-business.ru/uploads/products_image/<?= $product['image'] ?>"
                                                     height="150px"
                                                     style="width: 100%">
                                            <?php else: ?>
                                                <img src="https://barter-business.ru/uploads/products_image/default.svg"
                                                     height="150px">
                                            <?php endif ?>
                                        </div>
                                        <div class="product_title">
                                            <p class="font-16 align-left"><?= $product['title'] ?></p>
                                        </div>
                                        <div class="product_price">
                                            <div class="fa-pull-left">Цена:<br><span
                                                        class="font-22 font__grad__bar"><?= $product['price'] ?></span>
                                            </div>

                                        </div>
                                    </div>
                                </div>


                            <?php endforeach; ?>


                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade deal_go" tabindex="-1" role="dialog"
         aria-labelledby="myLargeModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered ">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal"
                            aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <?php if ($company_data['status'] == 3) {
                    ?>
                    <div class="row margin-b-30">
                        <div class="col-sm-12 col-md-3"></div>
                        <div class="col-sm-12 col-md-12">
                            <div class="widget bg_light widget bg_light "
                            >
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
                                            <input class="form-control" id="sum_deal" name="sum_deal" type="number">
                                            <label class="form-label">Сумма заказа</label>
                                        </div>
                                        <div id="itog_sum_block">С учетом комиссии сервиса: <span id="itog_sum"
                                                                                                  class="my_info">0.00</span>
                                        </div>
                                        <div class="error" id="error_sum_deal"></div>
                                    </div>
                                </div>
                                <div class="col-sm-12">
                                    <div class="form-group form-float" style="margin-bottom: 0px">
                                        <div class="form-line">
                                            <input type="text" id="comment_deal" name="comment_deal" maxlength="50"
                                                   class="form-control">
                                            <label class="form-label">Комментарий к заказу</label>
                                        </div>
                                    </div>
                                </div>

                                <div id="buyer_balance"
                                     data-buyer-balance="<?php echo ($company_data['barter_balance'] - ($sum_all_orders + ($sum_all_orders / 100) * PERCENT_SYSTEM)) / 100; ?>"></div>

                            </div><!-- /input-group -->
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-border-radius"  style="width: 100%" type="button" id="create_deal"
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
</section>

<script>
    ymaps.ready(init);
    <?php
    $code = explode(' ', $company_detail['geo_code']);
    $code = array_reverse($code);
    $code = implode(',', $code);
    ?>
    function init() {
        var myGeocoder = ymaps.geocode([<?= $code ?>], {
            'json': true,
            'results': 1,
        });
        myGeocoder.then(
            function (res) {
                var myMap = new ymaps.Map('map', {
                    center: [<?= $code ?>],
                    zoom: 16,
                }, {
                    searchControlProvider: 'yandex#search',
                });
                myMap.geoObjects.add(new ymaps.Placemark([<?= $code ?>], {
                    balloonContentHeader: "<?= str_replace('"', "'", $company_detail['company_name']) ?>",
                    balloonContentFooter: res.GeoObjectCollection.featureMember[0].GeoObject.metaDataProperty.GeocoderMetaData.text,
                    preset: 'islands#icon',
                    iconColor: '#0095b6',
                }));
            },
        );
    };
</script>
<script>
    function write_review(to) {
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
            success: function (data) {

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
</script>
<script>
    function write_review_comp(to) {
        let data = {
            'text': $('#review_comp').val(),
            'token_form': $('#token_form').val(),
            'to': to
        };

        $.ajax({
            url: base_url + 'company/company_ajax/ajax/review_comp',
            type: 'POST',
            dataType: 'JSON',
            data: data,
            cache: false,
            success: function (data) {

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
</script>
<script type="text/javascript">VK.init({apiId: 6667108, onlyWidgets: true});</script>
<script type="text/javascript">
    VK.Widgets.Comments('vk_comments');
</script>
<script>
    jQuery(document).ready(function () {
        function classFunction() {
            if (jQuery('body').width() < 900) {
                jQuery('.order-1').removeClass('order-1').addClass('order-3');
            } else {
                jQuery('.order-3').removeClass('order-3').addClass('order-1');
            }
        }

        classFunction();
        jQuery(window).resize(classFunction);
    });
</script>

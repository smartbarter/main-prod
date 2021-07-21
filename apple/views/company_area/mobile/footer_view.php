<input type="hidden" id="token_form" class="hidden_field token_form"
       name="<?php echo $this->security->get_csrf_token_name(); ?>"
       value="<?php echo $this->security->get_csrf_hash(); ?>" />


<script type="text/javascript" src="<?php echo site_url('assets/js/mobile/jquery.cookie.js'); ?>"></script>
<script type="text/javascript" src="<?php echo site_url('assets/js/mobile/plugins.js'); ?>?v3"></script>
<script type="text/javascript" src="<?php echo site_url('assets/js/mobile/mobile.js'); ?>?v4"></script>
<script type="text/javascript" src="<?php echo site_url('assets/js/all_area/all_area_scripts.js'); ?>?v6"></script>

<a href="#" id="comp_data_manual" data-menu="menu-instant-3" style="display: none"></a>
</body>
<div class="snackbars  ">
    <!-- Only manual snackbars get added here-->
</div>
<div id="menu-instant-3"
     class="menu-box"
     data-menu-type="menu-box-right"
     data-menu-height="100%"
     data-menu-width="100%"
     data-menu-effect="menu-over">

    <div data-height="60" class="caption shadow-tiny" >
        <div class="caption-top left-10 top-10 right-10">
            <div class="caption-author-left">
                <a href="#" class="icon icon-s float-left" style="color:#111; " ><i style="font-size: 18px;width: 50px" id="likes" onclick="change_fave(comp_id)" class="far fa-heart"></i></a>
                <a href="#" id="close_modal_btn" class="close-menu icon icon-xs float-right"><i class="fa fa-times-circle color-red2-light font-24"></i></a>
            </div>
        </div>
    </div>
    <div>
        <div id="company_loader" class="loader-main loader-inactive"><div class="preload-spinner border-highlight"></div></div>
    </div>
    <div>
        <div class="content">
            <div id="status_online">-</div>
            <div id="was_online">Был(а) онлайн: -</div>
            <span  class="left-text bottom-5 load1">Работаю сверхлимита <span id="sverhlimit" style="padding: 3px 6px;background: #504de4;color: #fff;border-radius: 19px;"></span> бартер</span>

        </div>
        <div class="content">
            <h2 id="company_name" class="left-text bottom-5 load1"></h2>
        </div>

        <div class="profile-header">
            <div class="profile-left">
                <p id="company_limit" class="load1"></p>
                <p id="company_summ_deal" class="load1"></p>
                <p id="deals_count" class="load1"></p>
                <p id="views_count" class="load1"></p>
                <div class="clear"></div>
            </div>
            <div class="profile-right">
                <a href="#">
                    <img id="company_logo" src="" class="preload-image shadow-huge">
                </a>
            </div>
        </div>
        <div class="content">
            <div class="one-half">
                <a id="company_whatsapp" href="#"
                   class="button button-xs round-small shadow-large button-border button-full border-highlight color-highlight bg-transparent"><i class="fab fa-whatsapp font-15"></i> Написать</a>
            </div>
            <div class="one-half last-column">
                <a href="#" id="call_button"
                   class="button button-xs round-small shadow-large button-border button-full border-highlight color-highlight bg-transparent"><i class="fas fa-phone-volume font-15"></i> Позвонить</a>
            </div>
            <a href="#" class="make-deal button button-xs round-small shadow-large bg-highlight button-full bottom-30"
               style="width: 100%">Заключить сделку</a>
        </div>
        <div class="clear"></div>
        <div class="content">
            <div class="tab-controls tab-animated tabs-medium tabs-rounded"
                 data-tab-items="4"
                 data-tab-active="bg-blue1-dark">
                <a href="#" data-tab-active data-tab="tab-5">Описание</a>
                <a href="#" data-tab="tab-6">Контакты</a>
                <a href="#" data-tab="tab-7">Товары</a>
                <a href="#" data-tab="tab-8">Отзывы</a>
            </div>
            <div class="clear bottom-15"></div>
            <div class="tab-content" id="tab-5" style="border: 1px solid transparent;">
                <span class="font-16" id="description"></span>
                <a href="#" id="show_more" onclick="show_more()">Загрузить полностью</a>
                <div class="divider" style="margin-top: 10px;margin-bottom: 15px;"></div>
                <div id="map_company" style="width: 100%; height: 300px"></div>
            </div>
            <div class="tab-content" id="tab-6" style="border: 1px solid transparent;">
                <p><a href="#" id="company_phone" style="color: black; font-size: 16px" href=""></a></p>
                <p id="company_adress" style="color: black; font-size: 16px">Адрес</p>
                <p id="company_contact_name" style="color: black; font-size: 16px">Контактное лицо:</p>
                <p><a href="#" id="company_instagram" style="color: black; font-size: 16px"></a></p>
                <p><a href="#" id="company_vk" style="color: black; font-size: 16px"></a></p>
                <p><a href="#" id="company_website" style="color: black; font-size: 16px"></p>
            </div>
            <div class="tab-content" id="tab-7" style="border: 1px solid transparent;">
                <h2 class="bottom-15"><strong id="products_head">Товары (0)</strong></h2>
                <div id="products_table"></div>
                <a href="#"
                   id="button_load_products" style="width: 100%"
                   class="button button-xs round-small shadow-large bg-highlight button-full bottom-30"
                   onclick="load_products(products_num, products_num + products_PER_LOAD)">Загрузить еще</a>
                <p></p>
            </div>
            <div class="tab-content" id="tab-8" style="border: 1px solid transparent;">
                <h2 class="bottom-15"><strong>Оставить отзыв</strong></h2>
                <div class="input-style input-style-2">
                    <textarea id="review_text" placeholder="Отзыв"></textarea>
                </div>
                <a href="#" style="width: 100%"
                   class="button button-xs round-small shadow-large bg-highlight button-full bottom-30"
                   onclick="write_review_comp(comp_id)">Отправить</a>

                <p></p>
                <h2 class="bottom-15"><strong id="reviews_head">Отзывы (0)</strong></h2>
                <div id="review_table">
                </div>
                <a href="#"
                   id="button_load_reviews" style="width: 100%"
                   class="button button-xs round-small shadow-large bg-highlight button-full bottom-30"
                   onclick="load_reviews(review_num, review_num + REVIEWS_PER_LOAD)">Загрузить еще</a>
                <p></p>
            </div>
            <div class="divider" style="margin-top: 15px; margin-bottom: 10px"></div>
            <button style="width: 100%;"
                    class="button button-s round-small shadow-large bg-highlight button-full bg-gradient-red2 font-13 make-complaint">
                <i class="fas fa-exclamation-triangle font-20"></i> Сообщить о нарушении
            </button>
        </div>
    </div>
</div>
<script>
    var DESCR_SIZE = 180;
    var REVIEWS_PER_LOAD = 10;
    var products_PER_LOAD = 10;
    var inProgress = false;
    var descr = "";
    var myMap = null;
    var comp_id = null;
    var reviews = [];
    var review_num = 0;
    var products = [];
    var products_num = 0;
    var likes = 0;
    var account_balance = 0;

    // $(document).ready(function () {
    //     if (window.history && window.history.pushState) {
    //         $('#comp_data_manual').on('click', function (e) {
    //             window.history.pushState('forward', null, '#modal');
    //         });
    //
    //         $(window).on('popstate', function () {
    //             $('#close_modal_btn').click();
    //         });
    //     }
    // });

    function open_company_detail(company_id, attempt = 0) {

        document.getElementById("show_more").style.display = "none";
        document.getElementById("company_logo").src = "https://barter-business.ru/assets/images/all_area/loading.png";

        if (!inProgress) {
            var data = {
                'token_form': $.cookie('csrf_barter'),
                'company_id': company_id,
            };
            $.ajax({
                url: base_url + 'company/cabinet/company_detail',
                type: 'POST',
                dataType: 'JSON',
                data: data,
                cache: false,
                error: function () {
                    if (attempt > 3) {
                        vex.dialog.alert("Ошибка загрузки данных! Страница перезагрузится.");
                        setTimeout(function () {
                            reload_page();
                        }, 1500);
                    } else {
                        attempt++;
                        console.log('Loading failed. Attempt ' + attempt + '...')
                        //open_company_detail(company_id, attempt);
                        setTimeout(function () {
                            open_company_detail(company_id, attempt);
                        }, 500);
                    }
                },
                beforeSend: function () {
                    $('#company_loader').removeClass('loader-inactive');
                },
            }).done(function (data) {

                    insert_csrf_hash(data);
                    validate_data_server_response(data);

                    account_balance = (data.data.company_data.barter_balance - data.data.company_data.reserved_for_deals) / 100;
                    comp_id = data.data.company_detail.company_id;
                    likes = data.data.fave_count;

                    document.getElementById("likes").className = (data.data.isliked == 1 ? "fa fa-heart color-red2-light" : "far fa-heart");
                    document.getElementById("likes").innerHTML = " " + likes;
                    document.getElementById("company_name").innerHTML = data.data.company_detail.company_name;
                    descr = data.data.company_detail.description_company.replace("\n", "<br />");
                    if (descr.length > DESCR_SIZE) {
                        document.getElementById("description").innerHTML = descr.substr(0, DESCR_SIZE) + "...";
                        document.getElementById("show_more").style.display = "inline";
                    } else {
                        document.getElementById("description").innerHTML = descr;
                    }
                    document.getElementById("company_logo").src = "https://barter-business.ru/uploads/companys_logo/" + data.data.company_detail.logo; //https://barter-business.ru/uploads/companys_logo/
                    document.getElementById("company_limit").innerHTML = "Лимит: " + (data.data.company_detail.month_limit / 100);
                    document.getElementById("company_summ_deal").innerHTML = "Сумма сделок за месяц: " + (data.data.month_sales_detail.total > 0 ? data.data.month_sales_detail.total / 100 : 0);
                    document.getElementById("deals_count").innerHTML = "Сделок: " + data.data.company_detail.num_deals;
                    document.getElementById("sverhlimit").innerHTML = data.data.company_detail.sverh_limit + "%";
                    document.getElementById("views_count").innerHTML = "Просмотров: " + data.data.views.total + " (+" + data.data.views.today + ")";
                    document.getElementById("company_phone").href = "tel:+" + data.data.company_detail.company_phone;
                    document.getElementById("company_phone").innerHTML = "Телефон: +" + data.data.company_detail.company_phone;
                    document.getElementById("call_button").href = "tel:+" + data.data.company_detail.company_phone;
                    //document.getElementById("chat_button").href = "<?php echo site_url('company/chat?im=')?>" + data.data.company_detail.company_id;
                    document.getElementById("company_adress").innerHTML = "Адрес: " + data.data.company_detail.adress;
                    document.getElementById("company_contact_name").innerHTML = "Контакное лицо: " + data.data.company_detail.contact_name;
                    document.getElementById("company_whatsapp").href = "https://api.whatsapp.com/send?phone=" + data.data.company_detail.company_phone;

                    if (data.data.company_detail.online_status == 0) {
                        document.getElementById("status_online").innerHTML = "Не в сети";
                        document.getElementById("status_online").style.color = 'red';
                        document.getElementById("was_online").style.display = 'block';
                        document.getElementById("was_online").innerHTML = "Был онлайн: " + data.data.company_detail.was_online + " МСК";
                    } else {
                        document.getElementById("status_online").innerHTML = "Онлайн";
                        document.getElementById("status_online").style.color = 'green';
                        document.getElementById("was_online").style.display = 'none';
                    }
                    if (!data.data.company_detail.social_inst || data.data.company_detail.social_inst == "") {
                        document.getElementById("company_instagram").innerHTML = "Инстаграм: -";
                        document.getElementById("company_instagram").href = "#";
                    } else {
                        document.getElementById("company_instagram").href = data.data.company_detail.social_inst;
                        document.getElementById("company_instagram").innerHTML = "Инстаграм: " + data.data.company_detail.social_inst;
                    }

                    if (!data.data.company_detail.social_vk || data.data.company_detail.social_vk == "") {
                        document.getElementById("company_vk").innerHTML = "ВК: -";
                        document.getElementById("company_vk").href = "#";
                    } else {
                        document.getElementById("company_vk").href = data.data.company_detail.social_vk;
                        document.getElementById("company_vk").innerHTML = "ВК: " + data.data.company_detail.social_vk;
                    }

                    if (!data.data.company_detail.company_site || data.data.company_detail.company_site == "") {
                        document.getElementById("company_website").innerHTML = "Сайт: -";
                        document.getElementById("company_website").href = "#";
                    } else {
                        document.getElementById("company_website").href = data.data.company_detail.company_site;
                        document.getElementById("company_website").innerHTML = "Сайт: " + data.data.company_detail.company_site;
                    }

                    //Таблица отзывов
                    reviews = data.data.reviews.slice();
                    review_num = 0;

                    $('#review_table').empty();
                    var rev_len = reviews.length;
                    if (rev_len > 0) {
                        if (rev_len > REVIEWS_PER_LOAD) document.getElementById("button_load_reviews").style.display = "block";
                        document.getElementById("reviews_head").innerHTML = "Отзывы (" + rev_len + ")";
                        load_reviews(review_num, review_num + REVIEWS_PER_LOAD);
                    } else {
                        document.getElementById("reviews_head").innerHTML = "Отзывы (0)";
                        document.getElementById("review_table").innerHTML = "Нет отзывов";
                        document.getElementById("button_load_reviews").style.display = "none";
                    }

                    if(data.data.products) {
                        products = data.data.products.products;
                    }
                    else {
                        products = [];
                    }
                    products_num = 0;

                    $('#products_table').empty();
                    var products_len = products.length;
                    if (products_len > 0) {
                        if (products_len > products_PER_LOAD) document.getElementById("button_load_products").style.display = "block";
                        document.getElementById("products_head").innerHTML = "Товары (" + products_len + ")";
                        load_products(products_num, products_num + products_PER_LOAD);
                    } else {
                        document.getElementById("products_head").innerHTML = "Товары (0)";
                        document.getElementById("products_table").innerHTML = "Нет товаров";
                        document.getElementById("button_load_products").style.display = "none";
                    }

                    //Яндекс карты: данные
                    if (myMap != null) {
                        myMap.destroy()
                    }
                    if (data.data.company_detail.geo_code != null) {
                        var geo_code = data.data.company_detail.geo_code.split(' ');
                        geo_code = geo_code.reverse();
                        ymaps.ready(init_comp_map(geo_code, data.data.company_detail.company_name, data.data.company_detail.adress));
                    }
                    //Когда все загрузилось
                    $('#company_loader').addClass('loader-inactive');
                    inProgress = false;
                },
            ); // ajax
        } //end if
    };

    function show_more() {
        document.getElementById("description").innerHTML = descr;
        document.getElementById("show_more").style.display = "none";
    };

    function load_reviews(start, end) {
        var rev_len = reviews.length;
        if (end > rev_len) end = rev_len;
        for (var i = start; i < end; i++) {
            var row = `
                            <div style="font-size: 16px; padding: 15px 0px; border-bottom: 1px solid #ededed;">
                                <div class="company__card">
                                        <div class="company__company___img">
                                            <img class="company__img" src="${"https://barter-business.ru/uploads/companys_logo/" + reviews[i].logo}" alt="" width="50px" height="50px">
                                        </div>
                                        <div class="company__company__title"">
                                            <span>${reviews[i].company_name}</span>
                                        </div>
                                        <div class="company__company__desc top-15">
                                            <span>${(reviews[i].text_rev == "" ? "Нет текста отзыва." : reviews[i].text_rev)}</span>
                                        </div>
                                </div>
                            </div>
                            `;
            $('#review_table').append(row);
        }
        review_num = end;
        if (rev_len == end) {
            document.getElementById("button_load_reviews").style.display = "none";
        }
    }

    function load_products(start, end) {
        var products_len = products.length;
        if (end > products_len) end = products_len;
        for (var i = start; i < end; i++) {
            var row = `
            <div class="clear color-highlight">
                    <div class="one-half small-half">
                        <div data-height="140" class="caption" style="height: 140px;">
                            <div class="caption-image">
                                <a class="default-link" href="https://barter-business.ru/uploads/products_image/${products[i].image}" data-lightbox="gallery-1">
                                <div class="product__img" style="background: url(https://barter-business.ru/uploads/products_image/${products[i].image}) 50% center / cover no-repeat;"></div>
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="one-half large-half last-column">
                        <h5 class="color-theme">${products[i].title}</h5>
                        <span class="under-heading font-10 color-highlight">Категория: ${products[i].category_title == null ? 'Нет категории' : products[i].category_title}</span>
                        ${products[i].description != null ? "<p class=\"font-12 bottom-10 description_product\">" + products[i].description + "</p>" : ""}
                        <div class="one-half">
                            <span  class="font-16">${products[i].price} ₽</span>
                        </div>
                    </div>
            </div>
            `;
            $('#products_table').append(row);
        }
        products_num = end;
        if (products_len == end) {
            document.getElementById("button_load_products").style.display = "none";
        }
    }

    function write_review_comp(to) {

        var MIN_LENGTH = 10;

        if (to == null) {
            console.log("No company_id for sending review");
            return;
        }

        var text = $('#review_text').val();
        if (text.length < MIN_LENGTH){
            vex.dialog.alert("Длина отзыва должна быть не менее " + MIN_LENGTH + " символов!");
            return;
        }

        var data = {
            'text': $('#review_text').val(),
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
                //вставляем хэш
                insert_csrf_hash(data);

                if (data.status === 'success') {
                    $('#review_text').val("");
                    vex.dialog.alert("Ваш отзыв успешно опубликован!");
                } else {
                    vex.dialog.alert("Ошибка отправки отзыва! Попробуйте перезагрузить страницу и отправьте еще раз!");
                }
            }
        });
    };

    function change_fave(company_id) {
        var data = {
            'add_to_fave_id': company_id,
            'token_form': $('#token_form').val(),
        };

        $.ajax({
            url: base_url + 'company/company_ajax/ajax/change_fave',
            type: 'POST',
            dataType: 'JSON',
            data: data,
            cache: false,
            success: function (data) {

                //вставляем хэш
                insert_csrf_hash(data);//функция берется из all_area_scripts!
                var result = validate_data_server_response(data);
                var status = parseInt(data.code);
                if (status < 0) {
                    alert("Ошибка добавления в избранное! Статус:" + status);
                }
                else {
                    if (status == 1) {
                        likes++;
                        document.getElementById("likes").innerHTML = " " + likes;
                        document.getElementById("likes").className = "fa fa-heart color-red2-light";
                    }
                    else {
                        likes--;
                        document.getElementById("likes").innerHTML = " " + likes;
                        document.getElementById("likes").className = "far fa-heart";
                    }
                }
            },
        });
    };

    $('.make-deal').click(function () {

        var data = {
            'token_form': $('#token_form').val(),
        };
        $.ajax({
            url: base_url + 'company/company_ajax/ajax/check_sub_status',
            type: 'POST',
            dataType: 'JSON',
            data: data,
            cache: false,
            //async: false,
            success: function (data) {
                //вставляем хэш
                insert_csrf_hash(data);//функция берется из all_area_scripts!

                var result = validate_data_server_response(data);

                if (!result) {
                    vex.dialog.alert('Ошибка проверки статуса оплаты АП! Обновите страницу и попробуйте еще раз!');
                    return;
                }

                if(data.sub_status == 0) {
                    vex.dialog.open({
                        message: 'Ошибка! Абонетская плата не оплачена!',
                        buttons: [
                            $.extend({}, vex.dialog.buttons.YES, { text: 'Оплатить',
                                className: 'create-deal button button-xs round-small shadow-large bg-highlight button-full bottom-10'}),
                            $.extend({}, vex.dialog.buttons.NO, { text: 'Отмена',
                                className: 'button button-xs round-small shadow-large button-border button-full border-highlight color-highlight bg-transparent'})
                        ],
                        callback: function (data) {
                            if (!data) return;
                            window.location.replace("<?php echo site_url('company/abon_plata'); ?>");
                        }
                    });
                    return;
                }

                $.ajax({
                    url: base_url + 'company/company_ajax/ajax/get_available_coupons',
                    type: 'POST',
                    dataType: 'JSON',
                    data: {'token_form': $('#token_form').val()},
                    cache: false,
                    success: function (data) {

                        insert_csrf_hash(data);

                        let coupons = '';

                        if (data.status === 'success') {
                            coupons = '<div class="input-style input-style-2" id="coupons_menu"><select id="coupon" onchange="document.getElementById(\'coupon_alert\').style.display = \'block\'; checkSum();" name="coupon"><option value="0" data-sum="0" selected>Без купона</option>';
                            data.coupons.forEach(function(elem) {
                                let date_str = format_date(new Date(elem.date_expire), true);
                                coupons += `<option value="${elem.coupon_id}" data-sum="${elem.summa}">Сумма: ${elem.summa/100} руб. Активен до: ${date_str}</option>`;
                            });
                            coupons += '</select></div><div id="coupon_alert" style="display: none">Внимание! Если сумма купона больше суммы заказа, остаток по купону сгорает!</div>';
                        } else {
                            coupons = `<div>${data.text_message}</div>`;
                        }

                        vex.dialog.open({
                            message: 'Заключение сделки',
                            input: [
                                '<div class="input-style input-style-2">',
                                '<input placeholder="Сумма заказа" id="sum_deal" name="sum_deal" type="number" class="sum_deal form-control" required>',
                                '</div>',
                                '<div class="error" id="error_sum_deal"></div>',
                                '<div id="itog_sum_block">С учетом комиссии сервиса: <span id="itog_sum" class="my_info">0.00</span></div>',
                                '<div class="input-style input-style-2">',
                                '<input type="text" placeholder="Комментарий к заказу" id="comment_deal" name="comment_deal" maxlength="250" class="form-control" required>',
                                '</div>',
                                '<h4>Использовать купон</h4>',
                            ].join('') + coupons,
                            buttons: [
                                $.extend({}, vex.dialog.buttons.YES, { text: 'Отправить',
                                    className: 'create-deal button button-xs round-small shadow-large bg-highlight button-full bottom-10'}),
                                $.extend({}, vex.dialog.buttons.NO, { text: 'Отмена',
                                    className: 'button button-xs round-small shadow-large button-border button-full border-highlight color-highlight bg-transparent'})
                            ],
                            callback: function (data) {
                                if (!data) return;
                                let coupon = 0;
                                if (data.coupon !== undefined) coupon = data.coupon;
                                create_new_deal(comp_id, data.sum_deal, data.comment_deal, coupon);
                            }
                        });
                        document.getElementById("sum_deal").addEventListener('keyup', checkSum);
                        //document.getElementById("coupon").addEventListener('change', checkSum);
                    }
                });
            }
        });
    });

    function create_new_deal(company_id, sum_deal, comment_deal, coupon, coupon_sum) {

        if (comp_id == null) return;
        var data = {
            'company_id': comp_id,
            'sum_deal': Number(sum_deal) * 100,//отправляем копейки на север
            'comment_deal': comment_deal,
            'coupon': coupon,
            'token_form': $('#token_form').val(),
        };

        $('.create_deal').prop('disabled', true);

        if (data.sum_deal <= 0) {

            $('#error_sum_deal').html(
                '<p style="color: red; margin-bottom: 0;">Сумма сделки не может быть равна нулю!</p>');
            empty_errors();
            //вставляем хэш
            insert_csrf_hash(data);//функция берется из all_area_scripts!
        } else {

            $.ajax({
                url: base_url + 'company/company_ajax/ajax/create_new_deal',
                type: 'POST',
                dataType: 'JSON',
                data: data,
                cache: false,
                success: function (data) {
                    //вставляем хэш
                    insert_csrf_hash(data);//функция берется из all_area_scripts!

                    var result = validate_data_server_response(data);

                    if (result) {
                        var balance = $('#buyer_balance').data('buyerBalance') - Number(data.sum) / 100;
                        $('#buyer_balance').data('buyerBalance', balance);
                        $('#buyer').text('Баланс: ' + balance);
                        account_balance = account_balance - Number(data.sum) / 100;
                        console.log('balance=' + account_balance);

                        vex.dialog.open({
                            message: 'Сделка успешно создана! Можете оставить отзыв!',
                            input: [
                                '<div class="input-style input-style-2">',
                                '<input type="text" placeholder="Текст отзыва" id="review" name="review" maxlength="250" class="form-control" required>',
                                '</div>',
                            ].join(''),
                            buttons: [
                                $.extend({}, vex.dialog.buttons.YES, { text: 'Отправить',
                                    className: 'create-deal button button-xs round-small shadow-large bg-highlight button-full bottom-10'}),
                                $.extend({}, vex.dialog.buttons.NO, { text: 'Отмена',
                                    className: 'button button-xs round-small shadow-large button-border button-full border-highlight color-highlight bg-transparent'})
                            ],
                            callback: function (data) {
                                if (!data) {
                                    return;
                                }
                                let send_data = {
                                    'text': data.review,
                                    'token_form': $('#token_form').val(),
                                    'to': comp_id
                                };
                                return $.ajax({
                                    url: base_url + 'company/company_ajax/ajax/review_comp',
                                    type: 'POST',
                                    dataType: 'JSON',
                                    data: send_data,
                                    cache: false,
                                    success: function (data) {
                                        insert_csrf_hash(data);
                                        if (data.status === 'success') {
                                            vex.dialog.alert(data.text_message);
                                        } else {
                                            vex.dialog.alert(data.text_message);
                                        }
                                    }
                                });
                            }
                        });

                    } else {

                        if(data.sub_status !== undefined) {
                            vex.dialog.open({
                                message: data.text_message,
                                buttons: [
                                    $.extend({}, vex.dialog.buttons.YES, { text: 'Оплатить',
                                        className: 'create-deal button button-xs round-small shadow-large bg-highlight button-full bottom-10'}),
                                    $.extend({}, vex.dialog.buttons.NO, { text: 'Отмена',
                                        className: 'button button-xs round-small shadow-large button-border button-full border-highlight color-highlight bg-transparent'})
                                ],
                                callback: function (data1) {
                                    if (!data1) return;
                                    window.location.replace("<?php echo site_url('company/abon_plata'); ?>");
                                }
                            });
                            return;
                        }
                        else {
                            vex.dialog.alert(data.text_message);
                        }
                    }
                },
            });
        }
    };

    $('.make-complaint').click(function () {
        vex.dialog.open({
            message: 'Сообщить о нарушении',
            input: [
                '<div class="input-style input-style-2">',
                '<input type="text" name="review_text" placeholder="Описание" required></input>',
                '</div>',
            ].join(''),
            buttons: [
                $.extend({}, vex.dialog.buttons.YES, { text: 'Отправить',
                    className: 'button button-xs round-small shadow-large bg-highlight button-full bottom-10'}),
                $.extend({}, vex.dialog.buttons.NO, { text: 'Отмена',
                    className: 'button button-xs round-small shadow-large button-border button-full border-highlight color-highlight bg-transparent'})
            ],
            callback: function (data) {
                if(!data) return;
                let senddata = {
                    'text': data.review_text,
                    'token_form': $('#token_form').val(),
                    'to': comp_id
                };

                $.ajax({
                    url: base_url + 'company/company_ajax/ajax/review',
                    type: 'POST',
                    dataType: 'JSON',
                    data: senddata,
                    cache: false,
                    success: function (data) {

                        if (data.status === 'success') {
                            vex.dialog.alert('Ваша жалоба успешно отправлена!');
                        } else {
                            vex.dialog.alert('Ошибка отправки жалобы! Попробуйте еще раз!');
                        }
                        //вставляем хэш
                        insert_csrf_hash(data);
                    }
                });
            }
        });
    });

    function checkSum() {
        //функция не идеальна, но работает!
        var sum_deal = $('#sum_deal').val();
        var buyer_balance = account_balance;//$('#buyer_balance').data('buyerBalance');
        var reg = /[^\d.]/ig;


        if (reg.test(sum_deal) === true) {
            $('#error_sum_deal').html(
                '<p style="color: red; margin-bottom: 0;">В поле присутствуют недопустимые знаки!</p>');
            $('.create-deal').prop('disabled', true);

        } else {

            var sum_deal = Math.abs(Number(sum_deal)) * 100;//переводим в копейки
            //Сумма примененного купона
            var coupon_sum = 0;
            if ($('#coupon option:selected').data('sum') !== undefined) {
                 coupon_sum = parseInt($('#coupon option:selected').data('sum'));
            }
            if(sum_deal <= 0) {
                $('#error_sum_deal').html(
                    '<p style="color: red; margin-bottom: 0;">Сумма сделки должна быть больше 0 руб.!</p>');
                $('.create-deal').prop('disabled', true);
                return;
            }

            var itog_sum = (sum_deal + ((sum_deal / 100) * percent) - coupon_sum) / 100;//переводим в рубли
            var itog_sum = itog_sum.toFixed(2);//оставляем 2 знака после точки
            //В случае, если сумма купона больше итоговой суммы сделки
            if (itog_sum < 0) itog_sum = 0;

            if (itog_sum > buyer_balance) {

                var raznitsa_sum = itog_sum - buyer_balance;
                $('#error_sum_deal').html(
                    '<p style="color: red; margin-bottom: 0;">Сумма к оплате превышает ваш баланс на ' +
                    raznitsa_sum.toFixed(2) + ' руб.</p>');
                $('.create-deal').prop('disabled', true);

            } else {
                $('#error_sum_deal').html('');
                $('.create-deal').prop('disabled', false);
            }

            $('#itog_sum').html(itog_sum);
        }
    };

    $('#credit_rep').keyup(function() {
        //функция не идеальна, но работает!
        var sum = $(this).val();
        var buyer_balance = $('#buyer_balance').data('buyerBalance') * 100;
        var credit_balance = $("#credit_balance").data('creditBalance') * 100;
        var reg = /[^\d.]/ig;

        if (reg.test(sum) === true) {
            $('#error_credit_rep').html(
                '<p style="color: red; margin-bottom: 0;">В поле присутствуют недопустимые знаки!</p>');
            $('#submit_rep_credit').prop('disabled', true);
            return;
        }

        var itog_sum = Math.abs(Number(sum)) * 100;//переводим в копейки

        if(itog_sum <= 0) {
            $('#error_credit_rep').html(
                '<p style="color: red; margin-bottom: 0;">Сумма погашения должна быть больше 0 руб.!</p>');
            $('#submit_rep_credit').prop('disabled', true);
            return;
        }

        if(itog_sum > buyer_balance) {
            var raznitsa_sum = itog_sum - buyer_balance;
            $('#error_credit_rep').html(
                '<p style="color: red; margin-bottom: 0;">Сумма превышает ваш баланс на ' +
                raznitsa_sum.toFixed(2) + ' руб.</p>');
            $('#submit_rep_credit').prop('disabled', true);
            return;
        }

        if(itog_sum > credit_balance) {
            $('#error_credit_rep').html(
                '<p style="color: red; margin-bottom: 0;">Кредит будет погашен полностью на ' + credit_balance / 100 + ' руб. от Вашей суммы.</p>');
        }
        else {
            $('#error_credit_rep').html('');
        }

        $('#submit_rep_credit').prop('disabled', false);
    });

    function loan_repayment(elem = null) {

        if(elem != null) $(elem).prop("disabled", true);

        var credit = $('#credit_rep').val() * 100;
        var credit_balance = $("#credit_balance").data('creditBalance') * 100;

        if (credit_balance < credit) {
            credit = credit_balance;
        }

        if (credit  <= 0) {
            vex.dialog.alert('Ошибка введенной суммы!');
            return;
        }

        vex.dialog.confirm({
            message: `Вы точно хотите погасить кредит на сумму ${credit / 100} бр.?`,
            buttons: [
                $.extend({}, vex.dialog.buttons.YES, {
                    text: 'Да',
                    className: 'button button-s round-small shadow-large button-border button-full border-highlight color-green1-dark bg-transparent'
                }),
                $.extend({}, vex.dialog.buttons.NO, {
                    text: 'Отмена',
                    className: 'button button-s round-small shadow-large button-border button-full border-highlight color-red1-dark  bg-transparent'
                })
            ],
            callback: function (value) {
                if (value) {
                    var data = {
                        'credit': credit,
                        'token_form': $('#token_form').val(),
                    };

                    $.ajax({
                        url: base_url + 'company/company_ajax/ajax/update_credit',
                        type: 'POST',
                        dataType: 'JSON',
                        data: data,
                        cache: false,
                        error: function () {
                            vex.dialog.alert("Ошибка! Попробуйте еще раз...");
                            setTimeout(function () {
                                reload_page();
                            }, 1500);
                        },
                        success: function (data) {
                            //вставляем хэш
                            insert_csrf_hash(data);//функция берется из all_area_scripts!

                            var result = validate_data_server_response(data);

                            vex.dialog.alert(data.text_message);

                            setTimeout(function () {
                                reload_page();
                            }, 1500);
                        },
                    });
                }
            }
        });
    }

    function init_comp_map(geocode, company_name, adress) {
        myMap = new ymaps.Map('map_company', {
            center: geocode,
            zoom: 16,
        }, {
            searchControlProvider: 'yandex#search',
        });
        myMap.geoObjects.add(new ymaps.Placemark(geocode, {
            balloonContentHeader: company_name.replace(new RegExp("\"",'g'),"'"),
            balloonContentFooter: adress.replace(new RegExp("\"",'g'),"'"),//firstGeoObject.getAddressLine(),
            preset: 'islands#icon',
            iconColor: '#0095b6',
        }));
        // ymaps.geocode(geocode,{
        //     results: 1
        // }).then(function (res) {
        //     var firstGeoObject = res.geoObjects.get(0);
        //
        // });
    };
</script>

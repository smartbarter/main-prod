<div class="page-content header-clear-large">
        <div class="content">
                <div class="deal_sort">
                    <div class="deal_sort_list">
                        <a href="<?= site_url('company/orders/inbox') ?>" <?= ($this->uri->segment(3) === 'inbox') ? 'class="active show"' : '' ?>
                        >Входящие</a>
                    </div>
                    <div class="deal_sort_list">
                        <a href="<?= site_url('company/orders/outbox') ?>" <?= ($this->uri->segment(3) === 'outbox') ? 'class="active show"' : '' ?>
                        >Исходящие</a>
                    </div>
                    <div class="deal_sort_list">
                        <a href="<?= site_url('company/orders/unaccepted') ?>" <?= ($this->uri->segment(3) === 'unaccepted') ? 'class="active show"' : '' ?>
                        >Не принятые</a>
                    </div>

                </div>
        </div>
        <?php if (! empty($deals_list)): ?>
            <?php $c = 1; ?>
            <?php foreach ($deals_list as $data => $list): ?>

                <div class="content-box shadow-tiny bottom-10">
                    <p class="font-900 bottom-10 color-black1-dark"><?= $data ?></p>
                    <?php foreach ($list as $deal): ?>
                        <div class="history_deal_element" data-menu="action-deal" onclick="open_deal_detail(this, <?= $deal['deal_id']; ?>)">
                            <div class="company__card">
                                <div class="company__company___img">
                                    <img class="company__img"
                                         src="https://barter-business.ru/uploads/companys_logo/<?= $deal['logo']; ?>"
                                         alt="">
                                </div>
                                <div class="company__company__title">
                                <span>
                                <?php echo mb_substr($deal["company_name"], 0, 45, "UTF-8"); ?></span>
                                </div>
                                <div class="company__company__desc">
                                    <?php if ($deal['status_deal'] == 2) { ?>
                                        <?php if ($deal['want_cancel'] == 1) { ?>
                                            <span class="status color-yellow1-dark font-16">Ожидает отмены</span>
                                        <?php } else { ?>
                                            <span class="status color-green1-dark font-16">Сделка совершена</span>
                                        <?php }
                                    } elseif ($deal['status_deal'] == 0) { ?>
                                        <span class="status color-red1-dark font-16">Сделка отменена</span>
                                    <?php } elseif ($deal['status_deal'] == 1) { ?>
                                        <span class="status color-yellow1-dark font-16">Ожидает потверждения</span>
                                    <?php } elseif ($deal['status_deal'] == 3) { ?>
                                        <span class="status color-aqua-dark font-16">Выдача кредита</span>
                                    <?php } elseif ($deal['status_deal'] == 4) { ?>
                                        <span class="status color-aqua-dark font-16">Погашение кредита</span>
                                    <?php }?>
                                    <span style="float: right;font-weight: 700;letter-spacing: 1px;" class="font-16">Сумма: <?php echo $deal['summa_sdelki'] / 100; ?></span>
                                </div>
                            </div>
                        </div>

                        <?php $c++; ?>
                    <?php endforeach; ?>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>

    <?php echo $this->pagination->create_links(); ?>

</div>
<div class="menu-hider"></div>
<div id="action-deal"
     class="menu-box menu-box-detached round-medium"
     data-menu-type="menu-box-bottom"
     data-menu-height="360"
     data-menu-effect="menu-parallax">

    <div class="page-title ">

        <div class="page-title-right">
            <a href="#" class="close-menu"><i class="fa fa-times-circle font-20 color-red2-dark"></i></a>
        </div>
    </div>
    <div id="deal_loader" class="loader-main loader-inactive">
        <div class="preload-spinner border-highlight"></div>
    </div>
    <div class="content">
        <div class="info_deal">
            <div class="font-24">Сумма: <span id="deal_sum" class="load2"></span></div>
            <div>Статус: <span id="deal_status" class="load2 bolder"></span></div>
            <div>Дата: <span id="deal_date" class="load2"></span></div>
            <div id="grid" style="display: grid; grid-template-columns: 50% 50%;">
                <div id="itog_info">
                    <div class="bolder">Информация платежа:</div>
                    <div>Комиссия: <span id="deal_comission" class="load2"></span></div>
                    <div id="deal_coupon_box">Сумма купона: <span id="deal_coupon" class="load2"></span></div>
                    <div class="bolder">Итого: <span id="deal_full" class="load2"></span></div>
                </div>
                <div>
                    <span class="bolder">Комментарий:</span>
                    <p id="deal_comment" class="load2"></p>
                </div>
            </div>
        </div>
        <div class="deal_btn_detail">
            <div class="one-half">
                <a href="#" style="width: 100%" id="deal_reject_btn"
                   class="need_hide button button-m round-small color-red1-dark bg-transparent text-center"
                   onclick="accept_or_cancel_dealM(0);">Отменить
                </a>
            </div>
            <div class="one-half last-column">
                <a href="#" style="width: 100%" id="deal_accept_btn"
                   class="need_hide button button-m round-small shadow-large bg-green1-dark text-center"
                   onclick="accept_or_cancel_dealM(2);">Принять
                </a>
            </div>
            <button style="width: 100%; margin-top: 15px;" id="view_profile_btn"
                    class="button button-s round-small shadow-large button-border button-full border-highlight color-highlight bg-transparent"
                    data-menu="menu-instant-3" data-height="220"
                    onclick="">Профиль партнера
            </button>
        </div>
        <p id="deal_accept_refund_msg" class="need_hide" style="display: none">Ваш контрагент отправил заявку на
            отмену сделки</p>
        <button id="deal_refund"
                class="need_hide button button-m round-small button-full  color-red1-dark bg-transparent"
                style="display: none; width: 100%"
                onclick="cancel_dealM()">
            Подтвердить отмену сделки
        </button>
    </div>
</div>
<script>
    $(document).ready(function () {
        if(!window.location.hash) {
            window.location = window.location + '#loaded';
            window.location.reload();
        }
    });
</script>
<script>
    var curr_deal_id = null;
    var curr_buyer_deal_id = null;
    var curr_deal = null;
    var inProgress = false;

    function open_deal_detail(element = null, deal_id) {

        $(".load2").text("");
        $('#grid').css({'display': 'block'});
        $('#itog_info').hide();
        curr_deal_id = null;
        curr_buyer_deal_id = null;

        curr_deal = element;

        var data = {
            'token_form':  $.cookie('csrf_barter'),
            'deal_id': deal_id,
        };
        $.ajax({
            url: base_url + 'company/orders/open_deal_detail',
            type: 'POST',
            dataType: 'JSON',
            data: data,
            cache: false,
            error: function () {
                vex.dialog.alert("Ошибка загрузки данных! Страница перезагрузится.");
                setTimeout(function () {
                    reload_page();
                }, 1500);
            },
            beforeSend: function () {
                inProgress = true;
                $('#deal_loader').removeClass('loader-inactive');
            },
        }).done(function (data) {

            insert_csrf_hash(data);

            if (data.status === 'success') {

                curr_deal_id = parseInt(data.data.deal_id);
                curr_buyer_deal_id = data.data.buyer_deal_id;

                document.getElementById("deal_sum").innerHTML = data.data.summa_sdelki / 100;
                document.getElementById("deal_comment").innerHTML =  data.data.comment_deal == '' ? "-" : data.data.comment_deal;
                document.getElementById("deal_date").innerHTML = data.data.date;
                document.getElementById("view_profile_btn").onclick = function () {
                    open_company_detail(data.data.partner_id);
                };

                if ('<?= $this->uri->segment(3) ?>' == 'outbox' && parseInt(data.data.status_deal) !== 3 && parseInt(data.data.status_deal) !== 4) {
                    $('#grid').css({'display': 'grid'});
                    $('#itog_info').show();
                    document.getElementById("deal_comission").innerHTML = data.data.summa_sdelki * (percent / 100) / 100;
                    if (data.data.coupon_sum > 0) {
                        document.getElementById("deal_coupon").innerHTML = data.data.coupon_sum / 100;
                        $('#deal_coupon_box').show();
                    }
                    else {
                        $('#deal_coupon_box').hide();
                    }
                    var itog = data.data.summa_sdelki * (1 + percent / 100) - data.data.coupon_sum;
                    document.getElementById("deal_full").innerHTML = (itog > 0 ? itog / 100 : 0);
                }

                handle_buttons_and_status(parseInt(data.data.status_deal), parseInt(data.data.want_cancel));

            } else {
                vex.dialog.alert(data.text_message);
            }

            $('#deal_loader').addClass('loader-inactive');
            inProgress = false;
        });
    };

    function accept_or_cancel_dealM(status_deal) {

        if (curr_deal_id == null) return;
        var msg = "-";
        if(status_deal == 2) msg = "Вы действительно хотите принять сделку?";
        else msg = "Вы действительно хотите отменить сделку?";

        vex.dialog.confirm({
            message: msg,
            buttons: [
                $.extend({}, vex.dialog.buttons.YES, { text: 'Да',
                    className: 'button button-s round-small shadow-large button-border button-full border-highlight color-green1-dark bg-transparent'}),
                $.extend({}, vex.dialog.buttons.NO, { text: 'Нет',
                    className: 'button button-s round-small shadow-large button-border button-full border-highlight color-red1-dark  bg-transparent'})
            ],
            callback: function (value) {
                if (value) {
                    $(".need_hide").attr("disabled", true);

                    let data = {
                        'status_deal': status_deal,
                        'deal_id': curr_deal_id,
                        'buyer_deal_id': curr_buyer_deal_id,
                        'token_form': $('#token_form').val(),
                    };

                    $.ajax({
                        url: base_url + 'company/company_ajax/ajax/update_status_deal',
                        type: 'POST',
                        dataType: 'JSON',
                        data: data,
                        cache: false,
                        beforeSend: function () {
                            inProgress = true;
                        },

                        success: function (data) {

                            //вставляем хэш
                            insert_csrf_hash(data);

                            switch (data.status) {
                                case 'fail_data':
                                    console.log(data.message);
                                    break;
                                case 'success':
                                    vex.dialog.alert('Отлично! ' + data.message);
                                    break;
                                case 'fail':
                                    vex.dialog.alert('Ошибка: ' + data.message);
                                    break;
                            }
                            setTimeout(function () {
                                reload_page();
                            }, 1500);
                            //update_deal_status();
                            inProgress = false;
                        },
                    });
                }
            }
        });

    }

    function cancel_dealM() {

        if (curr_deal_id == null) return;
        vex.dialog.confirm({
            message: 'Вы действительно хотите отменить сделку?',
            buttons: [
                $.extend({}, vex.dialog.buttons.YES, { text: 'Да',
                    className: 'button button-s round-small shadow-large button-border button-full border-highlight color-green1-dark bg-transparent'}),
                $.extend({}, vex.dialog.buttons.NO, { text: 'Нет',
                    className: 'button button-s round-small shadow-large button-border button-full border-highlight color-red1-dark  bg-transparent'})
            ],
            callback: function (value) {
                if (value) {

                    $(".need_hide").attr("disabled", true);

                    let data = {
                        'token_form': $('#token_form').val(),
                        'deal_id': curr_deal_id,
                    };

                    $.ajax({
                        url: base_url + 'company/company_ajax/ajax/mutually_cancel',
                        type: 'POST',
                        dataType: 'JSON',
                        data: data,
                        cache: false,
                        beforeSend: function () {
                            inProgress = true;
                        },
                        success: function (data) {

                            //вставляем хэш
                            insert_csrf_hash(data);//функция берется из all_area_scripts!

                            var result = validate_data_server_response(data);
                            if (result) {
                                vex.dialog.alert(data.text_message);
                            } else {
                                vex.dialog.alert('Ошибка отмены сделки!');
                            }
                            setTimeout(function () {
                                reload_page();
                            }, 1500);
                            //update_deal_status();
                            inProgress = false;
                        },
                    });
                }
            }
        });
    };

    function update_deal_status() {
        var data = {
            'token_form': $.cookie('csrf_barter'),
            'deal_id': curr_deal_id,
            'stat': 1,
        };
        $.ajax({
            url: base_url + 'company/orders/open_deal_detail',
            type: 'POST',
            dataType: 'JSON',
            data: data,
            cache: false,
            error: function () {
                vex.dialog.alert("Ошибка загрузки данных! Страница перезагрузится.");
                setTimeout(function () {
                    reload_page();
                }, 1500);
            },
        }).done(function (data) {

            insert_csrf_hash(data);

            var status_deal = parseInt(data.data.status_deal);
            var want_cancel = parseInt(data.data.want_cancel);

            handle_buttons_and_status(status_deal, want_cancel);
        });
    }

    function handle_buttons_and_status(status_deal, want_cancel) {

        $(".need_hide").hide();
        var status_field = $(curr_deal).find("span.status");
        var status = "-";
        var class1 = "color-red1-dark";
        switch(status_deal)
        {
            case 0:
                status = "Отменена";
                class1 = "color-red1-dark";
                break;

            case 1:
                status = "Ожидает подтверждения";
                class1 = "color-yellow1-dark";
                if ('<?= $this->uri->segment(3) ?>' == 'outbox') {
                    $('#deal_reject_btn').show();

                } else if ('<?= $this->uri->segment(3) ?>' == 'inbox' || '<?= $this->uri->segment(3) ?>' == 'unaccepted') {
                    $('#deal_accept_btn').show();
                    $('#deal_reject_btn').show();
                }
                break;

            case 2:
                status = "Сделка совершена";
                class1 = "color-green1-dark";
                if (want_cancel == 1) {
                    status = "Ожидает отмены";
                    class1 = "color-yellow1-dark";
                }
                if (('<?= $this->uri->segment(3) ?>' == 'inbox' || '<?= $this->uri->segment(3) ?>' == 'unaccepted') && want_cancel == 1) {
                    $('#deal_refund').text('Подтвердить отмену сделки');
                    $('#deal_refund').show();
                    $('#deal_accept_refund_msg').show();
                }
                else if ('<?= $this->uri->segment(3) ?>' == 'outbox' && want_cancel == 0) {
                    $('#deal_refund').text('Отменить сделку');
                    $('#deal_refund').show();
                }
                break;
            case 3:
                status = "Выдача кредита";
                class1 = "color-aqua-dark";
                break;
            case 4:
                status = "Погашение кредита";
                class1 = "color-aqua-dark";
                break;
        }
        $("#deal_status").removeClass('color-red1-dark color-yellow1-dark color-aqua-dark');
        $("#deal_status").addClass(class1);
        $("#deal_status").text(status);
        status_field.removeClass('color-red1-dark color-yellow1-dark color-aqua-dark');
        status_field.addClass(class1);
        status_field.text(status);

    }
</script>
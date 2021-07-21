$('#search_field').keyup(function () {
    var search = $(this).val();
    var type_search = $('#type_search').val();
    if (search.length > 2) {
        load_data(search, type_search);
        $('#reset_icon').css({ 'display': 'block' });
    } else if (search == '') {
        hide_search_result_list();
    }
});

$('#reset_icon').on('click', function () {
    hide_search_result_list();
});

function hide_search_result_list() {
    $('#search_result').delay(100).fadeOut(500);
    $('#search_result').html('');
    $('#search_field').val('');
    $('#reset_icon').hide('fast');
}

function load_data(query, type_search) {

    var data = {
        'search_field': query,
        'type_search': type_search
    };

    $.ajax({

        url: base_url + 'api/search/search_company',
        type: 'POST',
        dataType: 'JSON',
        data: data,
        cache: false,
        beforeSend: function (xhr) {
            $('#loader').show();
        },
        success: function (data) {
            $('#loader').hide();
            //вставляем хэш
            insert_csrf_hash(data);

            $('#search_result').css({ 'display': 'block' });
            $('#search_result').html(data.search_result);
        }
    });

}

function activeRefBalanceM(company_id, type) {

    var msg = '/Err';
    switch (type) {
        case 1:
            msg = 'За каждую компанию можно получить только один из двух бонусов! Вы точно хотите пополнить свой бартерный баланс?';
            break;
        case 2:
            msg = 'За каждую компанию можно получить только один из двух бонусов! Вы точно хотите продлить подписку на 28 дней?';
            break;
        case 3:
            msg = 'Эта компания была зарегистрирована в декабре 2019 года! Будет произведено пополнение бартерного баланса на 3000БР вместо обычных 500БР! Вы точно хотите пополнить свой бартерный баланс?';
            break;
        default:
            return;
    }
    vex.dialog.confirm({
        message: msg,
        buttons: [
            $.extend({}, vex.dialog.buttons.YES, { text: 'Подтвердить',
                className: 'button button-s round-small shadow-large button-border button-full border-highlight color-green1-dark bg-transparent'}),
            $.extend({}, vex.dialog.buttons.NO, { text: 'Отмена',
                className: 'button button-s round-small shadow-large button-border button-full border-highlight color-red1-dark  bg-transparent'})
        ],
        callback: function (value) {
            if (value) {
                let data = {
                    'token_form': $('#token_form').val(),
                    'company_id': company_id,
                    'type': type,
                };
                $.ajax({
                    url: base_url + 'company/company_ajax/ajax/active_ref_balance',
                    type: 'POST',
                    dataType: 'JSON',
                    data: data,
                    cache: false,
                    success: function (data) {

                        //вставляем хэш
                        insert_csrf_hash(data);//функция берется из all_area_scripts!

                        var result = validate_data_server_response(data);
                        if (result) {
                            vex.dialog.alert("Бонус успешно начислен!");
                            setTimeout(function () {
                                reload_page();
                            }, 1000);

                        } else {
                            if (data.text_message !== undefined)
                                vex.dialog.alert(data.text_message);
                            else
                                vex.dialog.alert("Ошибка! Перезагрузите страницу и попробуйте еще раз!");
                        }
                    },
                });
            }
        }
    });

}

$('#city_selector').change(function () {

    value = $(this).val();
    if (!value.length) {
        vex.dialog.alert('Ошибка выбора города!(Пустое значение)');
        return;
    }
    $.ajax({
        url: base_url + 'company/company_ajax/ajax/setcity',
        type: 'POST',
        dataType: 'JSON',
        data: {
            q: value,
            token_form: $('#token_form').val(),
        },
        error: function () {
            vex.dialog.alert('Произошла ошибка выполнения запроса! Повторите попытку после перезагрузки страницы.');
            setTimeout(function () {
                reload_page();
            }, 1000)
        },
        success: function (data) {
            insert_csrf_hash(data);
            let result = validate_data_server_response(data);
            if (result) {
                vex.dialog.alert('Ошибка смены города! Повторите попытку после перезагрузки страницы.');
            }
            else {
                vex.dialog.alert('Город успешно изменен! Страница перезагрузится...');
            }
            setTimeout(function () {
                reload_page();
            }, 1000)
        }
    });
});
function restock_balance() {
    let data = {
        'amount': $('#restock_amount').val(),
        'token_form': $('#token_form').val(),
    };

    $.ajax({
        url: base_url + 'company/company_ajax/ajax/create_Ypayment',
        type: 'POST',
        dataType: 'JSON',
        data: data,
        cache: false,
        success: function (data) {

            insert_csrf_hash(data);//функция берется из all_area_scripts!

            let result = validate_data_server_response(data);
            if (result) {
                vex.dialog.alert('Успешно! Сейчас Вы будете перенаправлены на страницу оплаты...');
                setTimeout(function () {
                    window.location.replace(data.text_message, '_blank');
                }, 1500)
            } else {
                vex.dialog.alert('Ошибка! ' + data.text_message);
            }
        },
    });
}
function restock_balance_manual(elem, type) {

    elem.disabled = true;
    elem.innerHTML = 'Подождите...';

    let data = {
        'amount': 0,
        'type_payment': type,
        'token_form': $('#token_form').val(),
    };

    $.ajax({
        url: base_url + 'company/company_ajax/ajax/create_Ypayment',
        type: 'POST',
        dataType: 'JSON',
        data: data,
        cache: false,
        async: false,
        error: function () {
            vex.dialog.alert("Ошибка запроса! Повторите попытку после перезагрузки страницы...");
            setTimeout(function () {
                reload_page();
            }, 1500)
        },
        success: function (data) {

            insert_csrf_hash(data);//функция берется из all_area_scripts!

            var result = validate_data_server_response(data);
            if (result) {
                vex.dialog.alert("Сейчас Вы будете перенаправлены на страницу оплаты...");
                setTimeout(function () {
                    window.location.replace(data.text_message, '_blank');
                }, 1500)
            } else {
                vex.dialog.alert("Ошибка! " + data.text_message);
            }
        },
    });
}

function send_data(action, data) {
    $.ajax({
        url: action,
        type: 'POST',
        data: data,
        processData: false,
        contentType: false,
        cache: false,
        async: false,
        error: function () {
            vex.dialog.alert("Ошибка запроса! Повторите попытку после перезагрузки страницы...");
            setTimeout(function () {
                reload_page();
            }, 1500)
        },
        success: function (data) {
            //вставляем хэш
            insert_csrf_hash(data);//функция берется из all_area_scripts!

            //валидируем данные
            var result = validate_data_server_response(data);

            if (result) {//если все успешно
                vex.dialog.alert("Отлично! " + data.text_message);
                setTimeout(function () {
                    reload_page();
                }, 1500);
            } else {//если потерпели ошибку
                vex.dialog.alert("Ошибка! " + data.text_message);
            }
        },
    });
}

$('#update_logo').submit(function (e) {

    e.preventDefault();

    var data = new FormData(this);
    var action = $(this).attr('action');

    //добавляем скрытые поля
    var fields = $('.hidden_field');
    for (var i = 0; i < fields.length; i++) {
        data.append($(fields[i]).attr('id'), $(fields[i]).val());
    }

    send_data(action, data);

});

$('.update_description').submit(function (e) {
    e.preventDefault();

    var action = $(this).attr('action');
    var data = new FormData(this);

    if (data.has('confirm')) {
        vex.dialog.confirm({
            message: data.get('confirm'),
            buttons: [
                $.extend({}, vex.dialog.buttons.YES, { text: 'Да',
                    className: 'button button-s round-small shadow-large button-border button-full border-highlight color-green1-dark bg-transparent'}),
                $.extend({}, vex.dialog.buttons.NO, { text: 'Нет',
                    className: 'button button-s round-small shadow-large button-border button-full border-highlight color-red1-dark  bg-transparent'})
            ],
            callback: function (value) {
                if (value) {
                    data.append('token_form', $('#token_form').val());
                    send_data(action, data);
                }
            }
        });
    }
    else {
        data.append('token_form', $('#token_form').val());
        send_data(action, data);
    }
});

function findGetParameter(parameterName) {
    var result = null,
        tmp = [];
    var items = location.search.substr(1).split("&");
    for (var index = 0; index < items.length; index++) {
        tmp = items[index].split("=");
        if (tmp[0] === parameterName) result = decodeURIComponent(tmp[1]);
    }
    return result;
}
function take_credit() {

    var data = {
        'sum_credit': Number($('#sum_credit').val()) * 100,//отправляем копейки на сервер
        'token_form': $('#token_form').val(),
    };

    if (data.sum_deal <= 0) {

        $('#error_sum_deal').html(
            '<p style="color: red; margin-bottom: 0;">Сумма кредита не может быть равна нулю!</p>');
        empty_errors();
        //вставляем хэш
        insert_csrf_hash(data);//функция берется из all_area_scripts!

    } else {

        $.ajax({
            url: base_url + 'company/company_ajax/ajax/getcredit',
            type: 'POST',
            dataType: 'JSON',
            data: data,
            cache: false,
            success: function (data) {

                //вставляем хэш
                insert_csrf_hash(data);//функция берется из all_area_scripts!

                var result = validate_data_server_response(data);
                if (result) {//если все успешно
                    vex.dialog.alert("Заявка на кредит отправлена! ");

                } else {//если потерпели ошибку
                    vex.dialog.alert("Ошибка! " );
                }
            },
        });

    }
}

function select_ref_mode(elem, mode)
{
    vex.dialog.confirm({
        message: `Вы точно хотите выбрать тип реферальной системы: "${elem.innerHTML.trim()}"? Выбор изменить будет нельзя!`,
        buttons: [
            $.extend({}, vex.dialog.buttons.YES, { text: 'Принять',
                className: 'button button-s round-small shadow-large button-border button-full border-highlight color-green1-dark bg-transparent'}),
            $.extend({}, vex.dialog.buttons.NO, { text: 'Отмена',
                className: 'button button-s round-small shadow-large button-border button-full border-highlight color-red1-dark  bg-transparent'})
        ],
        callback: function (value) {
            if (value) {
                let data = {
                    'ref_mode': mode,
                    'token_form': $('#token_form').val(),
                };

                $.ajax({
                    url: base_url + 'company/company_ajax/ajax/select_ref_mode',
                    type: 'POST',
                    dataType: 'JSON',
                    data: data,
                    cache: false,
                    error: function () {
                        vex.dialog.alert("Ошибка запроса! Повторите попытку после перезагрузки страницы...");
                        setTimeout(function () {
                            reload_page();
                        }, 1500)
                    },
                    success: function (data) {

                        //вставляем хэш
                        insert_csrf_hash(data);//функция берется из all_area_scripts!

                        var result = validate_data_server_response(data);

                        if (result) {//если все успешно
                            vex.dialog.alert("Успешно! Страница сейчас обновится...");

                        } else {//если потерпели неудачу
                            vex.dialog.alert("Ошибка!" );
                        }

                        setTimeout(function () {
                            reload_page();
                        }, 1500);

                    },
                });
            }
        }
    });
}

function toClipboard(text) {
    var copytext = document.createElement('input');
    copytext.value = text;
    document.body.appendChild(copytext);
    copytext.select();
    document.execCommand('copy');
    document.body.removeChild(copytext);
}

function get_ref_bonuses() {

    $.ajax({
        url: base_url + 'company/company_ajax/ajax/get_ref_bonuses',
        type: 'POST',
        dataType: 'JSON',
        data: {'token_form': $('#token_form').val()},
        cache: false,
        async: false,
        error: function () {
            vex.dialog.alert("Ошибка запроса! Повторите попытку после перезагрузки страницы...");
            setTimeout(function () {
                reload_page();
            }, 1500);
        },
        success: function (data) {
            //вставляем хэш
            insert_csrf_hash(data);//функция берется из all_area_scripts!

            validate_data_server_response(data);

            vex.dialog.alert(data.text_message);

            setTimeout(function () {
                reload_page();
            }, 1500);
        },
    });
}

function format_date(date, time = false) {
    if (time) {
        var vals = [ date.getDate(), date.getMonth() + 1, date.getHours(), date.getMinutes() ];
    }
    else {
        var vals = [ date.getDate(), date.getMonth() + 1 ];
    }
    for( var id in vals ) {
        vals[ id ] = vals[ id ].toString().replace( /^([0-9])$/, '0$1' );
    }
    if (time) return `${vals[0]}.${vals[1]}.${date.getFullYear()} ${vals[2]}:${vals[3]}`;
    else return `${vals[0]}.${vals[1]}.${date.getFullYear()}`;
}

function close_other_sessions(button = null) {
    vex.dialog.confirm({
        message: "Вы точно хотите выйти со всех устройств, кроме этого?",
        buttons: [
            $.extend({}, vex.dialog.buttons.YES, { text: 'Да',
                className: 'button button-s round-small shadow-large button-border button-full border-highlight color-green1-dark bg-transparent'}),
            $.extend({}, vex.dialog.buttons.NO, { text: 'Нет',
                className: 'button button-s round-small shadow-large button-border button-full border-highlight color-red1-dark  bg-transparent'})
        ],
        callback: function (value) {
            if (value) {

                if (button != null) $(button).prop('disabled', true);
                let data = {
                    'token_form': $('#token_form').val(),
                };
                $.ajax({
                    url: base_url + 'company/company_ajax/ajax/close_other_sessions',
                    type: 'POST',
                    dataType: 'JSON',
                    data: data,
                    cache: false,
                    async: false,
                    success: function (data) {

                        //вставляем хэш
                        insert_csrf_hash(data);//функция берется из all_area_scripts!

                        var result = validate_data_server_response(data);

                        if (result) {
                            if (button != null) $(button).hide();
                            $('#close_ses_success').show();
                        }
                        vex.dialog.alert(data.text_message);
                    },
                });
            }
        }
    });
}
// $('#company_city').keyup(function () {
//     var query = $('#company_city').val();
//     $('#company_city').kladr({
//         type: $.kladr.type.city,
//         limit: 1,
//         check: function (obj) {
//             if (obj) {
//                 $('#company_city').css('color', 'black');
//             } else {
//                 $('#company_city').css('color', 'red');
//             }
//         },
//         receive: function (obj) {
//             if (obj.length > 0) {
//                 $('#name_city_company').attr('value', obj[0].name);
//                 $('#id_city_company_kladr').attr('value', obj[0].id);
//                 $('#zip_city_company').attr('value', obj[0].zip);
//             }
//         },
//         select: function (obj) {
//             $('#name_city_company').attr('value', obj.name);
//             $('#id_city_company_kladr').attr('value', obj.id);
//             $('#zip_city_company').attr('value', obj.zip);
//         },
//     });
// });

//для таблиц с данными
$(document).ready(function () {

    $('.data-table').DataTable({
        language: {
            'search': 'Поиск:',
            'lengthMenu': 'Показать _MENU_ записей',
            'info': 'Записи с _END_ до _START_ из _TOTAL_ записей',
            'infoEmpty': 'Записи с 0 до 0 из 0 записей',
            'paginate': {
                'first': 'Первая',
                'previous': 'Предыдущая',
                'next': 'Следующая',
                'last': 'Последняя',
            },
        },
        "paging": true
    });

    $('#data_table_deals').DataTable({
        order: [[ 0, "desc" ]],
        language: {
            'search': 'Поиск:',
            'lengthMenu': 'Показать _MENU_ записей',
            'info': 'Записи с _END_ до _START_ из _TOTAL_ записей',
            'infoEmpty': 'Записи с 0 до 0 из 0 записей',
            'paginate': {
                'first': 'Первая',
                'previous': 'Предыдущая',
                'next': 'Следующая',
                'last': 'Последняя',
            },
        }
    });

    function ajaxPostDownload(url, table) {
        var data = {
            'token_form': $('#token_form').val(),
        };
        $('#loading').html('<strong>Загрузка остальных сделок...</strong>');
        $.ajax({
            url: base_url + url,
            type: 'POST',
            dataType: 'JSON',
            data: data,
            cache: false,
            success: function (data) {
                //вставляем хэш
                insert_csrf_hash(data);//функция берется из all_area_scripts!

                var result = validate_data_server_response(data);
                if (result) {
                    table.api().rows.add(data.data).draw();
                    $('#loading').html('');
                    table.api().order([0, 'desc']).draw();
                } else {
                    swal({title: 'Ошибка подзагрузки сделок', type: 'info'});
                    $('#loading').html('');
                }
            },
        });
    }

    $('#tasks').DataTable({
        language: {
            'search': 'Поиск:',
            'lengthMenu': 'Показать _MENU_ записей',
            'info': 'Записи с _START_ до _END_ из _TOTAL_ записей',
            'infoEmpty': 'Записи с 0 до 0 из 0 записей',
            'paginate': {
                'first': 'Первая',
                'previous': 'Предыдущая',
                'next': 'Следующая',
                'last': 'Последняя',
            },
        },
    }).on('draw', function () {
        editable();
    });

    editable();

    $('#reviews').DataTable({
        language: {
            'search': 'Поиск:',
            'lengthMenu': 'Показать _MENU_ записей',
            'info': 'Записи с _END_ до _START_ из _TOTAL_ записей',
            'infoEmpty': 'Записи с 0 до 0 из 0 записей',
            'paginate': {
                'first': 'Первая',
                'previous': 'Предыдущая',
                'next': 'Следующая',
                'last': 'Последняя',
            },
        },
    });
    $('#reviews_comp').DataTable({
        language: {
            'search': 'Поиск:',
            'lengthMenu': 'Показать _MENU_ записей',
            'info': 'Записи с _END_ до _START_ из _TOTAL_ записей',
            'infoEmpty': 'Записи с 0 до 0 из 0 записей',
            'paginate': {
                'first': 'Первая',
                'previous': 'Предыдущая',
                'next': 'Следующая',
                'last': 'Последняя',
            },
        },
    });
});


//изменение статуса компании
$('#status_company').on('change', function () {
    var data = {
        'status_company': $('#status_company option:selected').val(),
        'company_id': $('#company_id').val(),
        'token_form': $('#token_form').val(),
    };

    $('#prichina').css({'display': 'none'});

    //если компаниию манагер хочет заблочить, тогда
    //тормозим все события и показываем ему форму для комментария
    if (data.status_company == 0) {
        $('#prichina').css({'display': 'block'});
        return;
    }

    var action = $(this).attr('action');

    send_data(action, data);

});

$('.change-status').on('click', function () {
    //если компаниию манагер хочет заблочить, тогда
    //тормозим все события и показываем ему форму для комментария
    var status = parseInt(this.dataset.status);
    if (status == 0) {
        $('#prichina').css({'display': 'block'});
        return;
    }

    if(!confirm('Вы уверены, что хотите выполнить: "' + this.textContent + '"?')) return;
    var data = {
        'status_company': status,
        'company_id': $('#company_id').val(),
        'token_form': $('#token_form').val(),
    };

    $('#prichina').css({'display': 'none'});

    var action = this.dataset.action;

    send_data(action, data);
});

$('#sub_period_company').submit(function (e) {
    e.preventDefault();

    var data = {
        'ap_start': $('#ap_start').val(),
        'ap_end': $('#ap_end').val(),
        'company_id': $('#company_id').val(),
        'token_form': $('#token_form').val(),
    };

    var action = $(this).attr('action');

    send_data(action, data);

});

//изменение ежемесячного лимита
$('#change_month_limit').submit(function (e) {
    e.preventDefault();

    var data = {
        'month_limit': Number($('#month_limit').val()) * 100,
        'company_id': Number($('#company_id').val()),
        'token_form': $('#token_form').val(),
    };

    var action = $(this).attr('action');

    send_data(action, data);
});

//изменение менеджера у компании
$('#manager').on('input', function () {
    var val = this.value;
    let arr = $('#manager_list option').filter(function (index) {
        return val === this.value
    });
    //send ajax request

    var data = {
        'manager_id': arr[0].text,
        'company_id': Number($('#company_id').val()),
        'token_form': $('#token_form').val(),
    };
    var action = base_url +
        'admin/admin_ajax/data_company/change_manager_in_company';

    send_data(action, data);
});

$('#submit_ban_comment').submit(function (e) {
    e.preventDefault();

    var data = {
        'status_company': 0,
        'ban_comment': String($('#ban_comment').val()),
        'company_id': $('#company_id').val(),
        'token_form': $('#token_form').val(),
    };

    var action = $(this).attr('action');

    send_data(action, data);
});

//изменение категории у компании
$('#edit_categories_company :checkbox').change(function () {
    var action = $('#edit_categories_company').attr('action');
    var data = {
        'company_id': $('#company_id').val(),
        'token_form': $('#token_form').val(),
    };
    // this will contain a reference to the checkbox
    if (this.checked) {
        // the checkbox is now checked
        var category_id = $(this).val();
        var cat_action = 'add';
    } else {
        // the checkbox is now no longer checked
        var category_id = $(this).val();
        var cat_action = 'delete';
    }

    data.category_action = cat_action;
    data.category_id = category_id;

    $.ajax({
        url: action,
        type: 'POST',
        data: data,
        cache: false,
        success: function (data) {

            //вставляем хэш
            insert_csrf_hash(data);//функция берется из all_area_scripts!

            //валидируем данные
            var result = validate_data_server_response(data);

            if (result) {//если все успешно
                swal('Отлично!', data.text_message, 'success');
            } else {//если потерпели ошибку
                swal('Ошибка!', data.text_message, 'error');
            }

            // console.log(data);
        },
    });

});

//Выдача кредита
$('#credit').submit(function (e) {
    e.preventDefault();

    var data = {
        'credit': Number($('#credit_dolg').val()) * 100,
        'credit_comment': $('#credit_comment').val(),
        'company_id': Number($('#company_id').val()),
        'token_form': $('#token_form').val(),
    };

    var action = $(this).attr('action');

    if (confirm('Вы уверены, что хотите изменить эти данные?')) {
        send_data(action, data);
    }
});

//отправка денег компании
$('#send_money').submit(function (e) {
    e.preventDefault();

    var data = {
        'summa_otpravki': Number($('#summa_otpravki').val()) * 100,
        'company_id': Number($('#company_id').val()),
        'token_form': $('#token_form').val(),
    };

    if (data.summa_otpravki < 0) {
        swal('Ошибка!', 'Сумма не может быть меньше нуля!', 'error');
        return;
    }

    var action = $(this).attr('action');

    if (confirm('Вы уверены, что хотите это сделать?')) {
        send_data(action, data);
    }
});

$('#send_partner_bonus').submit(function (e) {

    e.preventDefault();

    var data = {
        'partner_bonus': Number($('#partner_bonus').val()) * 100,
        'company_id': Number($('#company_id').val()),
        'token_form': $('#token_form').val(),
    };

    if (data.partner_bonus < 0) {
        swal('Ошибка!', 'Сумма не может быть меньше нуля!', 'error');
        return;
    }

    var action = $(this).attr('action');

    if (confirm('Вы уверены, что хотите это сделать?')) {
        send_data(action, data);
    }

});

$('#update_rub_balance').submit(function (e) {

    e.preventDefault();

    var data = {
        'rub_balance': Number($('#rub_balance').val()) * 100,
        'company_id': Number($('#company_id').val()),
        'token_form': $('#token_form').val(),
    };

    var action = $(this).attr('action');

    if (confirm('Вы уверены, что хотите изменить эти данные?')) {
        send_data(action, data);
    }

});

$('#update_barter_balance').submit(function (e) {

    e.preventDefault();

    var data = {
        'barter_balance': Number($('#barter_balance').val()) * 100,
        'company_id': Number($('#company_id').val()),
        'token_form': $('#token_form').val(),
    };

    var action = $(this).attr('action');

    if (confirm('Вы уверены, что хотите изменить эти данные?')) {
        send_data(action, data);
    }

});
$('#update_who_invite_company').submit(function (e) {

    e.preventDefault();

    var data = {
        'id_who_invite_company': Number($('#id_who_invite_company').val()),
        'company_id': Number($('#company_id').val()),
        'token_form': $('#token_form').val(),
    };

    var action = $(this).attr('action');

    if (confirm('Вы уверены, что хотите изменить эти данные?')) {
        send_data(action, data);
    }

});
$('#update_comment').submit(function (e) {

    e.preventDefault();

    var data = {
        'company_comment': $('#company_comment').val(),
        'company_id': Number($('#company_id').val()),
        'token_form': $('#token_form').val(),
    };

    var action = $(this).attr('action');

    if (confirm('Вы уверены, что хотите изменить эти данные?')) {
        send_data(action, data);
    }

});
$('#update_sverhlimit_company').submit(function (e) {

    e.preventDefault();

    var data = {
        'sverh_limit': Number($('#sverh_limit').val()),
        'company_id': Number($('#company_id').val()),
        'token_form': $('#token_form').val(),
    };

    var action = $(this).attr('action');

    if (confirm('Вы уверены, что хотите изменить эти данные?')) {
        send_data(action, data);
    }

});
//изменене статуса рекомендации компании
$('input[type=radio][name=recommend_company]').change(function () {

    var data = {
        'status_recommended': this.value,
        'company_id': Number($('#company_id').val()),
        'token_form': $('#token_form').val(),
    };

    var action = base_url +
        'admin/admin_ajax/data_company/change_status_recommended_company';

    send_data(action, data);
});

$('.send_form').submit(function (e) {

    e.preventDefault();

    var data = new FormData(this);
    var action = $(this).attr('action');

    //добавляем скрытые поля
    var fields = $('.hidden_field');
    for (var i = 0; i < fields.length; i++) {
        data.append($(fields[i]).attr('id'), $(fields[i]).val());
    }
    send_data_profile_super_admin(action, data);
});

function send_data(action, data) {
    $.ajax({
        url: action,
        type: 'POST',
        data: data,
        cache: false,
        //async: false,
        success: function (data) {

            //вставляем хэш
            insert_csrf_hash(data);//функция берется из all_area_scripts!

            //валидируем данные
            var result = validate_data_server_response(data);

            if (result) {//если все успешно
                swal('Отлично!', data.text_message, 'success');
                setTimeout(function () {
                    reload_page();
                }, 3000);
            } else {//если потерпели ошибку
                swal('Ошибка!', data.text_message, 'error');
            }

            // console.log(data);
        },
    });
}

//изменение профиля компании
//функция для обновления данных компании в кабинете супер.админа
$('#update_profile').submit(function (e) {
    e.preventDefault();

    var data = new FormData(this);
    var action = $(this).attr('action');

    //добавляем скрытые поля
    var fields = $('.hidden_field');
    for (var i = 0; i < fields.length; i++) {
        data.append($(fields[i]).attr('id'), $(fields[i]).val());
    }

    send_data_profile_super_admin(action, data);

});
$('#update_news').submit(function (e) {
    e.preventDefault();

    var data = new FormData(this);
    data.append('token_form', $('#token_form').val());
    data.append('news_id', $('#news_id').val());
    var action = $(this).attr('action');


    send_data_profile_super_admin(action, data);

});

$('#update_logo').submit(function (e) {

    e.preventDefault();

    var data = new FormData(this);
    var action = $(this).attr('action');

    //добавляем скрытые поля
    var fields = $('.hidden_field');
    for (var i = 0; i < fields.length; i++) {
        data.append($(fields[i]).attr('id'), $(fields[i]).val());
    }

    send_data_profile_super_admin(action, data);

});

$('#add_goods').submit(function (e) {

    e.preventDefault();

    var data = new FormData(this);
    var action = $(this).attr('action');

    //добавляем скрытые поля
    var fields = $('.hidden_field');
    for (var i = 0; i < fields.length; i++) {
        data.append($(fields[i]).attr('id'), $(fields[i]).val());
    }

    send_data_profile_super_admin(action, data);

});

$('#update_img_news').submit(function (e) {

    e.preventDefault();

    var data = new FormData(this);
    data.append('token_form', $('#token_form').val());
    data.append('news_id', $('#news_id').val());
    var action = $(this).attr('action');

    //добавляем скрытые поля

    send_data_profile_super_admin(action, data);

});

$('#update_vk_user_id').submit(function (e) {

    e.preventDefault();

    var data = new FormData(this);
    var action = $(this).attr('action');

    //добавляем скрытые поля
    var fields = $('.hidden_field');
    for (var i = 0; i < fields.length; i++) {
        data.append($(fields[i]).attr('id'), $(fields[i]).val());
    }

    send_data_profile_super_admin(action, data);

});

function send_data_profile_super_admin(action, data) {
    $.ajax({
        url: action,
        type: 'POST',
        data: data,
        processData: false,
        contentType: false,
        cache: false,
        async: false,
        success: function (data) {
            //вставляем хэш
            insert_csrf_hash(data);//функция берется из all_area_scripts!

            //валидируем данные
            var result = validate_data_server_response(data);

            if (result) {//если все успешно
                swal('Отлично!', data.text_message, 'success');

                setTimeout(function () {
                    reload_page();
                }, 3000);
            } else {//если потерпели ошибку
                swal('Ошибка!', data.text_message, 'error');
            }
        },
    });
}

$('#add_new_manager').submit(function (e) {
    e.preventDefault();

    $.ajax({
        url: $(this).attr('action'),
        type: 'POST',
        data: $(this).serialize(),
        cache: false,
        success: function (data) {

            //вставляем хэш
            insert_csrf_hash(data);//функция берется из all_area_scripts!

            //валидируем данные
            var result = validate_data_server_response(data);
            // validate_data_server_response(data);

            if (result) {//если все успешно
                $('#manager_id').val(data.manager_id);
                $('#add_new_manager').hide('fast');
                $('#manager_cities_block').css({'display': 'block'});
                swal('Отлично!', data.text_message, 'success');
            } else {//если потерпели ошибку
                swal('Ошибка!', data.text_message, 'error');
            }

            // console.log(data);
        },
    });

});

$('#manager_cities :checkbox').change(function () {
    var action = $('#manager_cities').attr('action');
    var data = {
        'manager_id': $('#manager_id').val(),
        'token_form': $('#token_form').val(),
    };
    // this will contain a reference to the checkbox
    if (this.checked) {
        // the checkbox is now checked
        $(this).prop('checked', true);
        var kladr_id = $(this).val();
        var city_action = 'add';
    } else {
        // the checkbox is now no longer checked
        $(this).prop('checked', false);
        var kladr_id = $(this).val();
        var city_action = 'delete';
    }

    data.city_action = city_action;
    data.kladr_id = kladr_id;

    $.ajax({
        url: action,
        type: 'POST',
        data: data,
        cache: false,
        success: function (data) {

            //вставляем хэш
            insert_csrf_hash(data);//функция берется из all_area_scripts!

            //валидируем данные
            var result = validate_data_server_response(data);

            if (result) {//если все успешно
                swal('Отлично!', data.text_message, 'success');
            } else {//если потерпели ошибку
                swal('Ошибка!', data.text_message, 'error');
            }

        },
    });

});

$('#manager_status').on('change', function () {
    var data = {
        'status': $('#manager_status option:selected').val(),
        'manager_id': $('#manager_id').val(),
        'token_form': $('#token_form').val(),
    };

    var action = $(this).attr('action');

    send_data(action, data);

});

/**********************************
 Управление категориями
 **********************************/
//переключатель - Вложить категорию в существуюущую
$('input[type=radio][name=have_parent]').change(function () {

    if (this.value == 0) {
        var display = 'none';
    } else {
        var display = 'block';
    }

    $('#cats_list').css({'display': display});

});

$('#add_new_category').submit(function (e) {
    e.preventDefault();
    var data = $(this).serialize();
    var action = $(this).attr('action');
    send_data(action, data);
});

$('#delete_category').submit(function (e) {
    e.preventDefault();
    var data = $(this).serialize();
    var action = $(this).attr('action');
    if (confirm('Вы уверены, что хотите удалить эту категорию?')) {
        send_data(action, data);
    }
});

/**********************************
 Функция регистрации компании из админки
 **********************************/
$('#registration_form').submit(function (e) {
    e.preventDefault();
    var data = new FormData(this);

    //добавляем скрытые поля
    var fields = $('.hidden_field');
    for (var i = 0; i < fields.length; i++) {
        data.append($(fields[i]).attr('id'), $(fields[i]).val());
    }

    $.ajax({
        url: $('#registration_form').attr('action'),
        type: 'POST',
        data: data,
        processData: false,
        contentType: false,
        cache: false,
        async: false,
        success: function (data) {
            insert_csrf_hash(data);//функция берется из all_area_scripts!

            //валидируем данные
            var result = validate_data_server_response(data);

            if (result) {//если все успешно
                swal('Отлично!',
                    'Компания успешно зарегистрирована! Сейчас страница перезагрузится.',
                    'success');
                setTimeout(function () {
                    reload_page();
                }, 1000);
            }
            else {
                swal('Ошибка!',
                    'Проверьте заполненные поля.',
                    'error');
            }
        },
    });
});

$('#company_phone').blur(function () {

    $.ajax({
        type: 'POST',
        url: base_url + 'public_ajax/registr_and_login/check_phone',
        data: {
            'company_phone': $('#company_phone').val(),
            'token_form': $('#token_form').val(),
        },
        dataType: 'JSON',
        success: function (data) {
            //валидируем данные, т.е. смотрим, что пришло с сервера
            validate_data_server_response(data);
            //вставляем хэш
            insert_csrf_hash(data);//функция берется из all_area_scripts!
        },
    });

});

/**********************************
 Функция отмены или подтверждения сделки админом
 **********************************/
function accept_or_cancel_deal(status_deal, deal_id) {

    var data = {
        'deal_id': deal_id,
        'token_form': $('#token_form').val(),
    };

    var action = base_url + 'admin/admin_ajax/deals/admin_cancel_deal';

    if (confirm('Вы уверены?')) {
        send_data(action, data);
    }
}

function add_task() {

    var data = {
        'token_form': $('#token_form').val(),
        'client_info': $('#task_client_info').val(),
        'author': $('#task_author').val(),
        'executor': $('#task_executor').val(),
        'comment': $('#task_comment').val(),
    };

    $.ajax({
        url: base_url + 'admin/admin_ajax/tasks/addtask',
        type: 'POST',
        dataType: 'JSON',
        data: data,
        cache: false,
        success: function (data) {

            //вставляем хэш
            insert_csrf_hash(data);//функция берется из all_area_scripts!

            var result = validate_data_server_response(data);
            if (result) {
                swal('Успешно', data.text_message, 'success');
            } else {
                swal('Ошибка!', data.text_message, 'error');
            }
        },
    });
}

function editable() {
    $('#tasks').editableTableWidget({needEdits: [4, 5]}).on('change', 'tbody td.action', function (evt, newValue) {
        let parent = this.parentNode;

        let $parent = $(parent);
        $action = evt.target.className.split(' ')[1];
        var data = {
            'token_form': $('#token_form').val(),
            'param': $action,
            'task_id': $parent.data('task-id'),
            'value': newValue,
        };

        $.ajax({
            url: base_url + 'admin/admin_ajax/tasks/tasksaction',
            type: 'POST',
            data: data,
            cache: false,
            success: function (data) {

                //вставляем хэш
                insert_csrf_hash(data);//функция берется из all_area_scripts!

                //валидируем данные
                var result = validate_data_server_response(data);

                if (!result) {
                    swal('Ошибка!', data.text_message, 'error');
                }
            },
        });
    });

    $('#not_active_comp_table').editableTableWidget({needEdits: [4]}).on('change', function (evt, newValue) {
        var target = evt.target;

        var data = {
            'token_form': $('#token_form').val(),
            'field': 'comment',
            'value': newValue,
            'company_id': target.dataset.companyid,
        };

        $.ajax({
            url: base_url + 'admin/admin_ajax/data_company/changeCompanyField',
            type: 'POST',
            data: data,
            cache: false,
            success: function (data) {

                //вставляем хэш
                insert_csrf_hash(data);//функция берется из all_area_scripts!

                //валидируем данные
                var result = validate_data_server_response(data);

                if (!result) {
                    swal('Ошибка!', data.text_message, 'error');
                }
            },
        });
    });
    $('#foreign_table').editableTableWidget({needEdits: [4]}).on('change', function (evt, newValue) {
        var target = evt.target;

        var data = {
            'token_form': $('#token_form').val(),
            'field': 'comment',
            'value': newValue,
            'company_id': target.dataset.companyid,
        };

        $.ajax({
            url: base_url + 'admin/admin_ajax/data_company/changeCompanyForeign',
            type: 'POST',
            data: data,
            cache: false,
            success: function (data) {

                //вставляем хэш
                insert_csrf_hash(data);//функция берется из all_area_scripts!

                //валидируем данные
                var result = validate_data_server_response(data);

                if (!result) {
                    swal('Ошибка!', data.text_message, 'error');
                }
            },
        });
    });


}

function delete_task($node) {
    let $parent = $($node.parentNode);

    let data = {
        'token_form': $('#token_form').val(),
        'task_id': $parent.data('task-id'),
    };

    $.ajax({
        url: base_url + 'admin/admin_ajax/tasks/deletetask',
        type: 'POST',
        dataType: 'JSON',
        data: data,
        cache: false,
        success: function (data) {

            //вставляем хэш
            insert_csrf_hash(data);//функция берется из all_area_scripts!

            var result = validate_data_server_response(data);
            if (result) {
                $parent.empty();
            } else {
                swal('Ошибка!', data.text_message, 'error');
            }
        },
    });
}

function change_task_status($node) {
    let $parent = $(($node.parentNode).parentNode);
    let data = {
        'token_form': $('#token_form').val(),
        'task_id': $parent.data('task-id'),
        'status': $($node).val(),
    };

    $.ajax({
        url: base_url + 'admin/admin_ajax/tasks/changestatus',
        type: 'POST',
        dataType: 'JSON',
        data: data,
        cache: false,
        success: function (data) {

            //вставляем хэш
            insert_csrf_hash(data);//функция берется из all_area_scripts!

            var result = validate_data_server_response(data);
            if (result) {
                $parent.removeClass('success warning danger').addClass($($node).val());
            } else {
                swal('Ошибка!', data.text_message, 'error');
            }
        },
    });
}

function delete_credit(id) {

    var data = {
        'token_form': $('#token_form').val(),
        'credit_id': id,
    };
    $.ajax({
        url: base_url + 'admin/admin_ajax/credits/delete_credit',
        type: 'POST',
        dataType: 'JSON',
        data: data,
        cache: false,
        success: function (data) {
            //вставляем хэш
            insert_csrf_hash(data);//функция берется из all_area_scripts!

            var result = validate_data_server_response(data);
            if (result) {
                swal('Успешно', data.text_message, 'success');
            } else {
                swal('Ошибка!', data.text_message, 'error');
            }
        },
    });
}

function delete_review(id, btn) {
    var data = {
        'token_form': $('#token_form').val(),
        'review_id': id,
    };
    $(btn).prop('disabled', true);
    $.ajax({
        url: base_url + 'admin/admin_ajax/review/delete_review',
        type: 'POST',
        dataType: 'JSON',
        data: data,
        cache: false,
        success: function (data) {
            //вставляем хэш
            insert_csrf_hash(data);//функция берется из all_area_scripts!

            var result = validate_data_server_response(data);
            $(btn).prop('disabled', false);
            if (result) {
                swal('Успешно', data.text_message, 'success');
            } else {
                swal('Ошибка!', data.text_message, 'error');
            }
        },
    });
}

function update_cash_status(id, status) {
    let data = {
        'token_form': $('#token_form').val(),
        'cash_id': id,
        'status': status,
    };
    $.ajax({
        url: base_url + 'admin/admin_ajax/credits/update_cash',
        type: 'POST',
        dataType: 'JSON',
        data: data,
        cache: false,
        success: function (data) {
            //вставляем хэш
            insert_csrf_hash(data);//функция берется из all_area_scripts!

            var result = validate_data_server_response(data);
            if (result) {
                swal('Успешно', data.text_message, 'success');
            } else {
                swal('Ошибка!', data.text_message, 'error');
            }
        },
    });
}

function changeHidden(id, button) {

    let data = {
        'token_form': $('#token_form').val(),
        'company_id': id,
        'value': Number(button.checked),
    };

    $.ajax({
        url: base_url + 'admin/admin_ajax/data_company/hidden',
        type: 'POST',
        dataType: 'JSON',
        data: data,
        cache: false,
        success: function (data) {
            //вставляем хэш
            insert_csrf_hash(data);//функция берется из all_area_scripts!

            var result = validate_data_server_response(data);
            if (result) {
                swal('Успешно', data.text_message, 'success');
            } else {
                swal('Ошибка!', data.text_message, 'error');
            }
        },
    });
}
function changeBarter_job(id, button) {

    let data = {
        'token_form': $('#token_form').val(),
        'company_id': id,
        'value': Number(button.checked),
    };

    $.ajax({
        url: base_url + 'admin/admin_ajax/data_company/barter_job',
        type: 'POST',
        dataType: 'JSON',
        data: data,
        cache: false,
        success: function (data) {
            //вставляем хэш
            insert_csrf_hash(data);//функция берется из all_area_scripts!

            var result = validate_data_server_response(data);
            if (result) {
                swal('Успешно', data.text_message, 'success');
            } else {
                swal('Ошибка!', data.text_message, 'error');
            }
        },
    });
}
function update_description(button, update_id, status, company_id) {
    let data = {
        token_form: $('#token_form').val(),
        desc: $(button).parents(".card-box").find('textarea').val(),
        status: status,
        update_id: update_id,
        company_id: company_id,
    };
    $.ajax({
        url: base_url + 'admin/admin_ajax/data_company/update_description',
        type: 'POST',
        dataType: 'JSON',
        data: data,
        cache: false,
        success: function (data) {
            //вставляем хэш
            insert_csrf_hash(data);//функция берется из all_area_scripts!

            var result = validate_data_server_response(data);
            if (result) {
                swal('Успешно', data.text_message, 'success');
            } else {
                swal('Ошибка!', data.text_message, 'error');
            }
        },
    });
}

function hideAdvert(id) {
    let data = {
        token_form: $('#token_form').val(),
        advert_id: id,
    };
    $.ajax({
        url: base_url + 'admin/admin_ajax/data_company/hide_advert',
        type: 'POST',
        dataType: 'JSON',
        data: data,
        cache: false,
        success: function (data) {
            insert_csrf_hash(data);//функция берется из all_area_scripts!

            var result = validate_data_server_response(data);
            if (result) {
                swal('Успешно', data.text_message, 'success');
            } else {
                swal('Ошибка!', data.text_message, 'error');
            }
        },
    });
}

function hideReviewscomp(id, elem = null) {
    let data = {
        token_form: $('#token_form').val(),
        Reviewscomp_id: id,
        type: 0
    };
    $.ajax({
        url: base_url + 'admin/admin_ajax/data_company/hide_delete_reviews_comp',
        type: 'POST',
        dataType: 'JSON',
        data: data,
        cache: false,
        success: function (data) {
            insert_csrf_hash(data);//функция берется из all_area_scripts!

            var result = validate_data_server_response(data);
            if (result) {
                if (elem != null) {
                    $(elem).prop('disabled', true);
                    $(elem).closest('tr').prop('class', 'success');
                }
                swal('Успешно', data.text_message, 'success');
            } else {
                swal('Ошибка!', data.text_message, 'error');
            }
        },
    });
}

function deleteReviewscomp(id, elem = null) {

    swal({
        title: 'Удаление отзыва',
        text: 'Вы действительно хотите удалить отзыв?',
        icon: 'warning',
        buttons: true,
    }).then((result) => {
        if (result) {

            if (elem != null) {
                $(elem).prop('disabled', true);
                $(elem).closest('tr').prop('class', 'danger');
            }

            let data = {
                token_form: $('#token_form').val(),
                Reviewscomp_id: id,
                type: 1
            };

            $.ajax({
                url: base_url + 'admin/admin_ajax/data_company/hide_delete_reviews_comp',
                type: 'POST',
                dataType: 'JSON',
                data: data,
                cache: false,
                success: function (data) {

                    insert_csrf_hash(data);//функция берется из all_area_scripts!

                    var result = validate_data_server_response(data);
                    if (result) {
                        swal('Успешно', data.text_message, 'success');
                    } else {
                        swal('Ошибка!', data.text_message, 'error');
                    }
                },
            });
        }
    });

}


function banCompany(id) {

    let data = {
        'token_form': $('#token_form').val(),
        'company_id': id,
    };

    if(!confirm('Вы точно хотите заблокировать компанию?')) return;

    $.ajax({
        url: base_url + 'admin/admin_ajax/data_company/ban_Company',
        type: 'POST',
        dataType: 'JSON',
        data: data,
        cache: false,
        success: function (data) {

            //вставляем хэш
            insert_csrf_hash(data);//функция берется из all_area_scripts!

            var result = validate_data_server_response(data);
            if (result) {
                swal('Успешно', data.text_message, 'success');
                setTimeout(function () {
                    reload_page();
                }, 1000);

            } else {
                swal('Ошибка!', data.text_message, 'error');
            }
        },
    });
}
function newsStatus(id, type) {

    let data = {
        'token_form': $('#token_form').val(),
        'news_id': id,
        'type':type,

    };

    if(!confirm('Вы точно хотите скрыть новость?')) return;

    $.ajax({
        url: base_url + 'admin/admin_ajax/data_company/news_hidden',
        type: 'POST',
        dataType: 'JSON',
        data: data,
        cache: false,
        success: function (data) {

            //вставляем хэш
            insert_csrf_hash(data);//функция берется из all_area_scripts!

            var result = validate_data_server_response(data);
            if (result) {
                swal('Успешно', data.text_message, 'success');
                setTimeout(function () {
                    reload_page();
                }, 1000);

            } else {
                swal('Ошибка!', data.text_message, 'error');
            }
        },
    });
}

function changeComment(id, comment) {

    let data = {
        'token_form': $('#token_form').val(),
        'company_id': id,
        'comment': comment,
    };

    $.ajax({
        url: base_url + 'admin/admin_ajax/data_company/comment',
        type: 'POST',
        dataType: 'JSON',
        data: data,
        cache: false,
        success: function (data) {

            //вставляем хэш
            insert_csrf_hash(data);//функция берется из all_area_scripts!

            var result = validate_data_server_response(data);
            if (result) {
                swal('Успешно', data.text_message, 'success');
                setTimeout(function () {
                    reload_page();
                }, 1000);

            } else {
                swal('Ошибка!', data.text_message, 'error');
            }
        },
    });
}
function changeManager(id) {

    let data = {
        'token_form': $('#token_form').val(),
        'company_id': id,
    };

    $.ajax({
        url: base_url + 'admin/admin_ajax/data_company/manager',
        type: 'POST',
        dataType: 'JSON',
        data: data,
        cache: false,
        success: function (data) {

            //вставляем хэш
            insert_csrf_hash(data);//функция берется из all_area_scripts!

            var result = validate_data_server_response(data);
            if (result) {
                swal('Успешно', data.text_message, 'success');
                setTimeout(function () {
                    reload_page();
                }, 1000);

            } else {
                swal('Ошибка!', data.text_message, 'error');
            }
        },
    });
}

function update_companies_credits(lol = 1) {
    let data = {
        'token_form': $('#token_form').val(),
        'memes': lol,
    };
    $.ajax({
        url: base_url + 'admin/admin_ajax/data_company/update_companies_credits',
        type: 'POST',
        dataType: 'JSON',
        data: data,
        cache: false,
        success: function (data) {
            //вставляем хэш
            insert_csrf_hash(data);//функция берется из all_area_scripts!

            var result = validate_data_server_response(data);

            if (result) {
                swal('Успешно', data.text_message, 'success');
            } else {
                swal('Ошибка!', data.text_message, 'error');
            }
        },
    });
}

function delete_product(product_id, company_id) {
    let data = {
        'product_id': product_id,
        'company_id': company_id,
        'token_form': $('#token_form').val(),
    };
    $.ajax({
        url: base_url + 'admin/admin_ajax/data_company/deleteproduct',
        type: 'POST',
        dataType: 'JSON',
        data: data,
        cache: false,
        success: function (data) {

            //вставляем хэш
            insert_csrf_hash(data);//функция берется из all_area_scripts!

            var result = validate_data_server_response(data);
            if (result) {
                swal('Успешно', data.text_message, 'success');
                /*setTimeout(function () {
                    reload_page();
                }, 1000);*/
            } else {
                swal('Ошибка!', data.text_message, 'error');
            }
        },
    });
}

$('.ajax_send').submit(function (e) {

    e.preventDefault();

    var data = new FormData(this);
    data.append('token_form', $('#token_form').val());
    var action = $(this).attr('action');

    send_data_profile_super_admin(action, data);
});

function close_other_sessions() {

    swal({
        title: 'Закрытие сессий',
        text: 'Вы действительно хотите выйти со всех остальных компьютеров?',
        icon: 'warning',
        buttons: true,
    }).then((result) => {
        if (result) {

            let data = {
                token_form: $('#token_form').val(),
            };

            $.ajax({
                url: base_url + 'admin/admin_ajax/data_company/close_other_sessions',
                type: 'POST',
                dataType: 'JSON',
                data: data,
                cache: false,
                async: false,
                error: function () {
                    swal('Ошибка!', 'Ошибка запроса', 'error');
                },
                success: function (data) {

                    insert_csrf_hash(data);//функция берется из all_area_scripts!

                    var result = validate_data_server_response(data);
                    if (result) {
                        swal('Успешно', data.text_message, 'success');
                    } else {
                        swal('Ошибка!', data.text_message, 'error');
                    }
                },
            });
        }
    });
}

function loadCoupons(coupon_status = -1, company_id = 0) {

    swal('Идет загрузка...', 'Пожалуйста, подождите.', 'info');

    $('#data_table_coupons').DataTable().clear().draw();

    let data_s = {
        'token_form': $('#token_form').val(),
        'status': coupon_status,
        'company_id': company_id,
    };
    $.ajax({
        url: base_url + 'admin/admin_ajax/data_company/load_coupons',
        method: 'POST',
        data: data_s,
    }).done(function (data) {

        insert_csrf_hash(data);//функция берется из all_area_scripts!

        validate_data_server_response(data);

        if (data.data.length > 0) {

            $.each(data.data, function (index, coupon) {

                let clas = 'danger';
                let status = 'Первичная инициализация';
                switch (parseInt(coupon.status))
                {
                    case 0:
                        status = "Не использован";
                        clas = "warning";
                        break;
                    case 1:
                        status = "Использован";
                        clas = "success";
                        break;
                    case 2:
                        status = "Истек";
                        clas = "danger";
                }

                let row = [coupon.created_at];
                if (company_id == 0) {
                    row.push("<a href=\"<?php echo site_url('admin/company_detail?company_id='); ?>" + coupon.company_id + "\" >" + coupon.company_name + "</a>");
                }
                row.push(coupon.summa / 100 + "<i class=\"fa fa-rub\" aria-hidden=\"true\"></i>",
                    coupon.deal_sum / 100 + "<i class=\"fa fa-rub\" aria-hidden=\"true\"></i>",
                    coupon.deal_id,
                    coupon.deal_date,
                    coupon.date_expire,
                    status
                );
                $("#data_table_coupons").DataTable().row.add(row).nodes().to$().addClass( clas );
            });//End each

            $("#data_table_coupons").DataTable().draw();
        }//end if

        swal('Готово', 'Загрузка завершена!', 'success');
        setTimeout(function () {
            swal.close();
        }, 500);
    });//end ajax
}

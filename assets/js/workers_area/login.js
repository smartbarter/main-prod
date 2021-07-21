$('#login_form').submit(function (e) {

    e.preventDefault();
    var data = {
        'company_login_phone': $('#company_login_phone').val(),
        'company_login_password': $('#company_login_password').val(),
        'token_form': $('#token_form').val(),
    };

    $.ajax({
        url: $('#login_form').attr('action'),
        type: 'POST',
        dataType: 'JSON',
        data: data,
        cache: false,
        success: function (data) {

            //валидируем данные
            var result = validate_data_server_response(data);

            if (result) {
                redirect('login_form', 'success_login', 'admin/cabinet', data);
            } else {
                //вставляем хэш
                insert_csrf_hash(data);//функция берется из all_area_scripts!
            }

        }
    });

});
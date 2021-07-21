$('#company_city').keyup(function() {
  let $city = $('#company_city');
  $city.kladr({
    type: $.kladr.type.city,
    limit: 1,
    check: function(obj) {
      if (obj) {
        $('#company_city').css('color', 'black');
      } else {
        $('#company_city').css('color', 'red');
      }
    },
    receive: function(obj) {
      if (obj.length > 0) {
        $('#name_city_company').attr('value', obj[0].name);
        $('#id_city_company_kladr').attr('value', obj[0].id);
        $('#zip_city_company').attr('value', obj[0].zip);
      }
    },
    select: function(obj) {
      $('#name_city_company').attr('value', obj.name);
      $('#id_city_company_kladr').attr('value', obj.id);
      $('#zip_city_company').attr('value', obj.zip);
    },
  });
});
//Функция регистрации компании в системе
$('#registration_form').submit(function(e) {
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
    success: function(data) {

      //валидируем данные
      var result = validate_data_server_response(data);

      if (result) {//если все успешно
        redirect('registration_form', 'success_registration', 'company/cabinet',
            data);

      } else {//если потерпели ошибку
        //вставляем хэш
        insert_csrf_hash(data);//функция берется из all_area_scripts!
      }
    },
  });
});

$('#company_phone').blur(function() {
  let login = $('#company_phone').val().replace(/\D/g, '');
  $.ajax({
    type: 'POST',
    url: base_url + 'public_ajax/registr_and_login/check_phone',
    data: {
      'company_phone': login,
      'token_form': $('#token_form').val(),
    },
    dataType: 'JSON',
    success: function(data) {
      //валидируем данные, т.е. смотрим, что пришло с сервера
      validate_data_server_response(data);
      //вставляем хэш
      insert_csrf_hash(data);//функция берется из all_area_scripts!
    },
  });
});

$('#login_form').submit(function(e) {
  e.preventDefault();
  let login = $('#company_login_phone').val().replace(/\D/g, '');
  var data = {
    'company_login_phone': login,
    'company_login_password': $('#company_login_password').val(),
    'token_form': $('#token_form').val(),
  };

  $.ajax({
    url: 'public_ajax/registr_and_login/login',
    type: 'POST',
    dataType: 'JSON',
    data: data,
    cache: false,
    success: function(data) {

      insert_csrf_hash(data);//функция берется из all_area_scripts!
      //валидируем данные
      var result = validate_data_server_response(data);

      if (result) {
        redirect('login_form', 'success_login', 'company/cabinet', data);
      }
    },
  });
});

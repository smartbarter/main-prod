$(document).ready(function() {






  //телефонная маска

  //Плавно скрываем элемент - точнее сообщения находящиеся в сессии
  $('.message_session').fadeIn(500).delay(6000).fadeOut(500);

  //тултипы
  //$('[data-toggle="tooltip"]').tooltip();

  //функция для аккордиона - категории
  // $('.categories').dcAccordion();

  //выделение активной ссылки
  $('ul.categories li a').each(function() {
    if (this.href == location.href) $(this).addClass('active');
  });

});

//Функция для вставки хэша в формы, после ответа сервера
function insert_csrf_hash(data) {
  $('#token_form').attr('value', data.csrf_token);
}

//функция для валидации данных
function validate_data_server_response(data) {
  if (data.status === 'fail') {
    if (data.response_data !== undefined) {
      //запускаем цикл, перебирая сообщения ошибок
      $.each(data.response_data, function(key, val) {
        if (val != '') {	//если у нас есть сообщение об ошибке, то вставляем данные в страницу
          $('#error_' + key).html(val);
        } else {	//иначе, если у нас нет сообщения с ошибкой, то просто засовываем пустоту в блок
          $('#error_' + key).html('');
        }
      });//each
    }
    //очищаем элементы с текстом ошибки
    //empty_errors();
    return false;
  } else if (data.status === 'success') {
    //очищаем элементы с текстом ошибки
    empty_errors();
    //$('.error').text('');
    return true;
  }
}

//Функция скрытия ошибок ч/з 6 секунд
function empty_errors() {
  setTimeout(function() {
    var divs = $('.error');
    for (var i = 0; i < divs.length; i++) {
      $(divs[i]).html('');
    }
  }, 6000);
}

function redirect(form, message_block, url, data) {
  $('#' + form).hide('fast');//прячем форму регистрации
  $('#' + message_block).css({'color': '#4fc162', 'font-size': '14px'});
  $('#' + message_block).html(data.text_message);//выводим сообщение с поздравлением

  var url_redirect = base_url + url;

  //редирект в личный кабинет, чтобы чел активировал telegram бота
  window.setTimeout('window.location.assign(\'' + url_redirect + '\')', 3000); //редирект после 3-х секунд
}

function reload_page() {
  //перезагрузка текущей страницы
  window.location.reload();
  // window.location = window.location.href;
}

//функция востановления пароля
function recover_password(type_user, step) {

  var open_form;
  var send_form;
  var send_data = {};

  switch (step) {
    case 'step_one':
      send_form = 'recover_password_step_one';
      open_form = 'recover_password_step_two';
      send_data.recover_password_phone = $('#recover_password_phone').val().replace(/\D/g, '');//$('#recover_password_phone').val();
      break;
    case 'step_two':
      send_form = 'recover_password_step_two';
      open_form = 'recover_password_step_three';
      send_data.activation_code = $('#activation_code').val();
      break;
    case 'step_three':
      send_form = 'recover_password_step_three';
      send_data.update_password = $('#update_password').val();
      break;
  }

  send_data.type_user = type_user;
  send_data.token_form = $('#token_form').val();

  if (step === 'step_three') {

    send_data.user_pass_phone = $('#recover_password_phone').val();

  }

  $.ajax({
    url: $('#' + send_form).attr('action'),
    type: 'POST',
    dataType: 'JSON',
    data: send_data,
    success: function(data) {

      //валидируем данные
      var result = validate_data_server_response(data);

      if (result) {
        insert_csrf_hash(data);

        if (step === 'step_one') {
          $('#user_pass_phone').
              attr('value', $('#recover_password_phone').val());
        }

        if (step !== 'step_three') {
          $('#' + send_form).hide('fast');//прячем форму
          $('#' + open_form).show('fast');//показываем форму
        } else {
          //иначе у нас 3 шаг, т.е. чел должен вбить пароль
          //мы принимаем пароль, пишем в БД и затем показываем сообщение
          //что все ок и скрываем форму
          $('#' + send_form).hide('fast');//прячем форму
          $('#success_update_password_msg').
              css({'color': '#4fc162', 'font-size': '16px'});
          $('#success_update_password_msg').html(data.success_message);
        }

      } else {
        //вставляем хэш
        insert_csrf_hash(data);
      }
    },
  });
}

function number_format( number, decimals, dec_point, thousands_sep ) {	// Format a number with grouped thousands
  //
  // +   original by: Jonas Raoni Soares Silva (http://www.jsfromhell.com)
  // +   improved by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
  // +	 bugfix by: Michael White (http://crestidg.com)

  var i, j, kw, kd, km, minus = "";

  if(number < 0){
    minus = "-";
    number = number*-1;
  }

  // input sanitation & defaults
  if( isNaN(decimals = Math.abs(decimals)) ){
    decimals = 2;
  }
  if( dec_point == undefined ){
    dec_point = ",";
  }
  if( thousands_sep == undefined ){
    thousands_sep = ".";
  }

  i = parseInt(number = (+number || 0).toFixed(decimals)) + "";

  if( (j = i.length) > 3 ){
    j = j % 3;
  } else{
    j = 0;
  }

  km = (j ? i.substr(0, j) + thousands_sep : "");
  kw = i.substr(j).replace(/(\d{3})(?=\d)/g, "$1" + thousands_sep);
  //kd = (decimals ? dec_point + Math.abs(number - i).toFixed(decimals).slice(2) : "");
  kd = (decimals ? dec_point + Math.abs(number - i).toFixed(decimals).replace(/-/, 0).slice(2) : "");


  return minus + km + kw + kd;
}

function datetime_format(date_string) {

  var date = new Date(date_string);
  var month = date.getMonth() + 1;

  return `${date.getDate()}.${month < 10 ? '0' + month : month}.${date.getFullYear()} ${date.getHours()}:${date.getMinutes()}`;
}
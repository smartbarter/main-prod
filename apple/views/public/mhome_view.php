<!DOCTYPE HTML>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title><?php echo PROJECT_NAME ?></title>
    <link rel="stylesheet" type="text/css" href="<?php echo site_url('assets/styles/all_area/mobile/style.css'); ?>?v3">
    <link rel="stylesheet" type="text/css"
          href="<?php echo site_url('assets/styles/all_area/mobile/framework.css'); ?> ">
    <link href="https://fonts.googleapis.com/css?family=IBM+Plex+Sans:300,300i,400,400i,500,500i,600,600i,700,700i|Roboto:300,300i,400,400i,500,500i,700,700i,900,900i"
          rel="stylesheet">

    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.8.2/css/all.css"
          integrity="sha384-oS3vJWv+0UjzBfQzYUhtDYW+Pj2yciDJxpsK1OYPAYjqT085Qq/1cq5FLXAZQ7Ay" crossorigin="anonymous">

    <!-- Don't forget to update PWA version (must be same) in pwa.js & manifest.json -->
    <link rel="manifest" href="_manifest.json" data-pwa-version="set_by_pwa.js">
    <link rel="apple-touch-icon" sizes="180x180" href="app/icons/icon-192x192.png">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    <script type="text/javascript" src="<?php echo site_url('assets/js/mobile/custom.js'); ?>"></script>
</head>

<body class="theme-light" data-highlight="blue2">

<div id="page">

    <div id="page-preloader">
        <div class="loader-main">
            <div class="preload-spinner border-highlight"></div>
        </div>
    </div>


    <div class="page-content">
        <div class="content  center-text top-50" style="">
            <img width="130" src="https://barter-business.ru/uploads/logo.png"
                                  class="round-image shadow-tiny bg-white ">
        </div>

        <div class="cover-slider owl-carousel owl-has-dots">
            <div data-height="320px" class="caption bottom-0">
                <div class=" center-text">

                    <h1 class="font-20">Максимизация прибыли бизнеса с</h1>
                    <h1 class="font-36 bottom-20 color-highlight ">Barter-Business</h1>
                    <p class="boxed-text-large font-16 bottom-20">
                        Новый подход к продажам товаров и услуг. Оптимизация расходов бизнеса. Экономия оборотных средств. Раскрутка и продвижение бренда.
                    </p>


                </div>
            </div>
            <div data-height="320px" class="caption bottom-0">
                <div class=" center-text">

                    <h1 class="font-20">Увеличьте свой товарооборот!</h1>

                    <p class="boxed-text-large font-16 bottom-20">
                        Создайте дополнительный поток новых клиентов. Полностью загружайте работой производство ваших товаров и услуг. Реализуйте их.
                    </p>


                </div>
            </div>
            <div data-height="320px" class="caption bottom-0">
                <div class=" center-text">

                    <h1 class="font-20">Найдите новых поставщиков!</h1>

                    <p class="boxed-text-large font-16 bottom-20">
                        Каталог партнеров системы включает свыше 5 000 компаний из различных сфер деятельности, который пополняется ежедневно!
                    </p>


                </div>
            </div>
            <div data-height="320px" class="caption bottom-0">
                <div class=" center-text">
                    <h1 class="font-20">Экономьте оборотные средства!</h1>

                    <p class="boxed-text-large font-16 bottom-20">
                        Оплачивайте необходимые для работы товары и услуги бартерными рублями, а не наличными. Превращайте их в дополнительную прибыль.
                    </p>


                </div>
            </div>
            <div data-height="320px" class="caption bottom-0">
                <div class=" center-text">
                    <h1 class="font-20">Раскрутите ваш бизнес!</h1>

                    <p class="boxed-text-large font-16 bottom-20">
                        Barter-Business – это площадка многостороннего бартерного обмена товарами и услугами. О вас узнают сотни потенциальных клиентов!
                    </p>
                </div>
            </div>

        </div>
        <p class="center-text bottom-70">
            <a href="#" data-menu="action-signup"
               class="button button-s round-small shadow-large bg-highlight bottom-20">Зарегистрироваться</a>
            <a href="#" data-menu="action-signin" id="btn_login"
               class="button button-s round-small button-full  color-highlight bg-transparent">Войти</a>
        </p>
    </div>


    <div class="menu-hider"></div>
    <div id="action-signin"
         class="menu-box menu-box-detached round-medium"
         data-menu-type="menu-box-bottom"
         data-menu-height="340"
         data-menu-effect="menu-parallax">

        <div class="page-title has-subtitle">
            <div class="page-title-left">
                <a href="#">Войти</a>

            </div>
            <div class="page-title-right">
                <a href="#" class="close-menu"><i class="fa fa-times-circle font-20 color-red2-dark"></i></a>
            </div>
        </div>

        <!--Log In Sheet-->
        <form id="login_form" autocomplete="off" method="POST">
            <div>
                <div id="login_loader" class="loader-main loader-inactive"><div class="preload-spinner border-highlight"></div></div>
            </div>
            <div class="content">
                <div class="input_home_public bottom-25">
                    <input id="company_login_phone" name="company_login_phone" type="text" maxlength="14" class="phone_number" placeholder="Телефон" />
                </div>
                <div id="error_company_login_phone" class="error"></div>
                <div class="input_home_public bottom-25">
                    <input type="password" placeholder="Пароль" id="company_login_password" required>
                </div>
                <div id="error_company_login_password" class="error"></div>

                <div id="error_message_login_fail" class="error txt_center"></div>
                <div id="success_login" class="txt_center"></div>

                <div class="float-right">
                    <a href="#" data-menu="action-forgot-1" class="left-text font-14">Напомнить пароль</a>
                </div>

                <div class="clear"></div>
            </div>
            <button style="width: calc(100% - 30px)" type="submit"
               class="button button-m button-full bg-highlight button-margins button-round-large shadow-huge">Войти</button>
        </form>
    </div>

    <!-- Sign Up Sheet-->
    <div id="action-signup"
         class="menu-box menu-box-detached round-medium"
         data-menu-type="menu-box-bottom"
         data-menu-height="440"
         data-menu-effect="menu-parallax">

        <div>
            <div id="login_loader" class="loader-main loader-inactive">
                <div class="preload-spinner border-highlight"></div>
            </div>
        </div>

        <div class="page-title has-subtitle">
            <div class="page-title-left">
                <a href="#">Зарегистрироваться</a>

            </div>
            <div class="page-title-right">
                <a href="#" class="close-menu"><i class="fa fa-times-circle font-20 color-red2-dark"></i></a>
            </div>
        </div>

        <form onsubmit="register()">
            <div class="content">
                <div class="input_home_public bottom-25">
                    <input type="name" placeholder="Название компании" id="company_name" required>
                </div>
                <div class="input_home_public bottom-25">
                    <input type="text" placeholder="Телефон" id="company_phone" class="phone_number" required>
                </div>
                <div id="error_company_phone" class="error"></div>

                <div class="input_home_public bottom-25">
                    <input type="text" placeholder="Город" id="company_city" required>
                </div>
                <input type="hidden" id="name_city_company"
                             name="name_city_company" class="hidden_field">
                <input type="hidden" id="id_city_company_kladr"
                       name="id_city_company_kladr" class="hidden_field">
                <input type="hidden" id="zip_city_company"
                       name="zip_city_company" class="hidden_field">

                <input type="checkbox" checked id="accept_rules" name="accept_rules" required>
                Я принимаю <a onclick="window.open('https://barter-business.ru/Polzovatelskoe_soglashenie.pdf');"
                              href="#">Пользовательское Соглашение</a>

                <div class="clear"></div>
            </div>
            <button type="submit"
                    id="btn_register"
                    class="button button-m button-full bg-highlight button-margins button-round-large shadow-huge">
                Зарегистрироваться
            </button>
        </form>
    </div>

    <div id="action-forgot-1" class="menu-box menu-box-detached round-medium menu-ready" data-menu-type="menu-box-bottom"
         data-menu-height="370" data-menu-effect="menu-parallax"
         style="height: 300px; width: auto; transform: translateY(100%);">

        <div class="page-title has-subtitle">
            <div class="page-title-left">
                <a href="#">Смена пароля</a>
            </div>
            <div class="page-title-right">
                <a href="#" id="close_password_recover" class="close-menu"><i class="fa fa-times-circle font-20 color-red2-dark"></i></a>
            </div>
        </div>

        <div id="error_generate_code" class="error txt_center"></div>

<!--        Step 1-->
        <form action="<?php echo site_url('public_ajax/recover_password/'); ?>"
              id="recover_password_step_one" onsubmit="recover_password_m('company', 'step_one'); return false">

            <div class="content">
                <div class="input_home_public bottom-25">
                    <input type="text" placeholder="Телефон" name="recover_password_phone" id="recover_password_phone" class="need_clear phone_number">
                </div>
                <div id="error_recover_password_phone" class="error"></div>
                <div class="clear"></div>
            </div>

            <button style="width: calc(100% - 30px)" type="submit" id="btn_recover_password"
                    class="button button-m button-full bg-highlight button-margins button-round-large shadow-huge btn_recover">Выслать код
            </button>

            <p class="content">Для восстановления пароля используется смс, туда придет код
                проверки! </p>
            <p class="content">Если есть проблемы со входом позвоните по номеру <a href="tel:+79272365529">+7(927)236-55-29</a></p>

        </form>

<!--        Step 2-->
        <form style="display:none;"
              action="<?php echo site_url('public_ajax/recover_password/confirm_activation_code'); ?>"
              id="recover_password_step_two" onsubmit="recover_password_m('company', 'step_two'); return false">

            <div class="content">
                <label for="activation_code">Введите код, который пришел вам по смс
                    <small>(код придет вам в течении 5 минут)</small>
                </label>
                <div class="input_home_public bottom-25">
                    <input type="text" placeholder="Код из смс" name="activation_code" id="activation_code" class="need_clear">
                </div>
                <div id="error_activation_code" class="error"></div>
                <div class="clear"></div>
            </div>
<!--            <a href="#" onclick="recover_to_start()">Начать заново</a>-->

            <button type="submit" style="width: calc(100% - 30px)"
                    class="button button-m button-full bg-highlight button-margins button-round-large shadow-huge btn_recover">Отправить</button>

        </form>

<!--        Step 3-->
        <form style="display:none;"
              action="<?php echo site_url('public_ajax/recover_password/update_password'); ?>"
              id="recover_password_step_three"
              onsubmit="recover_password_m('company', 'step_three'); return false">

            <div class="content">
                <label for="update_password">Введите ваш новый пароль для входа в личный кабинет</label>
                <div class="input_home_public bottom-25">
                    <input type="text" placeholder="Новый пароль" name="update_password" id="update_password" class="need_clear">
                </div>
                <input type="hidden" name="user_pass_phone" id="user_pass_phone">
                <div id="error_update_password" class="error"></div>

                <div class="clear"></div>
            </div>

            <button type="submit" class="button button-m button-full bg-highlight button-margins button-round-large shadow-huge btn_recover">Сохранить пароль</button>
        </form>
    </div>
</div>

<script type="text/javascript" src="<?php echo site_url('assets/js/mobile/plugins.js'); ?>"></script>
<script type="text/javascript" src="<?php echo site_url('assets/js/mobile/mobile.js'); ?>"></script>
<script type="text/javascript" src="<?php echo site_url('assets/js/all_area/all_area_scripts.js'); ?>"></script>

<!--DaData api-->
<link href="https://cdn.jsdelivr.net/npm/suggestions-jquery@19.8.0/dist/css/suggestions.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/suggestions-jquery@19.8.0/dist/js/jquery.suggestions.min.js"></script>

<!--Vex dialog-->
<script src="<?php echo site_url('assets/js/all_area/vex.combined.js'); ?>?v1"></script>
<link href="<?php echo site_url('assets/styles/all_area/vex.css'); ?>" rel="stylesheet" type="text/css"/>
<link href="<?php echo site_url('assets/styles/all_area/vex-theme-default.css'); ?>" rel="stylesheet" type="text/css"/>
<script>vex.defaultOptions.className = 'vex-theme-default'</script>

<!--<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.inputmask/3.3.4/inputmask/inputmask.min.js"-->
<!--        integrity="sha256-JUbLuiRKiaXfbMpDSL9JwAJugW+Hg2E07+fjOCFjSSA=" crossorigin="anonymous"></script>-->
<script type="text/javascript" src="<?php echo site_url('assets/js/mobile/jquery.inputmask.bundle.min.js'); ?>"></script>

<script>
    $(document).ready(function () {

        Inputmask({
            mask: '7(999)999-9999',
            oncomplete: function () {
                var val = $(this).val().replace(/\D/g, '');
                val = val.split('');
                var nums = [9, 3];
                var dis_btn = false;
                if (!nums.includes(+val[1])) {
                    vex.dialog.alert('Ошибка ввода! Введен неправильный номер! (должен начинаться с +7(9**) или +7(3**)');
                    $(this).val('');
                    dis_btn = true;
                }

                if ($(this).attr('id') == 'company_phone') {
                    $('#btn_register').prop('disabled', dis_btn);
                }
            },
        }).mask($('#company_login_phone, #company_phone, #recover_password_phone, .phone_mask'));
        //$('#company_adress').suggestions({
        //    token: "<?//= DADATA_API ?>//",
        //    type: 'ADDRESS',
        //    count: 2,
        //});

        $("#company_city").suggestions({
            token: "<?= DADATA_API ?>",
            type: "ADDRESS",
            /* Вызывается, когда пользователь выбирает одну из подсказок */
            count: 5,
            onSelect: function(obj) {
                $('#name_city_company').attr('value', obj.data.city.trim());
                $('#id_city_company_kladr').attr('value', obj.data.city_kladr_id);
                $('#zip_city_company').attr('value', obj.data.postal_code);
            },
        });
    });
    
    function register() {

        if(!document.getElementById("accept_rules").checked) {
            vex.dialog.alert('Примите условия лицензионного соглашения!');
            return false;
        }

        $('#btn_register').prop('disabled', true);
        $('#btn_register').text('Подождите...');

        let data = new FormData();
        let hasRef = <?= $this->session->has_userdata('referal') ? $this->session->has_userdata('referal') : 'false' ?>;
        if (hasRef) {
            data.append('who_invite', "<?= $this->session->userdata('referal') ?>");
        }
        data.append('company_name', $('#company_name').val());
        data.append('company_phone', $('#company_phone').val().replace(/\D/g, ''));
        var fields = $('.hidden_field');
        for (var i = 0; i < fields.length; i++) {
            data.append($(fields[i]).attr('id'), $(fields[i]).val());
        }
        return $.ajax({
                url: '/public_ajax/registr_and_login/regstr',
                type: 'POST',
                data: data,
                processData: false,
                contentType: false,
                cache: false,
                async: false,
                success:
                    function(data) {
                        //валидируем данные
                        let result = validate_data_server_response(data);
                        insert_csrf_hash(data);
                        if (result) {
                            vex.dialog.alert(data.text_message);

                            setTimeout(function() {
                                window.location.replace("<?= base_url() . 'company/profile' ?>");
                            }, 1000);

                        } else {
                            if (data.text_message !== undefined)
                                vex.dialog.alert(data.text_message);
                            else
                                vex.dialog.alert('Ошибка регистрации!');
                            $('#btn_register').prop('disabled', false);
                            $('#btn_register').text('Регистрация');
                        }
                    },
            },
        );
    }

    $('.phone_number').blur(function () {

        var phone = $(this).val().replace(/\D/g, '');
        if (phone.length != 11) {
            vex.dialog.alert("Внимание! Некорректный номер телефона! (Длина должна составлять 11 цифр)");
            if ($(this).attr('id') == 'company_phone') {
                $('#btn_register').prop('disabled', true);
            }
        }

    });

    $('#company_phone').blur(function() {

        var login = $('#company_phone').val().replace(/\D/g, '');
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
            beforeSend: function () {
                $('#login_loader').removeClass('loader-inactive');
            },
            error: function () {
                $('#login_loader').addClass('loader-inactive');
                vex.dialog.alert('Ошибка выполнения запроса!');
                setTimeout(function() {
                    reload_page();
                }, 1000);
            },
            success: function(data) {

                insert_csrf_hash(data);//функция берется из all_area_scripts!
                //валидируем данные
                var result = validate_data_server_response(data);

                if (result) {
                    vex.dialog.alert("Успешно! Сейчас Вы будете перенаправлены в личный кабинет!");
                    setTimeout(function() {
                        window.location.replace("<?= base_url() . 'company/cabinet' ?>");
                    }, 1000);
                }
                else {
                    if (data.text_message !== undefined)
                        vex.dialog.alert(data.text_message);
                    else
                        vex.dialog.alert('Ошибка входа!');

                    $('#login_loader').addClass('loader-inactive');
                }
            },
        });
    });

    var code_sent = false;
    function recover_password_m(type_user, step) {

        if (code_sent) return;
        code_sent = true;

        $('.btn_recover').prop('disabled', true);

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

            send_data.user_pass_phone = $('#recover_password_phone').val().replace(/\D/g, '');

        }

        $.ajax({
            url: $('#' + send_form).attr('action'),
            type: 'POST',
            dataType: 'JSON',
            data: send_data,
            success: function(data) {

                insert_csrf_hash(data);
                //валидируем данные
                var result = validate_data_server_response(data);

                if (result) {
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
                        document.getElementById("close_password_recover").click();
                        recover_to_start();
                        vex.dialog.alert("Пароль успешно изменен! Теперь Вы можете использовать его для входа!");
                        document.getElementById("btn_login").click();
                    }

                } else {
                    if (data.text_message !== undefined)
                        vex.dialog.alert(data.text_message);
                }

                $('.btn_recover').prop('disabled', false);
                code_sent = false;
            },
        });
    }

    function recover_to_start() {

        $('.need_clear').val('');

        $('#recover_password_step_two').hide('fast');
        $('#recover_password_step_three').hide('fast');

        $('#recover_password_step_one').show('fast');
    }

</script>

<input type="hidden" id="token_form" class="hidden_field token_form"
       name="<?php echo $this->security->get_csrf_token_name(); ?>"
       value="<?php echo $this->security->get_csrf_hash(); ?>"/>
</body>

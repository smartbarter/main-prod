<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title><?php echo $title ?></title>

    <link rel="shortcut icon" href="<?php echo site_url('favicon.ico'); ?>"
          type="image/x-icon">

    <link href="https://fonts.googleapis.com/icon?family=Material+Icons"
          rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Open+Sans"
          rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons"
          rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet"
          href="<?php echo site_url('assets/styles/public/registration.css'); ?>?v7">
    <link href="https://cdn.jsdelivr.net/npm/suggestions-jquery@18.11.1/dist/css/suggestions.min.css"
          type="text/css" rel="stylesheet"/>
    <script>
      var base_url = '<?php echo base_url(); ?>';
    </script>

</head>
<body>

<div class="wrapper" id="app">
    <form action="">
        <div id="wizard">
            <!-- Слайд 1 -->
            <h4></h4>
            <section>
                <div class="form-header">
                    <div class="avartar">
                        <a href="#">
                            <img src="<?php echo site_url('assets/images/all_area/loading.png'); ?>"
                                 alt="">
                        </a>
                        <div class="avartar-picker">
                            <input type="file" name="file-1[]" id="file-1"
                                   class="inputfile"
                                   data-multiple-caption="{count} files selected"
                                   multiple/>
                            <label for="file-1">
                                <i class="zmdi zmdi-camera"></i>
                                <span>Выбрать фото</span>
                            </label>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="form-holder active">
                            <input type="text" id="company_name"
                                   placeholder="Название компании"
                                   class="form-control required">
                        </div>
                        <div class="form-holder">
                            <input type="text" id="company_city"
                                   data-kladr-type="city" autocomplete="off"
                                   placeholder="Город" class="form-control required">
                            <div id="kladr_autocomplete"></div>
                        </div>
                        <div class="form-holder">
                            <input type="text" id="company_phone"
                                   placeholder="Телефон" class="form-control">
                            <div id="error_company_phone" class="error"></div>
                        </div>
                    </div>
                    <input type="hidden" id="name_city_company"
                           name="name_city_company" class="hidden_field">
                    <input type="hidden" id="id_city_company_kladr"
                           name="id_city_company_kladr" class="hidden_field">
                    <input type="hidden" id="zip_city_company"
                           name="zip_city_company" class="hidden_field">
                </div>
                <input type="checkbox" checked id="accept_rules" name="accept_rules" required>
                Я принимаю <a href="https://barter-business.ru/Polzovatelskoe_soglashenie.pdf">Пользовательское
                    Соглашение</a>
            </section>

            <!-- Слайд 2 -->
            <h4></h4>
            <section>
                <div class="form-group">
                    <div class="form-holder active">
                        <input type="text" id="contact_name" placeholder="Контактное лицо"
                               class="form-control company__post">
                    </div>
                    <div class="form-holder">
                        <input type="text" id="company_address" placeholder="Адрес"
                               class="form-control company__post">
                    </div>
                    <div class="form-holder">
                        <input type="text" id="company_limit" placeholder="Лимит"
                               class="form-control company__post">
                        <p style="color: #181824;">Сумма бартерных единиц
                            которую вы хотите зарабатывать в месяц</p>
                    </div>
                </div>
            </section>

            <!-- Слайд 3 -->
            <h4></h4>
            <section>
                <div class="form-group">

                    <div class="form-holder active">

                        <input type="text"
                               id="company_hobby"
                               placeholder="Деятельность вашей компании?"
                               class="form-control company__post">
                    </div>
                    <div class="form-holder">
                        <input type="text"
                               id="company_prices"
                               placeholder="Цены на ваши товары или услуги?"
                               class="form-control company__post">
                    </div>
                    <div class="form-holder">
                        <input type="text" placeholder="График работы"
                               id="company_hours"
                               class="form-control company__post">
                    </div>
                </div>
            </section>
            <h4></h4>
            <section>
                <div class="form-group">
                    <div class="form-holder active">
                        <input type="text" placeholder="Сколько лет на рынке?"
                               id="company_years"
                               class="form-control company__post">
                    </div>
                    <div class="form-holder">
                        <input type="text"
                               id="company_time"
                               placeholder="Время выполнения заказа?"
                               class="form-control company__post">
                    </div>
                    <div class="form-holder">
                        <input type="text"
                               id="company_order_type"
                               placeholder="По записи или живая очередь?"
                               class="form-control company__post">
                    </div>
                </div>
            </section>
        </div>
    </form>
</div>

<input type="hidden" id="token_form" class="hidden_field token_form"
       name="<?php echo $this->security->get_csrf_token_name(); ?>"
       value="<?php echo $this->security->get_csrf_hash(); ?>"/>
<script src="https://cdn.jsdelivr.net/npm/vue@2.5.22/dist/vue.js"></script>
<script src="<?php echo site_url('assets/js/public/jquery.min.js'); ?>"></script>
<script src="<?php echo site_url('assets/js/public/jquery.steps.js'); ?>"></script>
<script src="<?php echo site_url('assets/js/all_area/phone_digit.js'); ?>"></script>
<script src="<?php echo site_url('assets/js/all_area/all_area_scripts.js'); ?>"></script>
<script src="<?php echo site_url('assets/js/public/home_page.js'); ?>"></script>
<script src="<?php echo site_url('assets/js/all_area/jquery.kladr.min.js') ?>"></script>
<script src="<?php echo site_url('assets/js/public/registration.js'); ?>"></script>
<script type="text/javascript"
        src="https://cdnjs.cloudflare.com/ajax/libs/jquery-ajaxtransport-xdomainrequest/1.0.1/jquery.xdomainrequest.min.js"></script>
<script type="text/javascript"
        src="https://cdn.jsdelivr.net/npm/suggestions-jquery@18.11.1/dist/js/jquery.suggestions.min.js"></script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.inputmask/3.3.4/inputmask/inputmask.min.js"
        integrity="sha256-JUbLuiRKiaXfbMpDSL9JwAJugW+Hg2E07+fjOCFjSSA="
        crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@7.28.12/dist/sweetalert2.all.min.js"
        integrity="sha256-/4J7peVdmIQYZ+PiYc9Ae2I850oFTh5vf6dS+Du3OpQ="
        crossorigin="anonymous"></script>
<script>

  new Vue({
    el: '#app',
  });

  jQuery(document).ready(function($) {
    $('#company_city').suggestions({
      token: "<?= DADATA_API ?>",
      type: 'ADDRESS',
      count: 5,
      onSelect: function(obj) {
        $('#name_city_company').attr('value', obj.data.city);
        $('#id_city_company_kladr').attr('value', obj.data.city_kladr_id);
        $('#zip_city_company').attr('value', obj.data.postal_code);
      },
    });

    $('#company_address').suggestions({
      token: "<?= DADATA_API ?>",
      type: 'ADDRESS',
      count: 3,
    });
  });
</script>
</body>
</html>
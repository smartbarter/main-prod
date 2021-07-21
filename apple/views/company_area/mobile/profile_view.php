<div class="page-content header-clear-large">

    <div>
        <div class="content">
            <h4>Лимит</h4>
        </div>
        <div class="content-box shadow-small">
            <form id="sverh_limit" class="update_description"
                  action="<?php echo site_url('company/company_ajax/ajax/update_profile_m'); ?>">
                <div class="input-style input-style-2 input-required">
                    <div class="bottom-10">
                        <label>Сверх лимита (% бартерный)</label>
                        <select class="form-control-lg" name="sverh_limit" id="sverh_limit_data" value="">
                            <?php for ($i = 30; $i <= 100; $i += 10): ?>
                                <option <?= $i == $company_data['sverh_limit'] ? 'selected' : ''?>><?= $i ?></option>
                            <?php endfor; ?>
                        </select>
                        <div id="error_sverh_limit" class="error"></div>
                    </div>

<!--                    <div class="bottom-10">-->
<!--                        <label for="month_limit">Лимит</label>-->
<!--                        <input id="month_limit " name="month_limit" type="number"-->
<!--                               class=" " min="5000"-->
<!--                               value="--><?php //echo $company_data['month_limit'] / 100; ?><!--"-->
<!--                               placeholder="Введите сумму" required>-->
<!--                        <div id="error_month_limit" class="error"></div>-->
<!--                    </div>-->
                </div>

                <input type="hidden" name="form_type" value="limit">

                <button style="width: 100%;" class="button button-s round-small shadow-large bg-highlight button-full ">
                    Изменить данные
                </button>
            </form>
        </div>
    </div>

    <div class="divider divider-margins"></div>
    <div>
        <div class="content">
            <h4>Логотип вашей компании</h4>
        </div>
        <div class="content-box shadow-small">
            <div class="text-center">
                <img src="<?php echo site_url('uploads/companys_logo/' . $company_data['logo']); ?>"
                     height="150" width="150" class="img-circle img_border_shadow">
            </div>
            <hr>
            <h5>Загрузить новый логотип?</h5>
            <form id="update_logo"
                  action="<?php echo site_url('company/company_ajax/ajax/update_logo'); ?>"
                  enctype="multipart/form-data">
                <div class="form-group bottom-20 ">
                    <label for="avatar_company">Логотип
                        компании или ваше фото (max. 2MB)</label>
                    <input type="file" accept="image/*" name="avatar_company" id="avatar_company" required>
                    <div id="error_avatar_company" class="error"></div>
                </div>
                <button style="width: 100%;" class="button button-s round-small shadow-large bg-highlight button-full ">
                    Загрузить новый логотип
                </button>
            </form>
        </div>
    </div>
    <div class="divider divider-margins"></div>

    <div class="divider divider-margins"></div>
    <div>
        <div class="content">
            <h4>Данные компании</h4>
        </div>
        <div class="content-box shadow-small">


            <form class="update_description"
                  action="<?= site_url('/company/company_ajax/ajax/update_profile_m') ?>">

                <label>Название компании</label>
                <div class="input_home_public bottom-10">
                    <input type="text" name="company_name" id="company_name"
                           value="<?php echo html_escape($company_data['company_name']); ?>">
                </div>
                <div id="error_company_name" class="error"></div>

                <label>Контактное лицо</label>
                <div class="input_home_public bottom-10">
                    <input type="text" name="contact_name" id="contact_name"
                           value="<?php echo html_escape($company_data['contact_name']); ?>">
                </div>
                <div id="error_contact_name" class="error"></div>

                <label>Контактный телефон</label>
                <div class="input_home_public bottom-10">
                    <input type="text" name="contact_phone"
                           id="contact_phone" maxlength="11"
                           value="<?php echo html_escape($company_data['company_phone']); ?>">
                </div>
                <div id="error_contact_phone" class="error"></div>

                <label>Адрес компании</label>
                <div class="input_home_public bottom-10">
                    <input type="text" placeholder="Город" name="company_adress" id="company_adress" value="<?php echo html_escape($company_data['adress']); ?>">
                </div>
                <div id="error_company_adress" class="error"></div>

                <label>Город</label>
                <div class="input_home_public bottom-10">
                    <input type="text" placeholder="Город" name="city_name" id="city_name" value="<?php echo html_escape($company_data['city_name']); ?>">
                </div>
                <div id="error_city_name" class="error"></div>
                <input type="hidden" name="form_type" value="comp_data">

                <button style="width: 100%;" id="btn_change_data_company"
                        class="button button-s round-small shadow-large bg-highlight button-full ">
                    Изменить данные
                </button>
            </form>
            <form class="update_description"
                  action="<?= site_url('/company/company_ajax/ajax/update_about') ?>">
                <div class="input-style input-style-2 input-required">
                    <div class="form-group">
                        <label for="about_company">Описание</label>
                        <textarea style="margin-bottom: 10px; height: 120px; line-height: 25px; padding-top: 10px;"
                                  name="about_company" id="about_company" class="form-control"
                                  rows="3"><?php echo html_escape($company_data['description_company']); ?></textarea>
                    </div>
                </div>

                <?php if ($can_change_description): ?>
                    <button style="width: 100%"
                            class="button button-s round-small shadow-large bg-highlight button-full"
                            type="submit">Отправить заявку
                    </button>
                <?php else: ?>
                    <button style="width: 100%" disabled
                            class="button button-s round-small shadow-large bg-highlight button-full"
                            type="submit">Ваша заявка отправлена
                    </button>
                <?php endif; ?>
            </form>
        </div>
    </div>
    <div class="divider divider-margins"></div>
    <div>
        <div class="content">
            <h4>Изменить пароль</h4>
        </div>
        <div class="content-box shadow-small">
            <div class=" input-required">
                <form class="update_description"
                      action="<?= site_url('/company/company_ajax/ajax/update_profile_m') ?>">
                    <label for="">Пароль</label>
                    <input class="input_home_public" type="password" id="password1" name="password1" required>
                    <div class="bottom-10"></div>
                    <div id="error_password1" class="error"></div>

                    <label for="">Повторите пароль</label>
                    <input class="input_home_public" type="password" id="password2" name="password2" required>
                    <div class="bottom-10"></div>
                    <div id="error_password2" class="error"></div>

                    <input type="hidden" name="form_type" value="password">

                    <button style="width: 100%;"
                            class="button button-s round-small shadow-large bg-highlight button-full ">
                        Сохранить
                    </button>
                </form>
            </div>
        </div>
        <div class="divider divider-margins"></div>
        <div>
            <div class="content">
                <h4>Социальные сети</h4>
            </div>
            <div class="content-box shadow-small">
                <div class=" input-required">
                    <form class="update_description"
                          action="<?= site_url('/company/company_ajax/ajax/update_profile_m') ?>">
                        <div class="bottom-10">
                            <label for="social_vk">Ссылка Вконтакте</label>
                            <input class="input_home_public" type="text" name="social_vk" class="input_grey" id="social_vk"
                                   value="<?php echo html_escape($company_data['social_vk']); ?>">
                        </div>

                        <div class="bottom-10">
                            <label for="social_inst">Ссылка Инстаграм</label>
                            <input class="input_home_public" type="text" name="social_inst" class="input_grey" id="social_inst"
                                   value="<?php echo html_escape($company_data['social_inst']); ?>">
                        </div>

                        <div class="bottom-10">
                            <label for="company_site">Сайт компании</label>
                            <input class="input_home_public" type="text" name="company_site" class="input_grey" id="company_site"
                                   value="<?php echo html_escape($company_data['company_site']); ?>">
                        </div>

                        <input type="hidden" name="form_type" value="social">

                        <button style="width: 100%;"
                                class="button button-s round-small shadow-large bg-highlight button-full ">
                            Изменить данные
                        </button>
                    </form>
                </div>
            </div>
        </div>
        <div class="divider divider-margins"></div>
        <div>
            <div class="content">
                <h4>Оповещения</h4>
            </div>
            <div class="content-box shadow-small">
                <div class=" input-required">
                    <form class="update_notifications"
                          action="<?= site_url('/company/company_ajax/ajax/update_notification') ?>">
                        <a href="#" class="toggle-switch toggle-ios <?= $company_data['whatsapp_notif'] ? 'toggle-on' : 'toggle-off'?>"
                           style="margin-bottom: 20px"
                           data-toggle-height="30"
                           data-toggle-width="55"
                           data-toggle-content="toggle-content-whatsapp-notif"
                           data-toggle-checkbox="whatsapp_notif"
                           data-icons-size="9"
                           data-bg-on="bg-green1-dark"
                           data-bg-off="bg-red2-dark">
                            <span class="color-theme bolder font-14">Уведомления в WhatsApp</span>
                            <strong></strong>
                            <i class="fa-t1 fa fa-check"></i>
                            <i class="fa-t2 fa fa-times"></i>
                            <u></u>
                        </a>
                        <input type="checkbox" style="display: none;" id="whatsapp_notif">
                        <input type="checkbox" style="display: none;" id="whatsapp_ap">
                        <input type="checkbox" style="display: none;" id="whatsapp_deal">
                        <div class="toggle-content" id="toggle-content-whatsapp-notif">
                            <div class="content">
                                <a href="#" class="toggle-switch toggle-ios <?= $company_data['whatsapp_ap'] ? 'toggle-on' : 'toggle-off'?>"
                                   style="margin-bottom: 20px"
                                   data-toggle-height="30"
                                   data-toggle-width="55"
                                   data-toggle-checkbox="whatsapp_ap"
                                   data-icons-size="9"
                                   data-bg-on="bg-green1-dark"
                                   data-bg-off="bg-red2-dark">
                                    <span class="color-theme bolder font-14">Об окончании подписки</span>
                                    <strong></strong>
                                    <i class="fa-t1 fa fa-check"></i>
                                    <i class="fa-t2 fa fa-times"></i>
                                    <u></u>
                                </a>
                                <a href="#" class="toggle-switch toggle-ios <?= $company_data['whatsapp_deal'] ? 'toggle-on' : 'toggle-off'?>"
                                   style="margin-bottom: 20px"
                                   data-toggle-height="30"
                                   data-toggle-width="55"
                                   data-toggle-checkbox="whatsapp_deal"
                                   data-icons-size="9"
                                   data-bg-on="bg-green1-dark"
                                   data-bg-off="bg-red2-dark">
                                    <span class="color-theme bolder font-14">Об операциях со сделками</span>
                                    <strong></strong>
                                    <i class="fa-t1 fa fa-check"></i>
                                    <i class="fa-t2 fa fa-times"></i>
                                    <u></u>
                                </a>
                            </div>
                        </div>
                        <button style="width: 100%;"
                                class="button button-s round-small shadow-large bg-highlight button-full ">
                            Сохранить
                        </button>
                    </form>
                </div>
            </div>
        </div>
        <div class="divider divider-margins"></div>
        <div class="content-box shadow-small">
            <div class=" input-required">
                <div class="bottom-10">
                    <label id="close_ses_success" class="color-green2-dark text-center" style="display: none;">Вы вышли со всех устройств, кроме этого!</label>
                </div>
                <button style="width: 100%;"
                        class="button button-s round-small shadow-large bg-highlight button-full bg-gradient-red2 font-13"
                        onclick="close_other_sessions(this)">
                    Выйти со всех устройств
                </button>

            </div>
        </div>
<!--        <div>-->
<!--            <div class="content">-->
<!--                <h4>Дополнительные платные опции</h4>-->
<!--            </div>-->
<!--            <div class="content-box shadow-small">-->
<!--                <div class="form-group">-->
<!--                    <form class="update_description"-->
<!--                          action="--><?//= site_url('/company/profile/advert') ?><!--">-->
<!--                        <input type="hidden" name="company_id"-->
<!--                               value="--><?//= $company_data['company_id'] ?><!--">-->
<!--                        <input type="hidden" name="from_advert"-->
<!--                               value="Платная реклама">-->
<!--                        <input type="hidden" name="confirm"-->
<!--                               value="Вы точно хотите прорекламировать компанию? (500бр)">-->
<!---->
<!--                        <button style="width: 100%" type="submit"-->
<!--                                class="button button-s round-small shadow-large bg-highlight button-full">-->
<!--                            Прорекламировать компанию - 500бр-->
<!--                        </button>-->
<!--                    </form>-->
<!--                    <div class="divider divider-margins"></div>-->
<!--                </div>-->
<!--            </div>-->
<!---->
<!---->
<!--        </div>-->
    </div>
</div>

<link href="https://cdn.jsdelivr.net/npm/suggestions-jquery@19.7.1/dist/css/suggestions.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/suggestions-jquery@19.7.1/dist/js/jquery.suggestions.min.js"></script>
<script type="text/javascript" src="<?php echo site_url('assets/js/mobile/jquery.inputmask.bundle.min.js'); ?>"></script>

<script>
    Inputmask({
        mask: '79999999999',
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
    }).mask($('#contact_phone'));

    $(document).ready(function () {
        $('#company_adress').suggestions({
            token: "<?= DADATA_API ?>",
            type: 'ADDRESS',
            scrollOnFocus: false,
            count: 5,
            onSelect: function(suggestion) {
                console.log(suggestion);
            }
        });

        $('#city_name').suggestions({
            token: "<?= DADATA_API ?>",
            type: 'ADDRESS',
            bounds: "city",
            scrollOnFocus: false,
            count: 3,
            onSelect: function(obj) {
                $('#name_city_company').attr('value', obj.data.city.trim());
                $('#id_city_company_kladr').attr('value', obj.data.city_kladr_id);
            },

        });
    });
</script>

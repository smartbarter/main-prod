<div class="page-content header-clear-large">


    <div class="bottom-0">
        <div class="content-box  bottom-10">
        <div class="content-title ">
            <h5>Выбор города</h5>
        </div>
        <div class="input-style input-style-2 input-required">
            <?php if (!empty($cities)): ?>
                <div class="control-group">
                    <select id="city_selector">
                        <?php foreach ($cities as $city):
                            $selected = '';
                            if ($city['city_kladr_id'] === $default_city['for_search']) {
                                $selected = 'selected';
                            } ?>
                            <option value="<?= $city['city_kladr_id'] ?>" <?= $selected ?>><?= $city['city_name'] ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            <?php endif; ?>
        </div>
        </div>

        <div class="content-box ">
        <div class="link-list link-list-2 link-list-long-border link-list-icon-bg">
            <a href="<?php echo site_url('company/news'); ?>">
                <i class="fas fa-newspaper bg-magenta2-dark round-circle shadow-huge"></i>
                <span>Новости</span>
                <strong>Последние новости</strong>
                <i class="fa fa-angle-right"></i>
            </a>

            <a href="<?= site_url('company/orders/inbox') ?>">
                <i class="fas fa-money-bill-alt bg-yellow1-dark round-circle shadow-huge"></i>
                <span>Сделки</span>
                <strong>Совершенные сделки</strong>
                <i class="fa fa-angle-right"></i>
            </a>
            <a href="<?php echo site_url('company/fave'); ?>">
                <i class="fa fa-heart bg-red2-dark round-circle shadow-huge"></i>
                <span>Избранное</span>
                <strong>Компании добавленые в избранное</strong>
                <i class="fa fa-angle-right"></i>
            </a>


            <a href="<?php echo site_url('company/product'); ?>">
                <i class="fas fa-shopping-bag bg-magenta1-dark round-circle shadow-huge"></i>
                <span>Товары</span>
                <strong>Ваши товары</strong>
                <i class="fa fa-angle-right"></i>
            </a>
            <a href="<?php echo site_url('company/coupon'); ?>">
                <i class="fas fa-money-check-alt bg-green1-dark round-circle shadow-huge"></i>
                <span>Купоны</span>
                <strong>Ваши купоны</strong>
                <i class="fa fa-angle-right"></i>
            </a>
            <a href="<?php echo site_url('company/statistics'); ?>">
                <i class="fa fa-chart-pie bg-gray2-dark round-huge shadow-huge"></i>
                <span>Статистика</span>
                <strong>Статистика аккаунта</strong>
                <i class="fa fa-angle-right"></i>
            </a>
            <a href="<?php echo site_url('company/prize'); ?>">
                <i class="fas fa-money-check-alt bg-red1-dark round-circle shadow-huge"></i>
                <span>Лидеры</span>
                <strong>Лидеры месяца</strong>
                <i class="fa fa-angle-right"></i>
            </a>
            <a href="<?php echo site_url('company/abon_plata'); ?>">
                <i class="fas fa-ruble-sign bg-blue1-dark round-circle shadow-huge"></i>
                <span>Абонентская плата</span>
                <strong>Статус абонентской платы</strong>
                <i class="fa fa-angle-right"></i>
            </a>
            <a href="<?php echo site_url('company/referrals'); ?>">
                <i class="fas fa-users bg-magenta1-plum round-circle shadow-huge"></i>
                <span>Реферальная программа</span>
                <strong>Ваши приглашенные компании</strong>
                <i class="fa fa-angle-right"></i>
            </a>

            <a href="<?php echo site_url('company/profile'); ?>">
                <i class="fa fa-cog bg-blue2-dark round-circle shadow-huge"></i>
                <span>Настройки</span>
                <strong>Настройки компании</strong>
                <i class="fa fa-angle-right"></i>
            </a>
            <a href="<?php echo site_url('support'); ?>">
                <i class="fas fa-life-ring bg-green1-dark round-circle shadow-huge"></i>
                <span>Помощь</span>
                <strong>Правила система</strong>
                <i class="fa fa-angle-right"></i>
            </a>
            <a href="<?php echo site_url('publics/logout'); ?>">
                <i class="fas fa-sign-out-alt bg-red1-dark round-circle shadow-huge"></i>
                <span>Выход</span>
                <strong>Выход из системы</strong>
                <i class="fa fa-angle-right"></i>
            </a>
        </div>
    </div>
    </div>
    <div class="divider-margins bottom-50"></div>
<div class="content-box " style="padding: 10px; text-align: center; overflow: initial ">
    <img src="<?= base_url().'/assets/images/img/parner2.png' ?>" alt="" width="100%" style="margin-top: -60px">
    <h1>Приглашаем к сотрудничеству</h1>
    <span>Мы работаем в конкурентной среде. Успех нашего бизнеса зависит от достижений сотрудников. Мы хотим, чтобы вы преуспели вместе с нами, и предоставим вам все возможности для обучения, развития, профессионального и карьерного роста.</span>
    <a class="button button-s round-small button-full  color-highlight bg-transparent bottom-0" href="<?php echo site_url('company/parner'); ?>">Присоединиться</a>
</div>
    <div class="divider divider-margins"></div>
</div>


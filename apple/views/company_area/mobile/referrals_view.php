<div class="page-content header-clear-large">
    <div class="content-box  shadow-small" style="padding: 10px">
        <h4 class="text-center">Реферальная ссылка</h4>
        <a class="font-14 text-center"
           data-snack-id="snack-ref1"
           data-snack-color="bg-highlight color-white bg-gradient-green1"
           data-snack-text="Реферальная ссылка скипирована в буфер обмена!"
           data-snack-icon="fa fa-check"
           onclick="toClipboard('<?= site_url('/publics/reg?ref=' . $ref_link) ?>')">
            <?= site_url('/publics/reg?ref=' . $ref_link) ?>
        </a>
    </div>
    <div class="divider divider-margins"></div>
    <?php if ($company_data['ref_mode'] == 0): ?>
        <div class="content">
            <div class="content-title  bottom-0">
                <h5>Выберите тип реферальной программы</h5>
            </div>
        </div>
        <div class="content-box shadow-small bottom-10">

            <span>Получайте бонусы за друга, 500 бартерный рублей за прилечение друга который совершил сделок более 10000 бартерных рублей и тд</span>
            <div class="divider-margins"></div>
            <button style="width: 100%;" onclick="select_ref_mode(this, 1)"
                    class="button button-s round-small shadow-large bg-highlight button-full ">
                Старая
            </button>
        </div>
        <div class="content-box shadow-small">
            <span>Текст о новой</span>
            <div class="divider-margins"></div>
            <button style="width: 100%;" onclick="select_ref_mode(this, 2)"
                    class="button button-s round-small shadow-large bg-highlight button-full ">
                Новая
            </button>
        </div>
    <?php elseif ($company_data['ref_mode'] == 1): ?>

        <?php if (!empty($referrals)): ?>
            <?php foreach ($referrals as $company): ?>

                <div style="padding: 10px" class="content round-medium shadow-small">
                    <div class="company__card">
                        <div class="company__company___img">
                            <img class="company__img"
                                 src="https://barter-business.ru/uploads/companys_logo/<?php echo $company['logo']; ?>"
                                 alt="">
                        </div>
                        <div class="company__company__title">
                                <span>
                                <?php echo mb_substr($company["company_name"],
                                    0, 65, "UTF-8"); ?>
                                </span>
                        </div>
                        <div class="company__company__desc">
                            <div class="company__datail__footer" style="text-align: center; ">

                                <?php if ($company['ref_paid'] == 0): ?>
                                    <?php if ($company['deals_sum'] / 100 > 10000): ?>
                                        <button style="width: 100%;"
                                                onclick="activeRefBalanceM(<?= $company['company_id'] ?>, 1)"
                                                class="button button-s round-small shadow-large bg-highlight button-full ">
                                            Пополнить бартерный баланс
                                        </button>
                                    <?php else: ?>
                                        <button style="width: 100%;" data-menu="action-bart-balance"
                                                class="button button-s round-small shadow-large bg-highlight button-full">
                                            Пополнить бартерный баланс
                                        </button>
                                    <?php endif; ?>

                                    <?php if (!empty($company['pid'])): ?>
                                        <button style="width: 100%;"
                                                onclick="activeRefBalanceM(<?= $company['company_id'] ?>, 2)"
                                                class="button button-s round-small button-full  color-highlight bg-transparent ">
                                            В счет абонентской платы
                                        </button>
                                    <?php else: ?>
                                        <button data-menu="action-ap" style="width: 100%;"
                                                class="button button-s round-small button-full  color-highlight bg-transparent">
                                            В счет абонентской платы
                                        </button>
                                        <span>Сумма сделок пользователя:  <?php echo $company['deals_sum'] / 100 ?></span>
                                    <?php endif; ?>
                                <?php else: ?>
                                    <span style="color: green" class="font-18">Бонус выплачен</span>
                                <?php endif; ?>

                            </div>
                        </div>
                    </div>
                </div>

            <?php endforeach; ?>
        <?php endif; ?>
    <?php elseif ($company_data['ref_mode'] == 2): ?>


    <div class="content">
        <div class="content-title  bottom-0">
            <h3>Доступно для вывода: <?= number_format(($total_sum - $total_withdrawal) / 100, 2) ?> бр.</h3>
        </div>
        <span>Получено бонусов: <?= number_format($total_withdrawal / 100, 2) ?> бр.</span><br>
        <span>Всего бонусов: <?= number_format($total_sum / 100, 2) ?> бр.</span>

    </div>
        <div class="content">
            <?php if ($company_data['sub_status'] == 1): ?>
                <?php if ($total_sum > $total_withdrawal): ?>
                    <a style="width: 100%;"
                       href="#"
                       onclick="get_ref_bonuses();"
                       class="button button-s round-small button-full  color-highlight bg-transparent">
                        Получить все накопленные бонусы
                    </a>
                <?php endif; ?>
            <?php else: ?>
                <a style="width: 100%;"
                   href="<?= site_url('/company/abon_plata/') ?>"
                   class="button button-s round-small button-full  color-highlight bg-transparent">
                    Вывод недоступен. Оплатите АП!
                </a>
            <?php endif; ?>
    </div>

        <?php if (!empty($referrals)): ?>
            <?php foreach ($referrals as $company): ?>
                <div style="padding: 10px" class="content round-medium shadow-small">
                    <div class="text-left color-magenta1-dark" style="margin-bottom: 5px;">Дата регистрации: <?= (new DateTime($company['registr_date']))->format('d.m.Y'); ?></div>
                    <div class="company__card">
                        <div class="company__company___img">
                            <img class="company__img"
                                 src="https://barter-business.ru/uploads/companys_logo/<?php echo $company['logo']; ?>"
                                 alt="">
                        </div>
                        <div class="company__company__title">
                                <span>
                                <?php echo mb_substr($company["company_name"],
                                    0, 65, "UTF-8"); ?>
                                </span>
                        </div>
                        <div class="company__company__desc" style="text-align: center; ">
                            <?php if ($company['ref_paid'] == 0): ?>
                                <?php if ($company['deals_sum'] / 100 > 10000): ?>
                                    <?php if (strtotime($company['registr_date']) > strtotime('2019-12-01') and strtotime($company['registr_date']) < strtotime('2020-01-01') ): ?>
                                    <button style="width: 100%;"
                                            onclick="activeRefBalanceM(<?= $company['company_id'] ?>, 3)"
                                            class="button button-s round-small shadow-large bg-highlight button-full ">
                                        Пополнить бартерный баланс
                                    </button>
                                    <?php else: ?>
                                        <button style="width: 100%;"
                                                onclick="activeRefBalanceM(<?= $company['company_id'] ?>, 1)"
                                                class="button button-s round-small shadow-large bg-highlight button-full ">
                                            Пополнить бартерный баланс
                                        </button>
                                    <?php endif; ?>
                                <?php else: ?>
                                    <button style="width: 100%;" data-menu="action-bart-balance"
                                            class="button button-s round-small shadow-large bg-highlight button-full">
                                        Пополнить бартерный баланс
                                    </button>
                                <?php endif; ?>

                                <?php if (!empty($company['pid'])): ?>
                                    <button style="width: 100%;"
                                            onclick="activeRefBalanceM(<?= $company['company_id'] ?>, 2)"
                                            class="button button-s round-small button-full  color-highlight bg-transparent ">
                                        В счет абонентской платы
                                    </button>
                                <?php else: ?>
                                    <button data-menu="action-ap" style="width: 100%;"
                                            class="button button-s round-small button-full  color-highlight bg-transparent">
                                        В счет абонентской платы
                                    </button>

                                <?php endif; ?>
                            <?php else: ?>
                                <span style="color: green" class="font-18">Бонус выплачен</span>
                            <?php endif; ?>
                            <div class="company__datail__footer" style="text-align: center; ">
                                <div>Бонусов от компании(1 уровень - 0.3%): <?= number_format($company['deals_sum_ref0'] / 100, 2) ?> бр.</div>
                                <div>Рефералы 2 уровня(0.2%): <?= number_format($company['deals_sum_ref1'] / 100, 2) ?> бр.</div>
                                <div>Рефералы 3 уровня(0.1%): <?= number_format($company['deals_sum_ref2'] / 100, 2) ?> бр.</div>
                                <div>Итого: <?= number_format($company['ref_sum'] / 100, 2) ?> бр.</div>
                            </div>

                        </div>
                    </div>
                </div>

            <?php endforeach; ?>
        <?php endif; ?>
    <?php endif; ?>
</div>
<div class="menu-hider"></div>
<div id="action-ap"
     class="menu-box round-medium"
     data-menu-type="menu-box-modal"
     data-menu-height="250"
     data-menu-width="300">

    <div class="page-title">
        <div class="page-title-right">
            <a href="#" class="close-menu"><i class="fa fa-times-circle font-20 color-red2-dark"></i></a>
        </div>
    </div>

    <div class="content caption-center center-text">
        <span class="font-18">Эта компания еще не оплатила абонентскую плату, бонус недоступен.</span>
        <div class="clear"></div>
    </div>
</div>
<div id="action-bart-balance"
     class="menu-box round-medium"
     data-menu-type="menu-box-modal"
     data-menu-height="250"
     data-menu-width="300">

    <div class="page-title">

        <div class="page-title-right">
            <a href="#" class="close-menu"><i class="fa fa-times-circle font-20 color-red2-dark"></i></a>
        </div>
    </div>

    <div class="content caption-center center-text">
        <span class="font-18">Оборот этой компании меньше 10 000 бартерных рублей, бонус недоступен.</span>
        <div class="clear"></div>
    </div>
</div>
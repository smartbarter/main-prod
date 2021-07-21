
<div class="kt-space-20"></div>
<div class="kt-body kt-grid__item kt-grid__item--fluid kt-grid kt-grid--hor kt-grid--stretch" id="kt_body">
    <div class="kt-content  kt-grid__item kt-grid__item--fluid kt-grid kt-grid--hor" id="kt_content">
        <div class="kt-container  kt-grid__item kt-grid__item--fluid">
            <div class="row">

                    <?php if ($company_data['ref_mode'] == 0): ?>
                        <div class="header">
                            <h2>Выберите тип реферальной программы</h2>
                        </div>
                        <button style="width: 100%;"
                                onclick="select_ref_mode(this, 1)"
                                class="btn btn-outline-default">
                            Тип 1
                        </button>
                        <button style="width: 100%;"
                                onclick="select_ref_mode(this, 2)"
                                class="btn btn-outline-default">
                            Тип 2
                        </button>
                    <?php elseif ($company_data['ref_mode'] == 1): ?>
                        <?php if (!empty($referrals)): ?>
                            <div class="body table-responsive">
                                <table class="table">
                                    <thead class="thead-light">
                                    <tr>
                                        <th>Дата</th>
                                        <th>Название компании</th>
                                        <th>Сумма</th>
                                        <th>Пополнение бартерного баланса</th>
                                        <th>В счет абонентской платы</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php foreach ($referrals as $company): ?>
                                        <tr>
                                            <td><?= $company['registr_date'] ?></td>
                                            <td>
                                                <a href="<?= site_url('company/cabinet/company_detail?company_id=' . $company['company_id']) ?>">
                                                    <?= $company['company_name'] ?>
                                                </a>
                                            </td>
                                            <td>500</td>
                                            <?php if (!$company['ref_paid']): ?>
                                                <td>
                                                    <?php if ($company['deals_sum'] / 100 > 10000): ?>

                                                        <button onclick="activeRefBalance(<?= $company['company_id'] ?>, 1)"
                                                                class="waves-effect waves-light btn btn-success btn-border-radius">
                                                            Пополнить
                                                        </button>
                                                    <?php else: ?>
                                                        <button
                                                                class="waves-effect waves-light btn btn-success btn-border-radius "
                                                                disabled="disabled">
                                                            Пополнить
                                                        </button>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <?php if (!empty($company['pid'])): ?>
                                                        <button onclick="activeRefBalance(<?= $company['company_id'] ?>, 2)"
                                                                class="waves-effect waves-light btn btn-outline-warning btn-border-radius">
                                                            В счет АП
                                                        </button>
                                                    <?php else: ?>
                                                        <button class="waves-effect waves-light btn btn-outline-warning btn-border-radius"
                                                                disabled="disabled">
                                                            В счет АП
                                                        </button>
                                                    <?php endif; ?>

                                                </td>
                                            <?php endif; ?>
                                        </tr>
                                    <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php else: ?>
                            <div class="header">
                                <h2>Не найдено приглашенных компаний</h2>
                            </div>
                        <?php endif; ?>

                    <?php elseif ($company_data['ref_mode'] == 2): ?>
                        <?php if (!empty($referrals)): ?>
                            <div class="body">
                                <h2>Всего бонусов: <?= number_format($total_sum / 100, 2) ?> бр.</h2>
                                <p>Получено бонусов: <?= number_format($total_withdrawal / 100, 2) ?> бр.</p>
                                <p>Доступно бонусов: <?= number_format(($total_sum - $total_withdrawal) / 100, 2) ?> бр.</p>
                            <?php if ($company_data['sub_status'] == 1): ?>
                                <?php if ($total_sum > $total_withdrawal): ?>
                                    <a style="width: 100%;"
                                       href="#"
                                       onclick="get_ref_bonuses()"
                                       class="btn btn-label-brand btn-bold">
                                        Получить все накопленные бонусы
                                    </a>
                                <?php endif; ?>
                            <?php else: ?>
                                <a style="width: 100%;"
                                   href="<?= site_url('/company/abon_plata/') ?>"
                                   class="btn btn-label-brand btn-bold">
                                    Вывод недоступен. Оплатите АП!
                                </a>
                            <?php endif; ?>

                            </div>
                            <div class="body table-responsive" style="margin-top: 40px;">
                                <table class="table">
                                    <thead class="thead-light">
                                    <tr>
                                        <th>Дата</th>
                                        <th>Название компании</th>
                                        <th>Пополнение бартерного баланса</th>
                                        <th>В счет абонентской платы</th>
                                        <th>Реферал 1 уровня</th>
                                        <th>Рефералы 2 уровня</th>
                                        <th>Рефералы 3 уровня</th>
                                        <th>Итого</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php foreach ($referrals as $company): ?>
                                        <tr>
                                            <td><?= $company['registr_date'] ?></td>
                                            <td>
                                                <a href="<?= site_url('company/cabinet/company_detail?company_id=' . $company['company_id']) ?>">
                                                    <?= $company['company_name'] ?>
                                                </a>
                                            </td>
                                            <?php if ($company['ref_paid'] == 0): ?>
                                            <td>
                                                <?php if ($company['deals_sum'] / 100 > 10000): ?>
                                                    <?php if (strtotime($company['registr_date']) > strtotime('2019-12-01') and strtotime($company['registr_date']) < strtotime('2019-12-31') ): ?>
                                                        <button
                                                                onclick="activeRefBalance(<?= $company['company_id'] ?>, 3)"
                                                                class="waves-effect waves-light btn btn-success btn-border-radius ">
                                                            Пополнить
                                                        </button>
                                                    <?php else: ?>
                                                        <button
                                                                onclick="activeRefBalance(<?= $company['company_id'] ?>, 1)"
                                                                class="waves-effect waves-light btn btn-success btn-border-radius ">
                                                            Пополнить
                                                        </button>
                                                    <?php endif; ?>
                                                <?php else: ?>
                                                    <span style="color:red">Не доступно</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <?php if (!empty($company['pid'])): ?>
                                                    <button style="width: 100%;"
                                                            onclick="activeRefBalance(<?= $company['company_id'] ?>, 2)"
                                                            class="waves-effect waves-light btn btn-outline-warning btn-border-radius ">
                                                        В счет абонентской платы
                                                    </button>
                                                <?php else: ?>
                                                    <span style="color: red">Не доступно</span>

                                                <?php endif; ?>
                                            </td>
                                            <?php else: ?>
                                                <td><span style="Dcolor: green" >Бонус выплачен</span></td>
                                                <td><span style="color: green" >Бонус выплачен</span></td>

                                            <?php endif; ?>
                                            <td><?= number_format($company['deals_sum_ref0'] / 100, 2) ?></td>
                                            <td><?= number_format($company['deals_sum_ref1'] / 100, 2) ?></td>
                                            <td><?= number_format($company['deals_sum_ref2'] / 100, 2) ?></td>
                                            <td><?= number_format($company['ref_sum'] / 100, 2) ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php else: ?>
                            <div class="header">
                                <h2>Не найдено приглашенных компаний</h2>
                            </div>
                        <?php endif; //Referrals ?>

                    <?php endif; //Ref mode 2?>
                </div>

                

            </div>
        </div>
    </div>



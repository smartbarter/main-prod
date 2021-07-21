<section class="content">

    <div class="block-header">

        <ul class="nav nav-tabs tab-nav-right " role="tablist" style="margin-bottom: 20px">

            <li role="presentation">
                <a href="<?= site_url('company/orders/inbox') ?>" <?= ($this->uri->segment(3) === 'inbox') ? 'class="active show"' : '' ?>
                >Входящие</a>
            </li>
            <li role="presentation">
                <a href="<?= site_url('company/orders/outbox') ?>" <?= ($this->uri->segment(3) === 'outbox') ? 'class="active show"' : '' ?>
                >Исходящие</a>
            </li>
            <li role="presentation">
                <a href="<?= site_url('company/orders/unaccepted') ?>" <?= ($this->uri->segment(3) === 'unaccepted') ? 'class="active show"' : '' ?>
                >Не принятые</a>
            </li>
        </ul>

        <?php if (! empty($deals_list)): ?>
            <?php $c = 1; ?>
            <?php foreach ($deals_list as $data => $list): ?>

                <div class="deal_data_company">
                    <p class="font-bold"><?= $data ?></p>
                    <?php foreach ($list as $deal): ?>
                        <div class="history_deal_element" data-toggle="collapse" data-target="#collapse<?php echo $c; ?>" aria-expanded="false" aria-controls="collapseExample">
                            <div class="row" style="padding: 10px">
                                <div class="col-2" style="padding-right: 0px;">
                                    <img class="img-circle"
                                         src="https://barter-business.ru/uploads/companys_logo/<?= $deal['logo']; ?>"
                                         alt="" width="35px" height="35px">
                                </div>
                                <div class="col-10">
                                    <h5><?php echo mb_substr($deal["company_name"], 0, 45, "UTF-8"); ?></h5>


                                    <?php if ($this->uri->segment(3) === 'index'): ?>
                                        <div>
                                            <span>Продавец: <span  class="font-bold"><?= $deal['s_company_name'] ?></span></span>

                                        </div>
                                    <?php endif; ?>
                                </div>
                                <div class="deal__status_info">
                                    <div class="col-6" style="margin-top: 10px">
                                        <?php if ($deal['status_deal'] == 2) { ?>
                                            <span class="text-success">Сделка совершена</span>
                                        <?php } elseif ($deal['status_deal'] == 0){?>
                                            <span class="text-danger">Сделка отменена</span>
                                        <?php } elseif ($deal['status_deal'] == 1){?>
                                            <span class="text-warning">Ожидает потверждения</span>
                                        <?php }?>

                                    </div >
                                    <div class="col-6" style="margin-top: 10px;text-align: right;">
                                        <span class="font-16 font__grad__bar">Сумма: </span><span
                                                class="font-16"><?php echo $deal['summa_sdelki'] / 100; ?></span>
                                    </div>
                                </div>

                                <div class="status_company_deal m-t-10 align-center ">

                                    <?php if ($deal['status_deal'] === '2' && $deal['want_cancel'] === '1' && $this->uri->segment(3) === 'inbox'): ?>
                                        <p>Ваш контрагент отправил заявку на отмену сделки</p>
                                        <button class="form-control"
                                                onclick="cancel_deal(<?= $deal['deal_id'] ?>)">
                                            Подтвердить отмену сделки
                                        </button>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="history-item-content collapse col-12"  id="collapse<?php echo $c; ?>">
                                <?php if ($this->uri->segment(3) === 'outbox' ) { ?>
                                    <div class="col-12">

                                        <address>
                                            <p class="font-bold">Информация платежа:</p>
                                            <p class="text-muted">
                                                Сумма платежа <?php echo $deal['summa_sdelki'] / 100; ?>
                                                <br>Комиссия <?php echo ($deal['summa_sdelki'] / 10000) * PERCENT_SYSTEM; ?>
                                                <br><p class="font-bold">Итого <?php echo (($deal['summa_sdelki'] /10000) * PERCENT_SYSTEM) + $deal['summa_sdelki'] /100; ?></p>

                                            </p>
                                        </address>

                                    </div>
                                <?php } ?>
                                <div class="col-12">

                                    <address>
                                        <?php if (!empty($deal['comment_deal'])) { ?>
                                            <p class="font-bold">Комментарий:</p>
                                            <p><?php echo $deal['comment_deal']; ?></p>
                                        <?php } ?>
                                    </address>

                                </div>
                                <div class="pull-right text-right">
                                    <address>
                                        <?php if ($deal['status_deal'] === '1' && ($this->uri->segment(3) === 'inbox' || $this->uri->segment(3) === 'unaccepted')) { ?>
                                            <span class="btn btn-success  width-per-50"
                                                  onclick="accept_or_cancel_deal(
                                                      2,<?php echo $deal['deal_id']; ?>,
                                                      '<?php echo $deal['buyer_deal_id']; ?>'
                                                      );">Принять</span>
                                            <span class="btn btn-danger  width-per-40"
                                                  onclick="accept_or_cancel_deal(
                                                      0,<?php echo $deal['deal_id']; ?>,
                                                      '<?php echo $deal['buyer_deal_id']; ?>'
                                                      );">Отказать</span>
                                        <?php } ?>
                                        <?php if ($deal['status_deal'] === '1' && ($this->uri->segment(3) === 'outbox')) { ?>

                                            <span class="btn btn-danger"
                                                  onclick="accept_or_cancel_deal(
                                                      0,<?php echo $deal['deal_id']; ?>,
                                                      '<?php echo $deal['buyer_deal_id']; ?>'
                                                      );">Отменить</span>
                                        <?php } ?>
                                        <p class="addr-font-h3 font-bold"><?php if ($deal['status_deal'] == 2) { ?>

                                            <?php if ($deal['want_cancel'] == 1): ?>
                                        <p>Ваш контрагент отправил заявку на отмену сделки</p>
                                        <button class="form-control"
                                                onclick="cancel_deal(<?= $deal['deal_id'] ?>)">
                                            Подтвердить отмену сделки
                                        </button>
                                    <?php endif; ?>
                                        <?php } ?></p>

                                        <p class="m-t-30">
                                            <b>Дата сделки :</b>
                                            <i class="fa fa-calendar"></i>
                                            <?php echo $deal['date']; ?> </p>
                                        <p> <b>Статус :</b>
                                            <?php if ($deal['status_deal'] == 2) { ?>
                                                <span class="label label-success">Успешно</span>
                                            <?php } elseif ($deal['status_deal'] == 0){?>
                                                <span class="label label-danger">Отказ</span>
                                            <?php } elseif ($deal['status_deal'] == 1){?>
                                                <span class="label label-warning">Ожидает потверждения</span>
                                            <?php } ?>

                                        </p>
                                        <a class="waves-effect waves-light btn m-t-10 btn-border-radius" href="<?php echo site_url('company/cabinet/company_detail?company_id='
                                            . $deal['company_id']); ?>">Профиль</a>
                                    </address>
                                </div>
                            </div>
                        </div>

                        <?php $c++; ?>
                    <?php endforeach; ?>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>

    </div>
    <?php echo $this->pagination->create_links(); ?>

</section>

<div class="kt-space-20"></div>
<div class="kt-body kt-grid__item kt-grid__item--fluid kt-grid kt-grid--hor kt-grid--stretch" id="kt_body">
    <div class="kt-content  kt-grid__item kt-grid__item--fluid kt-grid kt-grid--hor" id="kt_content">
        <div class="kt-container  kt-grid__item kt-grid__item--fluid">


            <ul class="nav nav-pills nav-fill" role="tablist" style="margin-bottom: 20px">

                <li class="nav-item">
                    <a style="font-size: 16px;font-weight: 500;" href="<?= site_url('company/orders/inbox') ?>" <?= ($this->uri->segment(3) === 'inbox') ? 'class="nav-link active"' : 'class="nav-link"' ?>
                    >Входящие</a>
                </li>
                <li class="nav-item">
                    <a  style="font-size: 16px;font-weight: 500;" href="<?= site_url('company/orders/outbox') ?>"  <?=  ($this->uri->segment(3) === 'outbox') ? 'class="nav-link active"' : 'class="nav-link"' ?>
                    >Исходящие</a>
                </li>
                <li class="nav-item">
                    <a style="font-size: 16px;font-weight: 500;" href="<?= site_url('company/orders/unaccepted') ?>" <?= ($this->uri->segment(3) === 'unaccepted') ? 'class="nav-link active"' : 'class="nav-link"' ?>
                    >Не принятые</a>
                </li>
            </ul>

            <?php if (! empty($deals_list)): ?>
                <?php $c = 1; ?>
                <?php foreach ($deals_list as $data => $list): ?>

                <div class="deal_data_company">
                    <p class="kt-font-boldest"><?= $data ?></p>
                    <?php foreach ($list as $deal): ?>
                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 history_deal_element" data-toggle="collapse" data-target="#collapse<?php echo $c; ?>" aria-expanded="false" aria-controls="collapseExample">
                            <div class="history_deal">
                                <div class="img_comp_deal">
                                    <img class="img-circle"
                                         src="https://barter-business.ru/uploads/companys_logo/<?= $deal['logo']; ?>"
                                         alt="" width="50px" height="50px">
                                </div>
                                <div class="name_company_deal">
                                    <h5><?php echo mb_substr($deal["company_name"], 0, 65, "UTF-8"); ?></h5>


                                    <?php if ($this->uri->segment(3) === 'index'): ?>
                                        <div>
                                            <span>Продавец: <span  class="kt-font-boldest"><?= $deal['s_company_name'] ?></span></span>

                                        </div>
                                    <?php endif; ?>
                                </div>
                                <div class="status_deal">
                                    <?php if ($deal['status_deal'] == 2) { ?>
                                        <span class="text-success">Сделка совершена</span>
                                    <?php } elseif ($deal['status_deal'] == 0){?>
                                        <span class="text-danger">Сделка отменена</span>
                                    <?php } elseif ($deal['status_deal'] == 1){?>
                                        <span class="text-warning">Ожидает потверждения</span>
                                    <?php } elseif ($deal['status_deal'] == 3){?>
                                        <span style="color: #3BAFDA">Выдача кредита</span>
                                    <?php } elseif ($deal['status_deal'] == 4){?>
                                        <span style="color: #3BAFDA">Погашение кредита</span>
                                    <?php }?>

                                </div>

                                <div class="sum_company_deal">
                                    <span class="font-20 font__grad__bar">Сумма: </span><span
                                            class="font-20"><?php echo $deal['summa_sdelki'] / 100; ?></span>
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
                                <?php if ($this->uri->segment(3) === 'outbox' && $deal['status_deal'] != 3 && $deal['status_deal'] != 4) { ?>
                                <div class="pull-left">

                                    <address>
                                        <p class="kt-font-boldest">Информация платежа:</p>
                                        <p class="text-muted">
                                            Сумма платежа: <?php echo $deal['summa_sdelki'] / 100; ?>
                                            <br>Комиссия: <?php echo ($deal['summa_sdelki'] / 10000) * PERCENT_SYSTEM; ?>
                                            <?php if ($deal['coupon_sum'] != 0) echo '<br>Купон: ' . $deal['coupon_sum'] / 100;?>
                                            <br><p class="kt-font-boldest">Итого: <?php $itog = $deal['summa_sdelki'] * (1 + PERCENT_SYSTEM / 100) - $deal['coupon_sum'];
                                            echo $itog > 0 ? $itog / 100 : 0;
                                            ?></p>
                                        </p>

                                    </address>

                                </div>
                                <?php } ?>
                                <div class="pull-left m-l-50">

                                    <address>
                                        <?php if (!empty($deal['comment_deal'])) { ?>
                                            <p class="kt-font-boldest">Комментарий:</p>
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

                                            <?php if ($deal['status_deal'] == 1) { ?>
                                                <span class="btn btn-danger  width-per-40"
                                                      onclick="accept_or_cancel_deal(
                                                              0,<?php echo $deal['deal_id']; ?>,
                                                              '<?php echo $deal['buyer_deal_id']; ?>'
                                                              );">Отказать</span>
                                            <?php } ?>
                                        <?php } ?>
                                        <p class="addr-font-h3 kt-font-boldest"><?php if ($deal['status_deal'] == 2) { ?>

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
                                            <?php if (in_array($deal['status_deal'], [2, 3, 4])) { ?>
                                                <span class="label label-success">Успешно</span>
                                            <?php } elseif ($deal['status_deal'] == 0){?>
                                                <span class="label label-danger">Отказ</span>
                                            <?php } elseif ($deal['status_deal'] == 1){?>
                                                <span class="label label-warning">Ожидает потверждения</span>
                                            <?php }?>

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

            

            <?php echo $this->pagination->create_links(); ?>

        </div>
    </div>
</div>


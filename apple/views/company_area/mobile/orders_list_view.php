<section class="content">

        <div class="block-header">
            <div class="row">
                <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                    <h4>Отправленные сделки</h4>
                    <?php if (!empty($orders_list)): ?>
                    <div class="row clearfix ">
                        <?php foreach ($orders_list as $order): ?>

                            <div class="col-lg-3 col-md-4 col-sm-12 col-xs-12">
                                <div style="border-bottom: 3px solid white; border-radius: 5px" class="card counter-box shadow_material <?php if ($order['status_deal'] == 2) { ?>border-success<?php } ?><?php if ($order['status_deal'] == 0) { ?>border-danger<?php } ?>">
                                    <div class="name_company_deal">
                                        <a style="color:#111315" href="<?php echo site_url('company/cabinet/company_detail?company_id=' . $order['company_id']); ?>"><div><h5><?php echo mb_substr($order["company_name"], 0, 65, "UTF-8"); ?></h5></div></a>
                                    </div>
                                    <div class="city_company_deal">
                                        <p>г.<?php echo $order['city_name']; ?></p>
                                    </div>
                                    <div class="data_company_deal">
                                        <p><?php echo $order['date']; ?></p>
                                    </div>
                                    <div class="comment_company_deal">
                                        <?php if (!empty($order['comment_deal'])) { ?>
                                            <p><?php echo $order['comment_deal']; ?></p>
                                        <?php } ?>
                                    </div>
                                    <div class="sum_company_deal">
                                        <span class="font-20 font__grad__bar">Сумма: </span><span class="font-20"><?php echo $order['summa_sdelki'] / 100; ?></span>
                                    </div>
                                    <div class="status_company_deal m-t-10">
                                        <?php if ($order['status_deal'] == 1) { ?>
                                            <span class="btn btn-danger" onclick="accept_or_cancel_deal(
                                                    0,<?php echo $order['deal_id']; ?>,
                                                    '<?php echo $order['buyer_deal_id']; ?>'
                                                    );">Отказаться</span>
                                        <?php } ?>
                                        <?php if ($order['status_deal'] == 2) { ?>
                                            <?php if ($order['want_cancel'] != 1): ?>
                                                <button class="form-control"
                                                        onclick="cancel_deal(<?= $order['deal_id'] ?>)">
                                                    Отменить сделку
                                                </button>
                                            <?php endif; ?>
                                            <?php if ($order['want_cancel'] == 1): ?>
                                                <small>Запрос на отмену сделки отправлен</small>
                                            <?php endif; ?>
                                        <?php } ?>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                    <?php echo $this->pagination->create_links(); ?>
                </div>
            </div>
        </div>

</section>

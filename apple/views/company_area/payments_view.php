<section class="content">

        <div class="block-header">
            <div class="row">
                <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                    <div class="widget bg_light margin-b-30 padding-15">
                        <h4>История платежей за сервис</h4>

                        <?php if(!empty( $payments )) { ?>

                            <div class="table-responsive">
                                <table class="table table-striped table-hover">
                                    <tr>
                                        <th>ID платежа</th>
                                        <th>Дата платежа</th>
                                        <th>Сумма</th>
                                        <th>Способ оплаты</th>
                                        <th>Статус платежа</th>
                                    </tr>

                                    <?php foreach($payments as $payment) { ?>

                                        <tr>
                                            <td><?php echo $payment['payment_id']; ?></td>

                                            <?php
                                            //преобразуем дату в нормальный вид
                                            $myDateTime = DateTime::createFromFormat('Y-m-d H:i:s', $payment['date_payment']);
                                            $newDateString = $myDateTime->format('d.m.Y г.');
                                            ?>
                                            <td><?php echo $newDateString; ?></td>
                                            <td><?php echo $payment['summa'] / 100; ?> <i class="fa fa-rub" aria-hidden="true"></i></td>
                                            <?php

                                            switch($payment['type_payment']) {
                                                case 1:
                                                    $type_payment = "Яндекс.Деньги";
                                                    break;
                                                case 2:
                                                    $type_payment = "Безнал.";
                                                    break;
                                            }

                                            ?>
                                            <td><?php echo $type_payment; ?></td>

                                            <?php
//                                            switch($payment['status_payment']) {
//                                                case 0:
//                                                    $status_payment = "Не оплачен";
//                                                    break;
//                                                case 1:
//                                                    $status_payment = "Оплачен";
//                                                    break;
//                                            }
                                            $status_payment = "Оплачен";
                                            ?>
                                            <td><?php echo $status_payment; ?></td>

                                        </tr>

                                    <?php } ?>

                                </table>
                            </div>
                            <?php echo $this->pagination->create_links(); ?>

                        <?php } else { ?>

                            <p>Пока что вы не производили оплат за сервис...</p>

                        <?php } ?>

                    </div>
                </div>
            </div>
        </div>
        <!-- Your content goes here  -->

</section>










<div class="kt-space-20"></div>
<div class="kt-body kt-grid__item kt-grid__item--fluid kt-grid kt-grid--hor kt-grid--stretch" id="kt_body">
    <div class="kt-content  kt-grid__item kt-grid__item--fluid kt-grid kt-grid--hor" id="kt_content">
        <div class="kt-container  kt-grid__item kt-grid__item--fluid">
            <div class="row">
            <?php foreach ($coupons as $coupon):
                $status = 'не назначен';
                $highlight = 'bg-yellow';
                switch ($coupon['status']) {
                    case 0:
                        $status = 'активен';
                        $highlight = 'kt-bg-success';
                        break;
                    case 1:
                        $status = 'использован в сделке №' . $coupon['deal_id'];
                        $highlight = 'kt-font-danger';
                        break;
                    case 2:
                        $status = 'истек';
                        $highlight = 'kt-font-danger';
                } ?>
                <div class="col-xl-4">
                    <div class="kt-portlet kt-portlet--head-noborder">
                        <div class="kt-portlet__head">
                            <div class="kt-portlet__head-label">
                                <h3 class="kt-portlet__head-title  <?= $highlight ?>">
                                    <?= $status ?>
                                </h3>
                            </div>
                            <div class="kt-portlet__head-toolbar">
                                <span class="kt-badge kt-badge--bolder kt-badge kt-badge--inline kt-badge--danger">до: <?= (new DateTime($coupon['date_expire']))->format('d.m.Y H:i'); ?></span>
                            </div>
                        </div>
                        <div class="kt-portlet__body kt-portlet__body--fit-top">
                            <div class="kt-section kt-section--space-sm">
                                <h3>Сумма: <?= $coupon['summa'] / 100 ?></h3>
                            </div>
                        </div>
                    </div>
                </div>

            <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>

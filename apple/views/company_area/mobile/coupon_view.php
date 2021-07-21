<div class="page-content header-clear-large">
    <?php foreach ($coupons as $coupon):
        $status = 'не назначен';
        $highlight = 'bg-yellow';
        switch ($coupon['status']) {
            case 0:
                $status = 'активен';
                $highlight = '';
                break;
            case 1:
                $status = 'использован в сделке №' . $coupon['deal_id'];
                $highlight = 'opacity-30';
                break;
            case 2:
                $status = 'истек';
                $highlight = 'opacity-30';
        } ?>
        <div data-height="160" class="caption caption-margins round-medium shadow-tiny bottom-50" style="height: 160px;">
            <div class="caption-bottom left-15 bottom-15">
                <h1 class="color-white font-30">Сумма купона: <?= $coupon['summa'] / 100 ?></h1>
                <p class="color-white under-heading opacity-70 bottom-0 font-11">Статус: <?= $status ?></p>
            </div>
            <div class="caption-top top-15 right-15">
                <span class="button button-xxs float-right bg-white round-small color-black">Действителен до: <?= (new DateTime($coupon['date_expire']))->format('d.m.Y H:i'); ?></span>
            </div>
            <div class="caption-overlay bg-blue1-dark <?= $highlight ?>"></div>
            <div class="caption-bg bg-18"></div>
        </div>
    <?php endforeach; ?>
</div>
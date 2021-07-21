<div class="page-content header-clear-large">
    <?php if ($company_data['sub_status'] == 0) {
        ?>
        <div class="content">
            <div class="content-title has-border border-highlight bottom-0">
                <h1 style="color: red">Абоненская плата не оплачена</h1>
            </div>
            <span>Уважаемый участник системы!
Уведомляем Вас о том, что истёк срок оплаты использования расширенного функционала Вашего личного кабинета. Для того, чтобы пользоваться всеми функциями системы в течение следующих 28 дней Вам необходимо оплатить абонентскую плату в размере 500 рублей. Для оплаты нажмите на кнопку ниже.</span>
        </div>
        <div class="content">
            <?php if (false):?>
            <iframe src="https://money.yandex.ru/quickpay/button-widget?targets=barter-business.ru%3A%20%D0%9F%D0%BE%D0%BF%D0%BE%D0%BB%D0%BD%D0%B5%D0%BD%D0%B8%D0%B5%20%D0%90%D0%9F%20(28%20%D0%B4%D0%BD%D0%B5%D0%B9)%20%3A%20ID%20<?= $company_data['company_id'] ?>&default-sum=500&button-text=12&any-card-payment-type=on&button-size=m&button-color=orange&successURL=barter-business.ru%2Fcompany%2Fpayments%2Fsuccess_payment&quickpay=small&account=410015495536043&label=<?= $company_data['company_id'] ?>&" width="184" height="36" frameborder="0" allowtransparency="true" scrolling="no"></iframe>
            <?php endif; ?>
                <a href="#" onclick="restock_balance_manual(this, '<?= MONTHLY_PAYMENT ?>')" class=" button button-xs round-small shadow-large bg-highlight button-full bottom-30" style="width: 100%">Оплатить</a>

        </div>
        <?php
    } else { ?>
        <div class="content">
            <div style="text-align: center" class="content-title ">
                <h1 style="color: green">Абоненская плата оплачена</h1>
                <h4">до <?= $company_data['sub_end']?></h4>
            </div>
        </div>
    <?php } ?>


<div class="divider divider-margins"></div>
    <div class="visible-slider2-medium single-slider owl-carousel owl-no-dots">
        <div class="slider-item bottom-10">
            <div class="pricing-4 round-medium shadow-large bg-theme">
                <h1 class="pricing-title center-text bg-blue1-dark uppercase">3 месяца</h1>

                <p class="pricing-list center-text top-30 bottom-30">При оплате за 3 месяца Вам начисляется кэшбек 30% в бартерных рублях</p>
                <a onclick="restock_balance_manual(this, '<?= PAYMENT_THREEMONTH ?>')" href="#" class="button button-xs bg-blue2-dark button-center-large button-circle uppercase">Оплатить</a>
                <em class="center-text color-gray-dark small-text font-10 uppercase top-10 bottom-0"></em>
            </div>
            <div class="clear"></div>
        </div>
        <div class="slider-item bottom-10">
            <div class="pricing-4 round-medium shadow-large bg-theme">
                <h1 class="pricing-title center-text bg-blue1-dark uppercase">6 месяцев</h1>

                <p class="pricing-list center-text top-30 bottom-30">При оплате за 6 месяцев Вам начисляется кэшбек 50% в бартерных рублях</p>
                <a onclick="restock_balance_manual(this, '<?= PAYMENT_SIXMONTH ?>')" href="#" class="button button-xs bg-blue2-dark button-center-large button-circle uppercase">Оплатить</a>
                <em class="center-text color-gray-dark small-text font-10 uppercase top-10 bottom-0"></em>
            </div>
            <div class="clear"></div>
        </div>
        <div class="slider-item bottom-10">
            <div class="pricing-4 round-medium shadow-large bg-theme">
                <h1 class="pricing-title center-text bg-blue1-dark uppercase">12 месяцев</h1>

                <p class="pricing-list center-text top-30 bottom-30">При оплате за 12 месяцев Вам начисляется кэшбек 100% в бартерных рублях</p>
                <a onclick="restock_balance_manual(this, '<?= PAYMENT_TWELVEMONTH ?>')" href="#" class="button button-xs bg-blue2-dark button-center-large button-circle uppercase">Оплатить</a>
                <em class="center-text color-gray-dark small-text font-10 uppercase top-10 bottom-0"></em>
            </div>
            <div class="clear"></div>
        </div>
    </div>
    <div class="divider divider-margins top-40"></div>
</div>
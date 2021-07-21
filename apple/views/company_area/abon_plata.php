<div class="kt-space-20"></div>
<div class="kt-body kt-grid__item kt-grid__item--fluid kt-grid kt-grid--hor kt-grid--stretch" id="kt_body">
    <div class="kt-content  kt-grid__item kt-grid__item--fluid kt-grid kt-grid--hor" id="kt_content">
        <div class="kt-container  kt-grid__item kt-grid__item--fluid">


                <?php if ($company_data['sub_status'] == 0) {
                    ?>
                    <div class="alert alert-danger" role="alert">
                        <div class="alert-text">
                            <h4 class="alert-heading">Абоненская плата не оплачена!</h4>
                            <p>Уведомляем вас о том, что необходимо произвести оплату сервиса.
                                Функционал вашего аккаунта ограничен. Сумма для оплаты 500 рублей,
                                нажмите на кнопку чтобы оплатить сервис!</p>
                            <hr>
                            <button type="button" class="btn btn-brand btn-wide btn-upper btn-bold">Оплатить</button>
                        </div>
                    </div>
                    Абоненская плата не оплачена


                    <?php if (false): ?>

                    <?php endif; ?>

                    <?php
                } else { ?>
                    <div class="alert alert-success" role="alert">
                        <div class="alert-text">
                            <h4 class="alert-heading">Абоненская плата оплачена до <?= $company_data['sub_end'] ?></h4>

                        </div>
                    </div>


                <?php } ?>


            <div class="kt-portlet">

                <div class="kt-portlet__body">
                    <div class="kt-pricing-3 kt-pricing-3--fixed">
                        <div class="kt-pricing-3__items">
                            <div class="row row-no-padding">
                                <div class="kt-pricing-3__item col-lg-3">
                                    <div class="kt-pricing-3__wrapper">
                                        <h3 class="kt-pricing-3__title">1 месяц</h3>

                                        <span class="kt-pricing-3__description" style="margin-bottom: 36px;"><span>30 дней подписки на сервис</span></span>
                                        <div class="kt-pricing-3__btn">
                                            <button type="button" class="btn  btn-brand btn-wide btn-upper btn-bold">оплатить</button>
                                        </div>
                                    </div>
                                </div>
                                <div class="kt-pricing-3__item col-lg-3">
                                    <div class="kt-pricing-3__wrapper">
                                        <h3 class="kt-pricing-3__title">3 месяца</h3>

                                        <span class="kt-pricing-3__description"><span>При оплате за 3 месяца Вам начисляется кэшбек 30% в бартерных рублях</span></span>
                                        <div class="kt-pricing-3__btn">
                                            <button type="button" class="btn  btn-brand btn-wide btn-upper btn-bold">оплатить</button>
                                        </div>
                                    </div>
                                </div>
                                <div class="kt-pricing-3__item col-lg-3">
                                    <div class="kt-pricing-3__wrapper">
                                        <h3 class="kt-pricing-3__title">6 месяцев</h3>


                                        <span class="kt-pricing-3__description">
								<span>При оплате за 6 месяцев Вам начисляется кэшбек 50% в бартерных рублях</span>
							</span>
                                        <div class="kt-pricing-3__btn">
                                            <button type="button" class="btn btn-brand btn-wide btn-upper btn-bold">оплатить</button>
                                        </div>
                                    </div>
                                </div>
                                <div class="kt-pricing-3__item col-lg-3">
                                    <div class="kt-pricing-3__wrapper">
                                        <h3 class="kt-pricing-3__title">12 месяцев</h3>

                                        <span class="kt-pricing-3__description"><span>При оплате за 12 месяцев Вам начисляется кэшбек 100% в бартерных рублях</span></span>
                                        <div class="kt-pricing-3__btn">
                                            <button type="button" class="btn  btn-brand btn-wide btn-upper btn-bold">оплатить</button>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php if (false): ?>

            <?php endif; ?>
        </div>
    </div>
</div>


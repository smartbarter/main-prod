<div class="page-content header-clear-large">
    <div class="content">
    <div class="tab-controls tab-animated tabs-medium tabs-rounded"
         data-tab-items="2"
         data-tab-active="bg-blue1-dark">
        <a href="#" data-tab-active data-tab="tab-1">По сделкам</a>
        <a href="#" data-tab="tab-2">По обороту</a>
    </div>
        <div class="clear bottom-15"></div>
        <div class="tab-content" id="tab-1">

                <?php $c = 1; ?>
                <?php if (! empty($company_cout_deal)) { ?>

                        <?php foreach ($company_cout_deal as $company) { ?>

                        <div class="bottom-10" data-menu="menu-instant-3" data-height="220" onclick="open_company_detail(<?= $company['company_id'] ?>)">
                            <div class="content content-box round-medium shadow-small">
                                <div class="company__card">
                                    <div class="company__company___img">
                                        <img class="company__img" src="https://barter-business.ru/uploads/companys_logo/<?php echo $company['logo']; ?>" alt="">
                                    </div>
                                    <div class="company__company__title">
                                <span>
                                <?php echo mb_substr($company["company_name"],
                                    0, 65, "UTF-8"); ?></span>
                                    </div>
                                    <div class="company__company__desc">
                                        <?php echo $c; ?> место
                                        <div class="company__datail__footer">
                                            Кол-во продаж: <?php echo $company["num_deals"]?>

                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                            <?php $c++; ?>
                        <?php } ?>


                <?php } ?>
        </div>

        <div class="tab-content" id="tab-2" style="border: 1px solid transparent;">

                <?php $d = 1; ?>
                <?php if (! empty($company_sum_deal)) { ?>

                        <?php foreach ($company_sum_deal as $company) { ?>


                        <div class="bottom-10" data-menu="menu-instant-3" data-height="220" onclick="open_company_detail(<?= $company['company_id'] ?>)">
                            <div class="content content-box round-medium shadow-small">
                                <div class="company__card">
                                    <div class="company__company___img">
                                        <img class="company__img" src="https://barter-business.ru/uploads/companys_logo/<?php echo $company['logo']; ?>" alt="">
                                    </div>
                                    <div class="company__company__title">
                                <span>
                                <?php echo mb_substr($company["company_name"],
                                    0, 65, "UTF-8"); ?></span>
                                    </div>
                                    <div class="company__company__desc">
                                        <?php echo $d; ?> место
                                        <div class="company__datail__footer">
                                            Сумма продаж: <?php echo ceil($company["sum_deals"]/100) ?>

                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>


                            <?php $d++; ?>
                        <?php } ?>


                <?php } ?>

        </div>
    </div>
</div>

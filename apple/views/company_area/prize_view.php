<section class="content">
    <div class="row clearfix">
        <div class="col-sm-12 col-md-12 col-lg-12">
            <div class="row ">



                <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                    <div class="header">
                        <h2> Лидеры по кол-ву продаж</h2>
                    </div>
                    <?php $c = 1; ?>
                    <?php if (! empty($company_cout_deal)) { ?>
                        <div class="row clearfix multiple-items">
                            <?php foreach ($company_cout_deal as $company) { ?>


                                <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">

                                    <div class="card">
                                        <div class="body">
                                            <div class="review-block">
                                                <a href="<?php echo site_url('company/cabinet/company_detail?company_id='
                                                    . $company['company_id']); ?>" style="color: #0a0c0d">
                                                    <div class="row">
                                                        <div class="review-img" style="background: url(https://barter-business.ru/uploads/companys_logo/<?php echo $company['logo']; ?>);background-position: center;background-size: 100%;height: 70px;width: 70px;border-radius: 50px;margin-left: 15px">
                                                        </div>
                                                        <div class="col">
                                                            <h6 class="m-b-15"><?php echo mb_substr($company["company_name"],
                                                                    0, 55, "UTF-8"); ?> <span style="position: absolute; right: 0; bottom: 0" class="float-right m-r-10 "><?php echo $c; ?> место </span>
                                                            </h6>
                                                            <p class="m-t-15 m-b-15"> Кол-во продаж: <?php echo $company["num_deals"]?></p>
                                                        </div>
                                                    </div>

                                                </a>
                                            </div>
                                        </div>
                                    </div>

                                </div>
                                <?php $c++; ?>
                            <?php } ?>
                        </div>

                    <?php } ?>
                </div>

                <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                    <div class="header">
                        <h2> Лидеры по сумме продаж</h2>
                    </div>
                    <?php $d = 1; ?>
                    <?php if (! empty($company_sum_deal)) { ?>
                        <div class="row clearfix multiple-items">
                            <?php foreach ($company_sum_deal as $company) { ?>

                                <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">

                                    <div class="card">
                                        <div class="body">
                                            <div class="review-block">
                                                <a href="<?php echo site_url('company/cabinet/company_detail?company_id='
                                                    . $company['company_id']); ?>" style="color: #0a0c0d">
                                                    <div class="row">
                                                        <div class="review-img" style="background: url(https://barter-business.ru/uploads/companys_logo/<?php echo $company['logo']; ?>);background-position: center;background-size: 100%;height: 70px;width: 70px;border-radius: 50px;margin-left: 15px">
                                                        </div>
                                                        <div class="col">
                                                            <h6 class="m-b-15"><?php echo mb_substr($company["company_name"],
                                                                    0, 35, "UTF-8"); ?> <span style="position: absolute; right: 0; bottom: 0" class="float-right m-r-10 "><?php echo $d; ?> место </span>
                                                            </h6>
                                                            <p class="m-t-15 m-b-15"> Сумма продаж: <?php echo ceil($company["sum_deals"]/100) ?></p>
                                                        </div>
                                                    </div>

                                                </a>
                                            </div>
                                        </div>
                                    </div>

                                </div>
                                <?php $d++; ?>
                            <?php } ?>
                        </div>

                    <?php } ?>
                </div>



            </div>
        </div>
    </div>
</section>
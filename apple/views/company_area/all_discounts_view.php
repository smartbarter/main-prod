<div class="container-fluid">
    <div class="row new_deals">
        <div class="col-sm-12 col-md-12 col-lg-12">
            <h4>Скидки предоставляемые сегодня:</h4>
        </div>
    </div>

    <?php if( !empty($discounts_today) ) { ?>

        <?php $c = 0; ?>
    
        <?php foreach ($discounts_today as $discount) { ?>

            <?php if($c % 3 == 0) { ?>
                <div class="row">
            <?php } ?>
        
            <div class="col-sm-12-col-md-12 col-lg-4">
                <div class="widget bg_light margin-b-30 padding-15">

                    <p class="txt_center">
                        <img src="<?php echo site_url('uploads/companys_logo/' . $discount['logo']); ?>" height="100" width="100" class="img-circle logo_new_company img_border_shadow">
                    </p>
                    <p class="txt_center"><?php echo $discount['company_name']; ?></p>

                    <hr>
                    <p><?php echo mb_substr($discount["description_company"], 0, 100, "UTF-8"); ?>...</p>
                    
                    <hr>
                    <p class="txt_center">Сегодня предоставляют скидку: <span class="my_danger"><span style="font-size: 20px;"><?php echo $discount['summa_skidki']; ?></span>%</span></p>
                    
                    <hr>
                    <div class="txt_center">
                        <a href="<?php echo site_url('company/cabinet/company_detail?company_id=' . $discount['company_id']); ?>" class="btn btn-primary">Подробнее</a>
                    </div>

                </div>
            </div>

            <?php if ( $c % 3 == 2 || $c == count($discounts_today) - 1 ) { ?>
            
                </div>
            
            <?php } ?>

        <?php $c++; ?>
        
        <?php }//foreach ?>

        <?php echo $this->pagination->create_links(); ?>

    <?php } else { ?>

        <div class="row">
    
            <div class="col-sm-12-col-md-12 col-lg-12">
                <div class="widget bg_light margin-b-30 padding-15">
                    <h5>Увы, сегодня нет скидок...</h5>
                </div>
            </div>

        </div>

    <?php } ?>

</div>
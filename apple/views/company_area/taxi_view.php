<section class="content">
    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
    <div class="row clearfix">



                    <?php if (! empty($taxi)): ?>
                        <?php foreach ($taxi as $item): ?>

                            <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12">
                                <div class="box-part text-center shadow_material">

                                    <a style="color:#111315"
                                       href="<?php echo site_url('company/cabinet/company_detail?company_id='
                                           .$item['company_id']); ?>"><img
                                                src="https://barter-business.ru/uploads/companys_logo/<?php echo $item['logo']; ?>"
                                                height="100" width="100"
                                                class="img-circle logo_new_company img_border_shadow"></a>
                                    <a style="color:#111315"
                                       href="<?php echo site_url('company/cabinet/company_detail?company_id='
                                           .$item['company_id']); ?>">
                                        <div class="title p-t-15">
                                            <h5><?php echo mb_substr($item["company_name"],
                                                    0, 65, "UTF-8"); ?></h5></div>
                                    </a>


                                    <div style="text-align: left">
                                        <p><strong class="font__grad__bar">Телефон:</strong> <a
                                                    href="tel:+<?php echo $item['company_phone']; ?>">+<?php echo $item['company_phone']; ?></a>
                                        </p>
                                        <p><strong class="font__grad__bar">Город:</strong> <?php echo $item['city_name']; ?></p>
                                        <p><strong class="font__grad__bar">Машина:</strong> <?php echo $item['name_car'];  ?></p>
                                        <p><strong class="font__grad__bar">Район:</strong> <?php echo $item['area'];  ?></p>
<a style="width: 100%" href="tel:+<?php echo $item['company_phone']; ?>" class="waves-effect waves-light btn m-t-10 btn-border-radius">Позвонить</a>
                                    </div>

                                </div>

                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>



        </div>
    </div>
</section>
<div class="kt-space-20"></div>
<div class="kt-body kt-grid__item kt-grid__item--fluid kt-grid kt-grid--hor kt-grid--stretch" id="kt_body">
    <div class="kt-content  kt-grid__item kt-grid__item--fluid kt-grid kt-grid--hor" id="kt_content">

        <div class="kt-container  kt-grid__item kt-grid__item--fluid">

                        <?php if (!empty($products)): ?>

                                <div class="row clearfix ">
                                    <?php foreach ($products as $product): ?>
                                        <div class="col-lg-3 col-md-4 col-sm-6 col-xs-6">
                                            <div class="kt-portlet kt-portlet--height-fluid">
                                                <div class="kt-portlet__body">
                                                    <div class="product_image" style=" border-radius : 4px;height: 160px;background: url(https://barter-business.ru/uploads/products_image/<?= $product['image'] ?>);background-position: center;background-size: 100%;margin: -25px -25px 10px -25px;">
                                                    </div>
                                                    <div class="product_title">
                                                        <p style="font-size: 16px" class=" align-left"><?= $product['title'] ?></p>
                                                    </div>
                                                    <div class="product_price">
                                                        <div class="fa-pull-left">Цена:<br><span style="font-size: 22px"><?= $product['price'] ?></span></div>
                                                        <div class="fa-pull-right"><a href="<?= base_url('/company/cabinet/company_detail?company_id=' .$product['company_id']) ?>" class="btn  btn-brand btn-wide btn-upper btn-bold">Купить</a></div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach ?>
                                </div>
                            <?php endif ?>
        </div>
    </div>
</div>



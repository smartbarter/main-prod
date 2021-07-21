<div class="kt-space-20"></div>
<div class="kt-body kt-grid__item kt-grid__item--fluid kt-grid kt-grid--hor kt-grid--stretch" id="kt_body">
    <div class="kt-content  kt-grid__item kt-grid__item--fluid kt-grid kt-grid--hor" id="kt_content">

        <div class="kt-container  kt-grid__item kt-grid__item--fluid">

        <?php if ($company_data['status'] == 2 || $company_data['status'] == 3) { ?>

                            <button style="margin-bottom: 10px" data-toggle="modal" data-target="#add_prod" class="btn btn-primary"
                                    id="show_hide_deal_form">Добавить товар
                            </button>
                        <?php }//if?>
                        <?php if (!empty($products['products'])): ?>
                            <div class="row clearfix ">


                                    <?php foreach ($products['products'] as $product): ?>

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
                                                    <div class="fa-pull-right"> <button class="btn btn-danger" style="float: right" type="button" onclick="delete_product(<?= $product['id'] ?>); $(this).prop('disabled', true);">Удалить</button></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <?php endforeach ?>

                            </div>
                            <?php if (isset($links)): ?>
                                <?= $links ?>
                            <?php endif ?>
                        <?php endif; ?>
                        <form action="<?= base_url('/company/product/add') ?>" method="post"
                              enctype="multipart/form-data">
                            <div class="modal fade" id="add_prod" tabindex="-1" role="dialog"
                                 aria-labelledby="myModalLabel"
                                 aria-hidden="true">
                                <input type="hidden" name="<?= $csrf_name ?>" value="<?= $csrf_value ?>">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        

                                        <div class="modal-body" >
                                            <div class="col-sm-12 col-md-12 col-lg-12">
                                                <div class="form-group">
                                                    <label for="exampleFormControlFile1">Фото товара/услуги</label>




                                                            <input type="file" accept="image/*"
                                                                   class="form-control-file"
                                                                   name="product_image" required>



                                                </div>
                                                <div class="form-group">
                                                    <label for="formGroupExampleInput">Название товара/услуги</label>
                                                    <input id="input_text" type="text" data-length="65" maxlength="65"
                                                           class="form-control" name="product_title"
                                                           placeholder="Название товара" required>
                                                </div>
                                                <div class="form-group">
                                                    <label for="formGroupExampleInput">Цена товара/услуги</label>
                                                    <input type="number" class="form-control" name="product_price"
                                                           placeholder="Цена товара" min="100" required>
                                                </div>

                                            </div>
                                        </div>
                                        <div class="modal-footer m-t-70">
                                            <input type="submit" class="btn btn-primary" value="Отправить">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

<script>
  $(document).ready(function() {
    $('input#input_text, textarea#textarea2').characterCounter();
  });
</script>




















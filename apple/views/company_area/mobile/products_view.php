<div class="page-content header-clear-large">
    <div class="content">
    <?php if ($company_data['status'] == 2 || $company_data['status'] == 3) { ?>
        <a href="#" data-menu="menu-instant-1"
           class="button button-xs round-small shadow-large bg-highlight button-full bottom-30" style="width: 100%">Добавить товар</a>
    <?php }//if?>
    </div>
    <div class="divider divider-margins"></div>


    <?php if (!empty($products['products'])): ?>



            <?php foreach ($products['products'] as $product): ?>

            <div class="clear">
                <div class="one-half small-half">
                    <div data-height="140" class="caption" style="height: 140px;">
                        <div class="caption-image">

                            <div class="product__img" style="background: url(https://barter-business.ru/uploads/products_image/<?= $product['image'] ?>) 50% center / cover no-repeat;"></div>
                        </div>
                    </div>
                </div>
                <div class="one-half large-half last-column">
                    <h5 class="color-theme"><?= $product['title'] ?></h5>
                    <?php if (!isset($_GET['filter'])): ?>
                        <span class="under-heading font-10 color-highlight">Категория: <?= $product['category_title'] == null ? 'Нет категории' : $product['category_title'] ?></span>
                    <?php endif; ?>
                    <?php if ($product['description']!='') :?>
                        <p class="font-12 bottom-10 description_product">
                            <?= $product['description'] ?>
                        </p>
                    <?php endif; ?>
                    <div class="one-half">
                        <span  class="font-16"><?= $product['price'] ?> ₽</span>

                    </div>
                    <div class="button_delete_product bg-red1-dark">
                        <div onclick="$(this).prop('disabled', true); delete_product(<?= $product['id'] ?>); ">
                            <i class="fas fa-trash"></i>
                        </div>
                    </div>
                </div>
            </div>



            <?php endforeach ?>

        <div class="clear"></div>
        <?php if (isset($links)): ?>
            <?= $links ?>
        <?php endif ?>
    <?php else: ?>
        <div class="caption-center">
            <div class="content center-text bottom-0">
                <h1 class="font-28 ultrabold">Товаров нет</h1>
            </div>
        </div>
    <?php endif; ?>



</div>

<!-- Link to open the modal -->
<div class="menu-hider"></div>

<div id="menu-instant-1"
     class="menu-box"
     data-menu-type="menu-box-right"
     data-menu-height="100%"
     data-menu-width="100%"
     data-menu-effect="menu-over">

    <div data-height="60" class="caption shadow-tiny">
        <div class="caption-top left-10 top-10 right-10">
            <div class="caption-author-left">
                <a href="#" class="close-menu icon icon-xs float-right"><i
                            class="fa fa-times-circle color-red2-light font-24"></i></a>
            </div>
        </div>
    </div>

    <form action="<?= base_url('/company/product/add') ?>" method="post"
          enctype="multipart/form-data">
        <input type="hidden" name="<?= $csrf_name ?>" value="<?= $csrf_value ?>">
        <div class="content">
        <h2 class="left-text bottom-5">Добавление товара</h2>
        </div>
        <div class="content">
            <div class="file-field input-field form-control-file">
                <label class="label">
                    <i class="fas fa-upload"></i>
                    <span class="title">Добавить файл</span>
                    <input type="file" accept="image/*"
                           name="product_image" id="product_image" required>
                </div>
        </div>
        <div class="content">
            <label for="formGroupExampleInput">Название товара/услуги</label>
            <div class="input-style input-style-2">
                <input id="input_text" type="text" data-length="65" maxlength="65"
                       class="form-control" name="product_title"
                       placeholder="Название товара" required>
            </div>
        </div>
        <div class="content">
            <label for="formGroupExampleInput">Описание товара/услуги</label>
            <div class="input-style input-style-2">
                <input type="text" data-length="400" maxlength="400"
                       class="form-control" name="product_description"
                       placeholder="Описание товара" required>
            </div>
        </div>
        <div class="content">
            <label for="formGroupExampleInput">Цена товара/услуги</label>
            <div class="input-style input-style-2">
                <input type="number" class="form-control" name="product_price"
                       placeholder="Цена товара" min="100" required>
            </div>
        </div>
        <div class="content">
            <div class="input-style input-style-2 input-required">
                <label for="product_category">Категория товара</label>
                <select name="product_category">
                    <option value="0" selected>Общая</option>
                    <?php foreach ($product_categories as $category): ?>
                        <option value="<?= $category['category_id'] ?>"><?= $category['category_title'] ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
        <div class="content">
                <input style="width: 100%" type="submit" class="button button-m round-small shadow-large bg-highlight button-full bottom-30"
                       value="Отправить">
        </div>
    </form>

</div>
<script>
    $(document).ready(function () {
        $('input#input_text, textarea#textarea2').characterCounter();
    });

    function delete_product(product_id) {
        vex.dialog.confirm({
            message: "Вы точно хотите удалить этот товар?",
            buttons: [
                $.extend({}, vex.dialog.buttons.YES, { text: 'Да',
                    className: 'button button-s round-small shadow-large button-border button-full border-highlight color-green1-dark bg-transparent'}),
                $.extend({}, vex.dialog.buttons.NO, { text: 'Нет',
                    className: 'button button-s round-small shadow-large button-border button-full border-highlight color-red1-dark  bg-transparent'})
            ],
            callback: function (value) {
                if (value) {
                    let data = {
                        'product_id': product_id,
                        'token_form': $('#token_form').val(),
                    };
                    $.ajax({
                        url: base_url + 'company/company_ajax/ajax/deleteproduct',
                        type: 'POST',
                        dataType: 'JSON',
                        data: data,
                        cache: false,
                        success: function (data) {

                            //вставляем хэш
                            insert_csrf_hash(data);//функция берется из all_area_scripts!

                            var result = validate_data_server_response(data);

                            if (result) {
                                vex.dialog.alert("Товар успешно удален!");
                                setTimeout(function () {
                                    reload_page();
                                }, 1000);
                            } else {
                                vex.dialog.alert("Ошибка! " + data.text_message);
                            }
                        },
                    });
                }
            }
        });
    }

</script>




















<div class="page-content header-clear-large">
    <div class="content">
    <a href="<?= base_url() . 'company/product' ?>"
       class="button button-xs round-small shadow-large bg-highlight button-full bottom-10">Мои товары</a>
</div>
    <div class="divider divider-margins"></div>
    <div class="content">
        <div class="input-style input-style-2 input-required">
            <label for="">Категория товаров</label>
            <em><i class="fa fa-check color-green1-dark"></i></em>
            <select onChange="window.location.href=this.value">
                <option value="<?= base_url() . 'company/product/all' ?>" <?= !isset($_GET['filter']) ? 'selected' : '' ?>>Все</option>
                <?php foreach ($product_categories as $category): ?>
                    <option value="<?= base_url() . 'company/product/all' . '?filter=' . $category['category_id'] ?>"
                        <?= $_GET['filter'] == $category['category_id'] ? 'selected' : '' ?>>
                        <?= $category['category_title'] ?></option>
                <?php endforeach; ?>
            </select>
        </div>
    </div>
    <div class="content">
        <form action="<?= base_url() . 'company/product/search' ?>" method="get">
            <div>
                <div class="input-style input-style-2">
                    <input type="text" placeholder="Поиск товара" name="search" class="form-control search-query search-field">
                </div>

            </div>
        </form>
    </div>

    <?php if (!empty($products)): ?>
    <div class="content">
        <?php foreach ($products as $product): ?>
            <div class="clear color-highlight">
                <div class="one-half small-half">
                    <div data-height="140" class="caption" style="height: 140px;">
                        <div class="caption-image">
                            <a class="default-link"
                               href="https://barter-business.ru/uploads/products_image/<?= $product['image'] ?>"
                               data-lightbox="gallery-1">
                                <div class="product__img"
                                     style="background: url(https://barter-business.ru/uploads/products_image/<?= $product['image'] ?>) 50% center / cover no-repeat;"></div>
                            </a>
                        </div>
                    </div>
                </div>
                <div class="one-half large-half last-column">
                    <h5 class="color-theme"><?= $product['title'] ?></h5>
                    <?php if (!isset($_GET['filter'])): ?>
                        <span class="under-heading font-10 color-highlight">Категория: <?= $product['category_title'] == null ? 'Нет категории' : $product['category_title'] ?></span>
                    <?php endif; ?>
                    <?php if ($product['description'] != '') : ?>
                        <p class="font-12 bottom-10 description_product">
                            <?= $product['description'] ?>
                        </p>
                    <?php endif; ?>
                    <div class="one-half">
                        <span class="font-16"><?= $product['price'] ?> ₽</span>
                    </div>
                    <div class="product_buy_btn bg-blue1-dark">
                        <div data-menu="menu-instant-3" data-height="220"
                             onclick="open_company_detail(<?= $product['company_id'] ?>)">
                            <i class="fas fa-shopping-basket"></i>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach ?>
        <div class="clear"></div>
        <?php endif ?>

    </div>

    <?php if (isset($links)): ?>
            <?= $links ?>
        <?php endif ?>
</div>

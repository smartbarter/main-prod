<div class="page-content header-clear-large">
    <div id="map_category" style="width: 100%; height: 200px; margin-top: -28px "></div>
    <script>
        $(document).ready(function () {
            var myMap;
            ymaps.ready(init);

            function init() {
                if (!myMap) {
                    myMap = new ymaps.Map('map_category', {
                        center: [54.735842, 55.958384],
                        zoom: 10,
                    }, {
                        searchControlProvider: 'yandex#search',
                    }),
                        objectManager = new ymaps.ObjectManager({
                            // Чтобы метки начали кластеризоваться, выставляем опцию.
                            clusterize: true,
                            // ObjectManager принимает те же опции, что и кластеризатор.
                            gridSize: 32,
                            clusterDisableClickZoom: true,
                        });

                    // Чтобы задать опции одиночным объектам и кластерам,
                    // обратимся к дочерним коллекциям ObjectManager.
                    objectManager.objects.options.set('preset', 'islands#blueDotIcon');
                    objectManager.clusters.options.set('preset', 'islands#blueClusterIcons');
                    objectManager.add(<?= $data_json ?>);
                    myMap.geoObjects.add(objectManager);
                }
            };
        });
    </script>
    <div class="divider-margins"></div>
    <div class="content">
        <div class="txt_left">
            <a href="#" data-menu="sort-menu"
               class="button button-xs round-small shadow-large bg-highlight button-full bottom-10">Сортировка</a>
        </div>
    </div>
    <div class="divider"></div>
    <?php if (!empty($companies_from_category)) { ?>

        <?php foreach ($companies_from_category as $company) { ?>

            <div style="padding: 10px" class="content round-medium shadow-small" data-menu="menu-instant-3"
                 onclick="open_company_detail(<?= $company['company_id'] ?>)">
                <div class="company__card">
                    <div class="company__company___img">
                        <img class="company__img"
                             src="https://barter-business.ru/uploads/companys_logo/<?php echo $company['logo']; ?>"
                             alt="">
                    </div>
                    <div class="company__company__title">

                                <span>
                                <?php echo mb_substr($company["company_name"],
                                    0, 65, "UTF-8"); ?></span>
                        </a>


                    </div>
                    <div class="company__company__desc">
                        <?php if (!empty($company['geo_code'])
                            && !empty($company_data['geo_code'])
                        ): ?>
                            <span class="font-16 "><?php echo $company['adress'] ?></span><br>
                            <span class="font-16 ">Расстояние: <?php echo calculateTheDistance($company_data['geo_code'], $company['geo_code']) ?></span>
                        <?php endif; ?>

                    </div>
                </div>
            </div>

            <?php
        }//foreach?>

        <?php echo $this->pagination->create_links(); ?>

        <?php
    } else {
        ?>

        <div class="row">

            <div class="col-sm-12-col-md-12 col-lg-12">
                <div class="widget bg_light margin-b-30 padding-15">
                    <h5>В данной категории пока что нет компаний...</h5>
                </div>
            </div>

        </div>

        <?php
    } ?>

</div>
<div class="menu-hider"></div>
<div id="sort-menu"
     class="menu-box menu-box-detached round-medium"
     data-menu-type="menu-box-bottom"
     data-menu-height="330"
     data-menu-effect="menu-parallax">

    <div class="page-title has-subtitle">
        <div class="page-title-left">
            <a href="#">Сортировка</a>
        </div>
        <div class="page-title-right">
            <a href="#" class="close-menu"><i class="fa fa-times-circle font-20 color-red2-dark"></i></a>
        </div>
    </div>

    <div class="content bottom-20">
        <div class="link-list link-list-1 link-list-icon-bg">
            <?php $sorts = [
                0 => 'По кол-ву сделок за 30 дней',
                1 => 'По общему кол-ву сделок',
                2 => 'По дате регистрации',
                3 => 'По лимиту',
                4 => 'По остатку лимита',
                5 => 'По добавлениям в избранное',
            ]; ?>
            <?php foreach ($sorts as $i => $text): ?>
                <a href="<?= base_url() . 'company/category?id=' . $_GET['id'] . '&sort=' . $i . '&order=' . ($order['type'] == $i ? ($order['order'] == 'd' ? 'a' : 'd') : 'd') ?>">
                    <span><?= $text ?></span>
                    <i class="fas <?= ($order['type'] == $i ? ($order['order'] == 'd' ? 'fa-sort-amount-down' : 'fa-sort-amount-up') : '') ?>"
                       style="font-size: 20px;
                       <?= $order['order'] != 'd' ? 'transform: scale(1, -1);' : ''?>"></i>
                </a>
            <?php endforeach; ?>
        </div>
    </div>
</div>


<?php

function calculateTheDistance($a, $b)
{
    define('EARTH_RADIUS', 6372795);
    define('M_PI', 3.14);
    $a = explode(' ', $a);
    $b = explode(' ', $b);
    // перевести координаты в радианы
    $lat1 = $a[1] * M_PI / 180;
    $lat2 = $b[1] * M_PI / 180;
    $long1 = $a[0] * M_PI / 180;
    $long2 = $b[0] * M_PI / 180;

    // косинусы и синусы широт и разницы долгот
    $cl1 = cos($lat1);
    $cl2 = cos($lat2);
    $sl1 = sin($lat1);
    $sl2 = sin($lat2);
    $delta = $long2 - $long1;
    $cdelta = cos($delta);
    $sdelta = sin($delta);

    // вычисления длины большого круга
    $y = sqrt(pow($cl2 * $sdelta, 2) + pow($cl1 * $sl2 - $sl1 * $cl2 * $cdelta, 2));
    $x = $sl1 * $sl2 + $cl1 * $cl2 * $cdelta;

    //
    $ad = atan2($y, $x);
    $dist = $ad * EARTH_RADIUS;

    if ($dist > 1000) {
        $dist = $dist / 1000;
        $dist = number_format($dist, '1', ',', ' ') . ' км.';
    } else {
        $dist = number_format($dist, '1', ',', ' ') . " м.";
    }

    return $dist;
}

?>
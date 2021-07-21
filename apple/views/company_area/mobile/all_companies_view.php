<div class="page-content header-clear-large">

    <div class="divider-margins"></div>
    <div class="divider"></div>
    <?php if (!empty($companies)) { ?>

        <?php foreach ($companies as $company) { ?>

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
                            <span class="font-16 "><?php echo $company_data['adress'] ?></span><br>
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
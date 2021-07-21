<section class="content">
    <div class="row">
        <?php if (!empty($companies)) {?>

            <?php foreach ($companies as $company) { ?>


                <div class="col-lg-3 col-md-4 col-sm-12 col-xs-12" style="<?php if (($company['sum_deals'] > $company['month_limit']) && ($company['barter_job'] == '0'))  {?>display: none;<?php } ?>">
                    <span class="procent_rab font__grad__bar"><?= $company['sverh_limit'] ?>%</span>
                    <div class="box-part text-center shadow_material">

                        <a style="color:#111315"
                           href="<?php echo site_url('company/cabinet/company_detail?company_id='
                               .$company['company_id']); ?>"><img
                                    src="https://barter-business.ru/uploads/companys_logo/<?php echo $company['logo']; ?>"
                                    height="100" width="100"
                                    class="img-circle logo_new_company img_border_shadow"></a>
                        <a style="color:#111315"
                           href="<?php echo site_url('company/cabinet/company_detail?company_id='
                               .$company['company_id']); ?>">
                            <div class="title p-t-15">
                                <h5><?php echo mb_substr($company["company_name"],
                                        0, 65, "UTF-8"); ?></h5></div>
                        </a>
                        <div class="text p-b-20 p-t-20"><p
                                    style="overflow: hidden;text-align: left;"><?php echo mb_substr(str_replace("\n",
                                    '<br />', $company["description_company"]),
                                    0, 120, "UTF-8"); ?>...</p></div>

                        <div style="text-align: left">
                            <p><strong class="font__grad__bar">Телефон:</strong> <a
                                        href="tel:+<?php echo $company['company_phone']; ?>">+<?php echo $company['company_phone']; ?></a>
                            </p>
                            <p><strong class="font__grad__bar">Город:</strong> <?php echo $company['city_name']; ?></p>
                            <?php if (!empty($company['adress'])) {
                                ?>
                                <p><strong class="font__grad__bar">Адрес:</strong> <?php echo $company['adress']; ?></p>
                                <?php
                            } ?>
                        </div>


                    </div>
                </div>











                <?php
            }//foreach?>

            <?php echo $this->pagination->create_links(); ?>

            <?php
        } else {
            ?>
    </div>
            <div class="row">

                <div class="col-sm-12-col-md-12 col-lg-12">
                    <div class="widget bg_light margin-b-30 padding-15">
                        <h5>В данной категории пока что нет компаний...</h5>
                    </div>
                </div>

            </div>

            <?php
        } ?>


    <div class="modal fade bd-map-modal-lg map_cat" tabindex="-1" role="dialog"
         aria-labelledby="myLargeModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal"
                            aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>

                </div>
                <div style="padding: 0px" class="modal-body">
                    <div id="map1" style="width: 100%; height: 400px"></div>
                </div>

            </div>
        </div>
    </div>
</section>
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
        $dist = number_format($dist, '1', ',', ' ').' км.';
    } else {
        $dist = number_format($dist, '1', ',', ' ')." м.";
    }

    return $dist;
}

?>
<script>
  ymaps.ready(init);

  function init() {
    var myMap;

    if (!myMap) {
      myMap = new ymaps.Map('map', {
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
      myMap.geoObjects.add(objectManager);

      objectManager.add(<?= $data_json ?>);
    }
  }
</script>
<script>
  ymaps.ready(init);

  function init() {
    var myMap;
    $('#maps').bind({
      click: function() {
        if (!myMap) {
          myMap = new ymaps.Map('map1', {
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
          myMap.geoObjects.add(objectManager);

          objectManager.add(<?= $data_json ?>);
        }
      },
    });
  }
</script>

<div class="kt-space-20"></div>
<div class="kt-body kt-grid__item kt-grid__item--fluid kt-grid kt-grid--hor kt-grid--stretch" id="kt_body">
    <div class="kt-content  kt-grid__item kt-grid__item--fluid kt-grid kt-grid--hor" id="kt_content">
        <div class="kt-container  kt-grid__item kt-grid__item--fluid">
            <div class="row">
        <?php if (!empty($companies_from_category)) {?>

            <?php foreach ($companies_from_category as $company) { ?>

                <div class="col-xl-4">
                    <div class="kt-portlet kt-portlet--height-fluid">

                        <div class="kt-portlet__body">
                            <!--begin::Widget -->
                            <div class="kt-widget kt-widget--user-profile-2">
                                <div class="kt-widget__head">
                                    <div class="kt-widget__media">
                                        <img class="kt-widget__img kt-hidden-" src="https://barter-business.ru/uploads/companys_logo/<?php echo $company['logo']; ?>" alt="image">

                                    </div>

                                    <div class="kt-widget__info">
                                        <a href="<?php echo site_url('company/cabinet/company_detail?company_id='
                                            . $company['company_id']); ?>" class="kt-widget__username"><?php echo mb_substr($company["company_name"],
                                                0, 65, "UTF-8"); ?></a>


                                    </div>
                                </div>

                                <div class="kt-widget__body">
                                    <div class="kt-widget__section">
                                        <span><?php echo mb_substr(str_replace("\n",
                                            '<br>',
                                            $company["description_company"]),
                                            0, 120, "UTF-8"); ?></span>
                                    </div>


                                </div>

                            </div>
                            <!--end::Widget -->
                        </div>
                    </div>
                    <!--End::Portlet-->
                </div>




                <?php
            }//foreach?>

            <?php echo $this->pagination->create_links(); ?>

            <?php
        } else {
            ?>
            
            
            <?php
        } ?>

            </div>

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

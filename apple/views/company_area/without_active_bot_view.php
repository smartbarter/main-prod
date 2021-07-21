<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title><?php echo $title; ?> | <?php echo PROJECT_NAME; ?></title>

    <!-- CSS -->
    <link href="https://fonts.googleapis.com/css?family=Roboto:300,400,700&amp;subset=cyrillic" rel="stylesheet">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="<?php echo site_url('assets/styles/all_area/base_styles.css'); ?>">


    <script type="text/javascript" src="//vk.com/js/api/openapi.js?153"></script>

</head>
<body>


<div class="container need_active_bot">
    <div class="row">
        <div class="col-sm-12 col-md-12 col-lg-12">

            <div class="txt_center">

                <?php if($company_data['status'] == 0) { ?>
                    <h4>К сожалению ваша компания заблокирована!</h4>
                    <p>Комментарий о причине блокировки ниже...</p>
                    <div class="alert alert-danger" role="alert">

                        <?php echo $company_data['ban_comment']; ?>

                    </div>

                <?php } elseif($company_data['status'] == 4) { ?>

                    <h4>Ваш договор на расторжении!</h4>
                    <p>Если у вас возникли какие-то вопросы, свяжитесь с вашим менеджером!</p>


<?php } elseif($company_data['status'] == 5) { ?>

                    <h4>Ваша компания удалена!</h4>
                    <p>Если у вас возникли какие-то вопросы, свяжитесь с вашим менеджером!</p>

                <?php } ?>

            </div>

        </div>
        <div class="col-sm-12 col-md-12 col-lg-12" style="text-align: center; margin-top: 40px;">


                <a style="color: #6100ff;font-size: 16px" class="btn btn-link" href="">Позвонить в поддержку</a><br><br>
                <a style="color: #6100ff;font-size: 16px" class="btn btn-link" href="">Выход</a>


        </div>
    </div>
</div>

<!-- Виджет от ВК -->

</body>
</html>
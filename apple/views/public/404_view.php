<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>404 | <?php echo PROJECT_NAME; ?></title>
    <link rel="shortcut icon" href="<?php echo site_url('favicon.ico'); ?>" type="image/x-icon">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
	<link rel="stylesheet" href="<?php echo site_url('assets/styles/all_area/base_styles.css'); ?>">
	<link rel="stylesheet" href="<?php echo site_url('assets/styles/public/404.css'); ?>">
</head>
<body>

    <div class="container page_not_found">
        <div class="row">
            <div class="col-sm-12 col-md-3 col-lg-3"></div>
            <div class="col-sm-12 col-md-6 col-lg-6 txt_center">
                <h2>404</h2>
                <h3>Упс... Мы не можем найти эту страницу...</h3>
                <a href="<?php echo site_url(); ?>" class="btn btn-primary">Вернуться на главную</a>
            </div>
            <div class="col-sm-12 col-md-3 col-lg-3"></div>
        </div>
    </div>
    
</body>
</html>
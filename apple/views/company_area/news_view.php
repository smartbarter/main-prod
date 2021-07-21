<section class="content">

        <div class="row clearfix">
            <?php foreach ($all_news as $news):?>
            <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12">
                <div class="card text-center shadow_material">
                    <div class="card-img-top" style="
                            background: url('<?php echo site_url('uploads/news_img/' . $news['img']); ?>');
                            background-position: center;
                            background-size: 100% 25rem;
                            height: 200px;"></div>
                    <div class="card-body">
                        <a href="<?php echo site_url('/company/news/detail/news_detail?news_id=' . $news['news_id']); ?>" style="color: #111315">
                    <div class="news_title left-align">
                        <h5 class="card-title"><?php echo $news['title']; ?></h5>
                    </div>
                        </a>
                    <div class="news_description left-align">
                        <p>
                            <?php echo mb_substr($news["description"],
                                0, 35, "UTF-8"). '...'; ?>
                        </p>
                    </div>
                    <div class="news_date left-align">
                        <p class="card-text">
                            <small class="text-muted"> <?php echo $news["date"]; ?></small>
                        </p>
                    </div>
                    </div>
                </div>
            </div>

            <?php endforeach; ?>

        </div>



</section>
<section class="content">



                <div class="news_detail card text-justify shadow_material" style="margin-bottom: 100px">
                    <div class="card-img-top">
                        <img src="<?php echo site_url('uploads/news_img/' . $news_detail['img']); ?>" alt="">
                    </div>
                    <div class="card-body">
                            <div class="news_title left-align">
                                <h5 class="card-title"><?php echo $news_detail['title']; ?></h5>
                            </div>

                        <div class="news_description left-align">
                            <p class="font-16">
                                <?php echo str_replace("\n", '<br />',
                                    $news_detail['description']); ?>
                            </p>
                        </div>
                        <div class="news_date left-align">
                            <p class="card-text">
                                <small class="text-muted"> <?php echo $news_detail["date"]; ?></small>
                            </p>
                        </div>
                    </div>
                </div>


</section>
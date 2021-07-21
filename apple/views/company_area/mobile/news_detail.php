<div class="page-content header-clear-large">

                <div>
                    <div>
                        <img style="width: 100%; margin-top: -30px;" src="<?php echo site_url('uploads/news_img/' . $news_detail['img']); ?>" alt="">
                    </div>

                    <div class="content-box">
                        <h5 class="card-title"><?php echo $news_detail['title']; ?></h5>
                        <p class="font-16"><?php echo str_replace("\n", '<br />', $news_detail['description']); ?></p>
                        <p class="card-text"><small class="text-muted"> <?php echo $news_detail["date"]; ?></small></p>
                    </div>
                </div>
</div>
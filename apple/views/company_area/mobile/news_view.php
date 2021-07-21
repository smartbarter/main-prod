<div class="page-content header-clear-large">

        <div class="content">
            <?php foreach ($all_news as $news):?>
                <div class="caption round-medium shadow-small bottom-10">
                    <div class="caption-bottom">
                        <a href="<?php echo site_url('/company/news/detail/news_detail?news_id=' . $news['news_id']); ?>">
                            <span class=" center-text uppercase ultrabold home__news__span"><?php echo $news['title']; ?></span>
                        </a>
                    </div>
                    <div class="caption-overlay bg-gradient"></div>
                    <img class="caption-image owl-lazy" data-src="<?php echo site_url('uploads/news_img/' . $news['img']); ?>" src="<?php echo site_url('uploads/news_img/' . $news['img']); ?>" style="opacity: 1;">
                </div>

            <?php endforeach; ?>

        </div>



</div>
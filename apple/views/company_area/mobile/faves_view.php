<div class="page-content header-clear-large">

                        <?php if (!empty($faves)): ?>

                            <?php foreach ($faves as $company): ?>


                                <div style="padding: 10px" class="content round-medium shadow-small" data-menu="menu-instant-3"
                                     onclick="open_company_detail(<?= $company['company_id'] ?>)">
                                    <div class="company__card">
                                        <div class="company__company___img">
                                            <img class="company__img"
                                                 src="https://barter-business.ru/uploads/companys_logo/<?php echo $company['logo']; ?>"
                                                 alt="">
                                        </div>
                                        <div class="company__company__title">
                                            <span><?php echo mb_substr($company["company_name"], 0, 65, "UTF-8"); ?></span>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                            <?php endif; ?>



        </div>



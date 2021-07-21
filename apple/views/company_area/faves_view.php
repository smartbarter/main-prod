<div class="kt-space-20"></div>
<div class="kt-body kt-grid__item kt-grid__item--fluid kt-grid kt-grid--hor kt-grid--stretch" id="kt_body">
    <div class="kt-content  kt-grid__item kt-grid__item--fluid kt-grid kt-grid--hor" id="kt_content">
        <div class="kt-container  kt-grid__item kt-grid__item--fluid">
            <div class="row">
                        <?php if (!empty($faves)): ?>
                        
                            <?php foreach ($faves as $company): ?>

                                <div class="col-xl-4">
                                    <div class="kt-portlet kt-portlet--height-fluid">
                                        <div style="position: absolute;top: 12px;font-size: 18px;color: #868686;right: 28px;">
                                            <span id="delete_from_fave" onclick="delete_from_fave(<?= $company['company_id'] ?>); return false;"><i class="fa fa-times"></i></span>
                                        </div>
                                        <div class="kt-portlet__body">
                                            <!--begin::Widget -->
                                            <div class="kt-widget kt-widget--user-profile-2">
                                                <div class="kt-widget__head">
                                                    <div class="kt-widget__media">
                                                        <img class="kt-widget__img kt-hidden-" src="https://barter-business.ru/uploads/companys_logo/<?php echo $company['logo']; ?>" alt="image">
                                                        <div class="kt-widget__pic kt-widget__pic--success kt-font-success kt-font-boldest kt-hidden">
                                                            ChS
                                                        </div>
                                                    </div>

                                                    <div class="kt-widget__info">
                                                        <a href="<?php echo site_url('company/cabinet/company_detail?company_id='
                                                            . $company['company_id']); ?>" class="kt-widget__username"><?php echo mb_substr($company["company_name"],
                                                                0, 65, "UTF-8"); ?></a>


                                                    </div>
                                                </div>

                                                <div class="kt-widget__body">
                                                    <div class="kt-widget__section"><span><?php echo mb_substr(str_replace("\n",
                                                            '<br />',
                                                            $company["description_company"]),
                                                            0, 200, "UTF-8"); ?></span></div>


                                                </div>

                                            </div>
                                            <!--end::Widget -->
                                        </div>
                                    </div>
                                    <!--End::Portlet-->
                                </div>
                                
                            <?php endforeach; ?>
                            <?php endif; ?>


            </div>
        </div>
    </div>
</div>


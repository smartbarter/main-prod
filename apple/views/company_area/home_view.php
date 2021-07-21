
            <div class="kt-space-20"></div>
            <div class="kt-body kt-grid__item kt-grid__item--fluid kt-grid kt-grid--hor kt-grid--stretch" id="kt_body">
                <div class="kt-content  kt-grid__item kt-grid__item--fluid kt-grid kt-grid--hor" id="kt_content">
                    <div class="kt-container  kt-grid__item kt-grid__item--fluid">
                        <div class="row">
                            <div class="col-xl-4 col-lg-6 order-lg-1 order-xl-1">
                                <!--begin:: Widgets/Inbound Bandwidth-->
                                <div class="kt-portlet ">
                                    <div class="kt-portlet__head">
                                        <div class="kt-portlet__head-label">
                                            <h3 class="kt-portlet__head-title">
                                                Категории
                                            </h3>
                                        </div>
                                    </div>
                                    <div class="accordion" id="accordionExample1">

                                        <?php if(!empty($category_list)) { ?>


                                                    <?php echo $category_list ?>
                                                
                                        <?php } ?>
                                    </div>

                                </div>
                                <!--end:: Widgets/Inbound Bandwidth-->
                                <div class="kt-space-20"></div>
                                <!--begin:: Widgets/Outbound Bandwidth-->

                                <!--end:: Widgets/Outbound Bandwidth-->    </div>
                            <div class="col-xl-8 order-lg-1 order-xl-1">
                                <div class="row">
                                    <?php foreach ($last_new_companies as $last_company) { ?>
                                        <div class="col-xl-4">
                                            <div class="kt-portlet kt-portlet--height-fluid">

                                                <div class="kt-portlet__body">
                                                    <!--begin::Widget -->
                                                    <div class="kt-widget kt-widget--user-profile-2">
                                                        <div class="kt-widget__head">
                                                            <div class="kt-widget__media">
                                                                <img class="kt-widget__img kt-hidden-" src="https://barter-business.ru/uploads/companys_logo/<?php echo $last_company['logo']; ?>" alt="image">
                                                                <div class="kt-widget__pic kt-widget__pic--success kt-font-success kt-font-boldest kt-hidden">
                                                                    ChS
                                                                </div>
                                                            </div>

                                                            <div class="kt-widget__info">
                                                                <a href="<?php echo site_url('company/cabinet/company_detail?company_id='
                                                                    . $last_company['company_id']); ?>" class="kt-widget__username"><?php echo mb_substr($last_company["company_name"],
                                                                        0, 65, "UTF-8"); ?></a>


                                                            </div>
                                                        </div>

                                                        <div class="kt-widget__body">
                                                            <div class="kt-widget__section"><span><?php echo mb_substr(str_replace("\n",
                                                                    '<br />',
                                                                    $last_company["description_company"]),
                                                                    0, 200, "UTF-8"); ?></span></div>


                                                        </div>
                                                        
                                                    </div>
                                                    <!--end::Widget -->
                                                </div>
                                            </div>
                                            <!--End::Portlet-->
                                        </div>
                                        <?php } ?>



                                </div>

                            </div>


                        </div>

                    </div>

                </div>
            </div>


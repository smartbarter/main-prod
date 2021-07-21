
</div>
</div>
</div>

<!-- end:: Page -->



<!-- begin::Scrolltop -->
<div id="kt_scrolltop" class="kt-scrolltop">
    <i class="fa fa-arrow-up"></i>
</div>


<div class="modal fade bd-example-modal-lg" tabindex="-1" role="dialog"
     aria-labelledby="myLargeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">

        <div class="modal-content">
            <div class="modal-header">
                <button style="font-size: 30px;position: absolute;right: 10px; top: 0px;" type="button" class="close"
                        data-dismiss="modal" aria-hidden="true">
                    &times;
                </button>

            </div>
            <div class="modal-body">

                <input class="input_grey" type="text" name="search_field" id="search_field"
                       placeholder="Поиск" autofocus>
                <div class="search_result widget" id="search_result"
                     style="display: none;">
                </div>

                <div class="preloader tx" id="loader" style="display: none; margin-left: calc(50% - 25px);">
                    <div class="spinner-layer pl-deep-purple">
                        <div class="circle-clipper left">
                            <div class="circle"></div>
                        </div>
                        <div class="circle-clipper right">
                            <div class="circle"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- begin::Global Config(global config for global JS sciprts) -->
<script>
    var KTAppOptions = {
        "colors": {
            "state": {
                "brand": "#374afb",
                "light": "#ffffff",
                "dark": "#282a3c",
                "primary": "#5867dd",
                "success": "#34bfa3",
                "info": "#36a3f7",
                "warning": "#ffb822",
                "danger": "#fd3995"
            },
            "base": {
                "label": ["#c5cbe3", "#a1a8c3", "#3d4465", "#3e4466"],
                "shape": ["#f0f3ff", "#d9dffa", "#afb4d4", "#646c9a"]
            }
        }
    };
</script>

<script src="<?php echo site_url('assets/js/all_area/phone_digit.js'); ?>"></script>
<script src="<?php echo site_url('assets/js/all_area/all_area_scripts.js'); ?>"></script>
<script src="<?php echo site_url('assets/js/cabinets_area/all_cabinets.js'); ?>"></script>
<script src="<?php echo site_url('assets/js/cabinets_area/plugins.bundle.js'); ?>"></script>
<script src="<?php echo site_url('assets/js/cabinets_area/scripts.bundle.js'); ?>"></script>
<script src="<?php echo site_url('assets/js/cabinets_area/dashboard.js'); ?>"></script>
<script type="text/javascript" src="<?php echo site_url('assets/js/all_area/admin.js'); ?>"></script>
<!-- подключаем скрипты компании -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/7.28.11/sweetalert2.min.js"></script>
<script src="<?php echo site_url('assets/js/company_cabinet_area/scripts.js'); ?>"></script>
<script src="https://cdn.jsdelivr.net/npm/clipboard@2/dist/clipboard.min.js"></script>

<!--end::Page Scripts -->
</body>
</html>
<input type="hidden" id="token_form" class="hidden_field token_form"
       name="<?php echo $this->security->get_csrf_token_name(); ?>"
       value="<?php echo $this->security->get_csrf_hash(); ?>" />


<script src="<?php echo site_url('assets/js/all_area/phone_digit.js'); ?>?v7"></script>
<script src="<?php echo site_url('assets/js/all_area/all_area_scripts.js'); ?>?v8"></script>
<script src="<?php echo site_url('assets/js/cabinets_area/all_cabinets.js'); ?>?v7"></script>
<script src="<?php echo site_url('assets/js/cabinets_area/scripts.js'); ?>?v7"></script>
<script type="text/javascript" src="<?php echo site_url('assets/js/all_area/admin.js'); ?>?v8"></script>
<!-- подключаем скрипты компании -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/7.28.11/sweetalert2.min.js"></script>
<script src="<?php echo site_url('assets/js/company_cabinet_area/scripts.js'); ?>?v10"></script>
<script src="https://cdn.jsdelivr.net/npm/clipboard@2/dist/clipboard.min.js"></script>


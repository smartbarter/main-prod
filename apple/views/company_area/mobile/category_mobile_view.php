
<div class="page-content header-clear-large">
    <div class="content">
        <div class="tab-controls tab-animated tabs-large tabs-rounded" data-tab-items="2" data-tab-active="bg-blue1-dark">
            <a href="#" data-tab-active="" data-tab="tab-8" style="width: 50%;" class="bg-blue1 color-white">Услуги</a>
            <a href="#" data-tab="tab-9" style="width: 50%;">Товары</a>
        </div>
        <div class="clear bottom-15"></div>
        <div class="tab-content" id="tab-8" style="display: block;">
            <div class="columns-two">
                <?php echo $goods['top']?>
            </div>

        </div>
        <div class="tab-content" id="tab-9">
            <div class="columns-two">
                <?php echo $services['top']?>
            </div>
        </div>
    </div>
</div>
<div class="menu-hider"></div>
<?php echo $goods['bottom']?>
<?php echo $services['bottom']?>

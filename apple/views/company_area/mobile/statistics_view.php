<div class="page-content header-clear-large">

    <div class="content">
        <h2>Статистика
                <select style="background: transparent;color: #504de4;" onchange="$('.stat-box').hide(); $('#' + this.value).show();">
                    <option value="stat1" selected><span class="color-blue2-dark">за этот месяц</span></option>
                    <option value="stat2">за все время</option>
                </select>
            </h2>
    </div>
    <div class="stat-box" id="stat1">
        <div class="content-box shadow-small">
            <span class="font-18">Оборот компании: <?= (($month_buy['total_buy'] > 0) ? $month_buy['total_buy'] / 100 : 0) + (($month_sales['total'] > 0) ? $month_sales['total'] / 100 : 0) ?> </span><br>
            <span class="font-18 left-10">Сумма продаж: <span class="color-green1-dark"><?= ($month_sales['total'] > 0) ? $month_sales['total'] / 100 : 0 ?></span></span><br>
            <span class="font-18 left-10">Сумма покупок: <span class="color-red1-dark"><?= ($month_buy['total_buy'] > 0) ? $month_buy['total_buy'] / 100 : 0 ?></span></span><br>
        </div>
        <div class="content-box shadow-small ">
            <span class="font-18">Количество сделок: <?= $month_count_sell + $month_count_buy ?></span><br>
            <span class="font-18 left-10">Продажи: <span class="color-green1-dark"><?= $month_count_sell ?></span></span><br>
            <span class="font-18 left-10">Покупки: <span class="color-red1-dark"><?= $month_count_buy ?></span></span><br>
        </div>
    </div>
    <div class="stat-box" style="display: none" id="stat2">
        <div class="content-box shadow-small">
            <span class="font-18">Оборот компании: <?= (($total_all_month_buy['total_buy'] > 0) ? $total_all_month_buy['total_buy'] / 100 : 0) + (($total_all_month_sales['total'] > 0) ? $total_all_month_sales['total'] / 100 : 0) ?> </span><br>
            <span class="font-18 left-10">Сумма продаж: <span class="color-green1-dark"><?= ($total_all_month_sales['total'] > 0) ? $total_all_month_sales['total'] / 100 : 0 ?></span></span><br>
            <span class="font-18 left-10">Сумма покупок: <span class="color-red1-dark"><?= ($total_all_month_buy['total_buy'] > 0) ? $total_all_month_buy['total_buy'] / 100 : 0 ?></span></span><br>
        </div>
        <div class="content-box shadow-small ">
            <span class="font-18">Количество сделок: <?= $total_all_month_count_sell + $total_all_month_count_buy?></span><br>
            <span class="font-18 left-10">Продажи: <span class="color-green1-dark"><?= $total_all_month_count_sell ?></span></span><br>
            <span class="font-18 left-10">Покупки: <span class="color-red1-dark"><?= $total_all_month_count_buy ?></span></span><br>
        </div>
    </div>
    <div class="divider divider-margins"></div>
    <div class="content">
        <h2>Статистика <span class="color-blue2-dark">за период:</span></h2>
        <div class="input-style input-style-1 input-required">
        <h5>
            Начало
            <input type="date" id="stat_date_start">
            Конец
            <input type="date" id="stat_date_end">
        </h5>
        </div>
            <a href="#" onclick="load_stat_manual()" class="button button-xs round-small shadow-large bg-highlight button-full bottom-30" style="width: 100%">Показать</a>
    </div>
    <div class="demo-preloader" id="loader" style="display: none;">
        <div class="preload-spinner border-highlight"></div>
    </div>
    <div style="display: none" id="stat_manual">
        <div class="content-box shadow-small ">
            <span class="font-18" >Оборот компании: <span id="cash_all">-</span></span><br>
            <span class="font-18 left-10">Сумма продаж: <span class="color-green1-dark" id="cash_sell">-</span></span><br>
            <span class="font-18 left-10">Сумма покупок: <span class="color-red1-dark" id="cash_buy">-</span></span><br>
        </div>
        <div class="content-box shadow-small ">
            <span class="font-18" >Количество сделок: <span id="deals_all">-</span></span><br>
            <span class="font-18 left-10">Продажи: <span class="color-green1-dark" id="deals_sell">-</span></span><br>
            <span class="font-18 left-10">Покупки: <span class="color-red1-dark" id="deals_buy">-</span></span><br>
            <div class="divider-margins"></div>
            <h5 id="stat_period">Статистика за период: с - по -</h5>
        </div>

    </div>
    <div class="divider divider-margins"></div>
</div>
<div class="menu-hider"></div>
<script>
    function load_stat_manual() {

        $('#loader').show();

        let data = {
            'date_start': $('#stat_date_start').val(),
            'date_end': $('#stat_date_end').val(),
            'token_form': $('#token_form').val(),
        };

        $.ajax({
            url: base_url + 'company/company_ajax/ajax/load_company_stat',
            type: 'POST',
            dataType: 'JSON',
            data: data,
            cache: false,
            error: function () {
                vex.dialog.alert("Ошибка запроса! Повторите попытку после перезагрузки страницы...");
                setTimeout(function () {
                    reload_page();
                }, 1500)
            },
            success: function (data) {

                //вставляем хэш
                insert_csrf_hash(data);//функция берется из all_area_scripts!

                var result = validate_data_server_response(data);

                if (result) {//если все успешно
                    document.getElementById('stat_period').innerHTML = `Статистика за период: с ${data.start} по ${data.end}`;
                    document.getElementById('cash_all').innerHTML = data.data.cash_all / 100;
                    document.getElementById('cash_sell').innerHTML = data.data.cash_sell / 100;
                    document.getElementById('cash_buy').innerHTML = data.data.cash_buy / 100;
                    document.getElementById('deals_all').innerHTML = data.data.deals_all;
                    document.getElementById('deals_sell').innerHTML = data.data.deals_sell;
                    document.getElementById('deals_buy').innerHTML = data.data.deals_buy;
                    $('#stat_manual').show();

                } else {//если потерпели неудачу
                    vex.dialog.alert(data.text_message);
                }
                $('#loader').hide();
            },
        });
    }
</script>

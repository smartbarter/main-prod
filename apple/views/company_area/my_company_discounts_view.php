<div class="container-fluid">
    <div class="row new_deals">
        <div class="col-sm-12 col-md-12 col-lg-12">
            <h4>Управление скидками вашей компании</h4>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-12 col-md-6 col-lg-7 new_deals">
            <div class="widget bg_light margin-b-30 padding-15">
                
                <h5>Все ваши скидки</h5>

                <?php if(!empty($all_discounts)) { ?>
                    <small>Планируемы, действующие и прошедшие скидки</small>
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <tr>
                                <th>Скидка в %</th>
                                <th>Дата действия скидки</th>
                                <th></th>
                            </tr>
                            <?php foreach($all_discounts as $discount) { ?>

                                <tr>
                                    <td><?php echo $discount['summa_skidki']; ?></td>
                                    <?php
                                        //преобразуем дату в нормальный вид
                                        $myDateTime = DateTime::createFromFormat('Y-m-d H:i:s', $discount['end_date']);
                                        $newDateString = $myDateTime->format('d.m.Y г.');
                                    ?>
                                    <td><?php echo $newDateString; ?></td>
                                    <td><span class="btn btn-danger" onclick="delete_discount(<?php echo $discount['id_skidki']; ?>);">Удалить скидку</span></td>
                                </tr>
                                
                            <?php } ?>
                        </table>
                    </div>
                    
                <?php } else { ?>
                    <p>Вы еще не создавали скидок...</p>
                <?php }?>

            </div>
            <?php echo $this->pagination->create_links(); ?>
        </div>
        <div class="col-sm-12 col-md-6 col-lg-5">
            <div class="widget bg_light margin-b-30 padding-15">
                <h5>Создать скидку</h5>
                
                <form id="create_new_discount">
                    <div class="form-group">
                        <div class="input-group">
                            <input type="number" class="form-control" id="skidka" min="1" max="100" placeholder="Размер скидки" required>
                            <div class="input-group-addon">%</div>
                        </div>
                        <div id="error_skidka" class="error"></div>
                    </div>
                    <p>Какого числа будет действовать скидка?</p>
                    <div class="form-group">
                        <div class="input-group">
                            <input type="text" class="form-control" id="date" placeholder="Выберите дату" required>
                            <div class="input-group-addon"><i class="fa fa-calendar" aria-hidden="true"></i></div>
                        </div>
                        <div id="error_date" class="error"></div>
                    </div>
                    <button type="submit" class="btn btn-success btn-block">Создать скидку</button>
                </form>

                <hr>
                <p>Данную скидку вы предоставляете самостоятельно при расчете с покупателем, система не учитывает вашу скидку при оплате!</p>
            
            </div>
        </div>
    </div>
</div>
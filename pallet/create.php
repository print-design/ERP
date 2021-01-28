<?php
include '../include/topscripts.php';

// Авторизация
if(!IsInRole(array('admin', 'dev', 'technologist', 'storekeeper'))) {
    header('Location: '.APPLICATION.'/unauthorized.php');
}

// Валидация формы

// Обработка отправки формы

// Получение данных
$id = 0;
$date = date("d.m.Y");
?>
<!DOCTYPE html>
<html>
    <head>
        <?php
        include '../include/head.php';
        ?>
    </head>
    <body>
        <?php
        include '../include/header.php';
        ?>
        <div class="container-fluid" style="padding-left: 40px;">
            <?php
            if(!empty($error_message)) {
                echo "<div class='alert alert-danger>$error_message</div>";
            }
            ?>
            <div class="backlink" style="margin-bottom: 56px;">
                <a href="<?=APPLICATION ?>/pallet/"><i class="fas fa-chevron-left"></i>&nbsp;Назад</a>
            </div>
            <h1 style="font-size: 32px; line-height: 48px; font-weight: 600; margin-bottom: 20px;">Новый паллет</h1>
            <h2 style="font-size: 24px; line-height: 32px; font-weight: 600; margin-bottom: 20px;">Паллет № <?=$id ?> от <?=$date ?></h2>
            <div style="width: 423px;">
                <form method="post">
                    <input type="hidden" />
                    <div class="form-group">
                        <label for="supplier_id">Поставщик</label>
                        <select id="supplier_id" name="supplier_id" class="form-control" required="required">
                            <option value="">Выберите поставщика</option>
                        </select>
                        <div class="invalid-feedback">Поставщик обязательно</div>
                    </div>
                    <div class="form-group">
                        <label for="id_from_supplier">ID паллета от поставщика</label>
                        <input type="text" id="id_from_supplier" name="id_from_supplier" class="form-control" placeholder="Введите ID" required="required" />
                        <div class="invalid-feedback">ID паллета от поставщика обязательно</div>
                    </div>
                    <div class="form-group">
                        <label for="film_brand_id">Марка пленки</label>
                        <select id="film_brand_id" name="film_brand_id" class="form-control" required="required">
                            <option value="">Выберите марку</option>
                        </select>
                        <div class="invalid-feedback">Марка пленки обязательно</div>
                    </div>
                    <div class="row">
                        <div class="col-6 form-group">
                            <label for="width">Ширина</label>
                            <input type="text" id="width" name="width" class="form-control int-only" placeholder="Введите ширину" required="required" />
                            <div class="invalid-feedback">Ширина обязательно</div>
                        </div>
                        <div class="col-6 form-group">
                            <label for="thickness">Толщина</label>
                            <select id="thickness" name="thickness" class="form-control" required="required">
                                <option value="">Выберите толщину</option>
                                <option value="1">1</option>
                                <option value="1">2</option>
                                <option value="1">3</option>
                                <option value="1">4</option>
                                <option value="1">5</option>
                                <option value="1">6</option>
                                <option value="1">7</option>
                                <option value="1">8</option>
                                <option value="1">9</option>
                                <option value="1">10</option>
                            </select>
                            <div class="invalid-feedback">Толщина обязательно</div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-6 form-group">
                            <label for="length">Длина</label>
                            <input type="text" id="length" name="length" class="form-control float-only" placeholder="Введите длину" required="required" />
                            <div class="invalid-feedback">Длина обязательно</div>
                        </div>
                        <div class="col-6 form-group">
                            <label for="net_weight">Масса нетто</label>
                            <input type="text" id="net_weight" name="net_weight" class="form-control float-only" placeholder="Введите массу нетто" required="required" />
                            <div class="invalid-feedback">Масса нетто обязательно</div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-6 form-group">
                            <label for="rolls_number">Количество рулонов</label>
                            <select id="rolls_number" name="rolls_number" class="form-control" required="required">
                                <option value="">Выберите количество</option>
                                <option value="1">1</option>
                                <option value="1">2</option>
                                <option value="1">3</option>
                                <option value="1">4</option>
                                <option value="1">5</option>
                                <option value="1">6</option>
                            </select>
                            <div class="invalid-feedback">Количество рулонов обязательно</div>
                        </div>
                        <div class="col-6 form-group">
                            <label for="cell">Ячейка на складе</label>
                            <input type="text" id="cell" name="cell" class="form-control" placeholder="Введите ячейку" required="required" />
                            <div class="invalid-feedback">Ячейка на складе обязательно</div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="manager_id">Менеджер</label>
                        <select id="manager_id" name="manager_id" class="form-control" required="required" style="background-color: #8B90A0; color: white;">
                            <option value="">ВЫБРАТЬ СТАТУС</option>
                        </select>
                    </div>
                </form>
            </div>
        </div>
        <?php
        include '../include/footer.php';
        ?>
    </body>
</html>
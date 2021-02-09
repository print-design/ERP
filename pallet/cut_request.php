<?php
include '../include/topscripts.php';

// Авторизация
if(!IsInRole(array('admin', 'dev', 'technologist', 'storekeeper'))) {
    header('Location: '.APPLICATION.'/unauthorized.php');
}

// Если не задано значение inner_id, перенаправляем на список
if(empty(filter_input(INPUT_GET, 'inner_id'))) {
    header('Location: '.APPLICATION.'/pallet/');
}

// Получение данных
$inner_id = filter_input(INPUT_GET, 'inner_id');
$sql = "select p.inner_id, p.date, p.storekeeper_id, p.supplier_id, p.id_from_supplier, p.film_brand_id, p.width, p.thickness, p.length, "
        . "p.net_weight, p.rolls_number, p.cell, p.manager_id, p.status_id, p.comment "
        . "from pallet p "
        . "where p.inner_id=$inner_id";

$row = (new Fetcher($sql))->Fetch();
$inner_id = $row['inner_id'];
$date = $row['date'];
$storekeeper_id = $row['storekeeper_id'];
$supplier_id = $row['supplier_id'];
$id_from_supplier = $row['id_from_supplier'];
$film_brand_id = $row['film_brand_id'];
$width = $row['width'];
$thickness = $row['thickness'];
$length = $row['length'];
$net_weight = $row['net_weight'];
$rolls_number = $row['rolls_number'];
$cell = $row['cell'];
$manager_id = $row['manager_id'];
$status_id = $row['status_id'];
$comment = $row['comment'];
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
            <div class="row">
                <div class="col-6">
                    <h1>Заявка на раскрой паллета</h1>
                    <form method="post">
                        <div class="form-group">
                            <label for="length">Длина</label>
                            <input type="text" class="form-control" style="width: 200px;" id="length" name="length" value="<?= filter_input(INPUT_POST, 'length') ?>" />
                            <div class="invalid-feedback">Длина обязательно и не больше, чем длина паллета.</div>
                        </div>
                        <input type="hidden" class="stream_number" value="1"/>
                        <p>Первый ручей</p>
                        <div class="form-group">
                            <label for="width1">Ширина</label>
                            <input type="text" class="form-control" style="width: 200px;" id="length" name="length" value="<?= filter_input(INPUT_POST, 'length1') ?>" />
                            <div class="invalid-feedback">Ширина обязательно.</div>
                        </div>
                        <div class="form-group">
                            <label for="request1">Под какой заказ режем?</label>
                            <textarea id="request1" name="request1" class="form-control" rows="5" style="width: 500px;"><?= filter_input(INPUT_POST, 'request1') ?></textarea>
                        </div>
                        <button class="btn btn-link" style="margin-bottom: 50px;"><i class="fas fa-plus" style="font-size: 10px; vertical-align: top; margin-top: 8px;"></i>&nbsp;Добавить ручей</button>
                        <div class="form-group">
                            <label for="remainder">Остаток</label>
                            <input type="text" class="form-control" style="width: 200px;" id="remainder" name="remainder" value="<?= filter_input(INPUT_POST, 'remainder') ?>" />
                        </div>
                        <button class="btn btn-dark" id="cut-request-submit" name="cut-request-submit" style="margin-top: 20px; padding-top: 14px; padding-bottom: 14px; padding-left: 50px; padding-right: 50px;">ОТПРАВИТЬ НА РАСКРОЙ</button>
                    </form>                    
                </div>
                <div class="col-6">
                    <h1>Паллет №<?=$inner_id ?></h1>
                </div>
            </div>
        </div>
        <?php
        include '../include/footer.php';
        ?>
    </body>
</html>
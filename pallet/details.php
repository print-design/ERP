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

// Валидация формы
define('ISINVALID', ' is-invalid');
$form_valid = true;
$error_message = '';

$status_id_valid = '';

// Обработка отправки формы
if(null !== filter_input(INPUT_POST, 'change-status-submit')) {
    $status_id = filter_input(INPUT_POST, 'status_id');
    if(empty($status_id)) {
        $status_id_valid = ISINVALID;
        $form_valid = false;
    }
    
    if($form_valid) {
        $id = filter_input(INPUT_POST, 'id');
        $status_id = filter_input(INPUT_POST, 'status_id');
        $length = filter_input(INPUT_POST, 'length');
        $net_weight = filter_input(INPUT_POST, 'net_weight');
        $rolls_number = filter_input(INPUT_POST, 'rolls_number');
        $cell = filter_input(INPUT_POST, 'cell');
        $comment = filter_input(INPUT_POST, 'comment');
        
        // Получаем имеющиеся данные и проверяем, совпадают ли они с новыми данными
        $sql = "select length, net_weight, rolls_number, cell, comment, "
                . "(select status_id from pallet_status_history where pallet_id=$id order by is limit 1) status_id "
                . "from pallet where id=$id";
        $row = (new Fetcher($sql))->Fetch();
        
        if(!$row || $row['status_id'] != $status_id) {
            $date = date('Y-m-d');
            $inner_id = filter_input(INPUT_POST, 'inner_id');
            $user_id = GetUserId();
            
            $error_message = (new Executer("insert into pallet_status_history (pallet_id, date, status_id, user_id) values ($id, '$date', $status_id, $user_id)"))->error;
        }
        
        if(empty($error_message)) {
            if(!$row || $row['length'] != $length || $row['net_weight'] != $net_weight || $row['rolls_number'] != $rolls_number || $row['cell'] != $cell || $row['comment'] != $comment) {
                $comment = addslashes($comment);
                $error_message = (new Executer("update pallet set length=$length, net_weight=$net_weight, rolls_number=$rolls_number, cell='$cell', comment='$comment' where id=$id"))->error;
            }
        }
        
        if(empty($error_message)) {
            header('Location: '.APPLICATION.'/pallet/');
        }
    }
}

// Получение данных
$inner_id = filter_input(INPUT_GET, 'inner_id');
$sql = "select p.id, p.inner_id, p.date, p.storekeeper_id, u.last_name, u.first_name, p.supplier_id, p.id_from_supplier, p.film_brand_id, p.width, p.thickness, p.length, "
        . "p.net_weight, p.rolls_number, p.cell, "
        . "(select psh.status_id from pallet_status_history psh where psh.pallet_id = p.id order by psh.id desc limit 0, 1) status_id, "
        . "p.comment "
        . "from pallet p inner join user u on p.storekeeper_id = u.id "
        . "where p.inner_id=$inner_id";

$row = (new Fetcher($sql))->Fetch();
$id = $row['id'];
$inner_id = $row['inner_id'];
$date = $row['date'];
$storekeeper_id = $row['storekeeper_id'];
$storekeeper = $row['last_name'].' '.$row['first_name'];
$supplier_id = $row['supplier_id'];
$id_from_supplier = $row['id_from_supplier'];
$film_brand_id = $row['film_brand_id'];
$width = $row['width'];
$thickness = $row['thickness'];
$length = $row['length'];
$net_weight = $row['net_weight'];
$rolls_number = $row['rolls_number'];
$cell = $row['cell'];

$status_id = filter_input(INPUT_POST, 'status_id');
if(empty($status_id)) $status_id = $row['status_id'];

$comment = $row['comment'];

// СТАТУС "СВОБОДНЫЙ" ДЛЯ ПАЛЛЕТА
$free_status_id = 1;

// СТАТУС "СРАБОТАННЫЙ" ДЛЯ ПАЛЛЕТА
$utilized_status_id = 2;
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
            <h1 style="font-size: 24px; line-height: 32px; fon24pxt-weight: 600; margin-bottom: 20px;">Информация о паллете № <?=$inner_id ?> от <?= (DateTime::createFromFormat('Y-m-d', $date))->format('d.m.Y') ?></h1>
            <h2 style="font-size: 24px; line-height: 32px; font-weight: 600; margin-bottom: 20px;">ID <?=$id_from_supplier ?></h2>
            <form method="post">
                <div style="width: 423px;">
                    <input type="hidden" id="id" name="id" value="<?=$id ?>" />
                    <input type="hidden" id="inner_id" name="inner_id" value="<?= filter_input(INPUT_GET, 'inner_id') ?>" />
                    <input type="hidden" id="date" name="date" value="<?= $date ?>" />
                    <input type="hidden" id="storekeeper_id" name="storekeeper_id" value="<?= $storekeeper_id ?>" />
                    <input type="hidden" id="storekeeper" name="storekeeper" value="<?= $storekeeper ?>" />
                    <input type="hidden" id="supplier_id" name="supplier_id" value="<?=$supplier_id ?>" />
                    <input type="hidden" id="id_from_supplier" name="id_from_supplier" value="<?=$id_from_supplier ?>" />
                    <input type="hidden" id="film_brand_id" name="film_brand_id" value="<?=$film_brand_id ?>" />
                    <input type="hidden" id="width" name="width" value="<?=$width ?>" />
                    <input type="hidden" id="thickness" name="thickness" value="<?=$thickness ?>" />
                    <input type="hidden" id="length" name="length" value="<?=$length ?>" />
                    <input type="hidden" id="net_weight" name="net_weight" value="<?=$net_weight ?>" />
                    <input type="hidden" id="rolls_number" name="rolls_number" value="<?=$rolls_number ?>" />
                    <input type="hidden" id="cell" name="cell" value="<?=$cell ?>" />
                    <div class="form-group">
                        <label for="storekeeper">Принят кладовщиком</label>
                        <p id="storekeeper"><?=$storekeeper ?></p>
                    </div>
                    <div class="form-group">
                        <label for="supplier_id">Поставщик</label>
                        <select id="supplier_id" name="supplier_id" class="form-control" disabled="disabled">
                            <option value="">Выберите поставщика</option>
                            <?php
                            $suppliers = (new Grabber("select id, name from supplier order by name"))->result;
                            foreach ($suppliers as $supplier) {
                                $id = $supplier['id'];
                                $name = $supplier['name'];
                                $selected = '';
                                if($supplier_id == $supplier['id']) $selected = " selected='selected'";
                                echo "<option value='$id'$selected>$name</option>";
                            }
                            ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="id_from_supplier">ID паллета от поставщика</label>
                        <input type="text" id="id_from_supplier" name="id_from_supplier" value="<?= $id_from_supplier ?>" class="form-control" placeholder="Введите ID" disabled="disabled" />
                    </div>
                    <div class="form-group">
                        <label for="film_brand_id">Марка пленки</label>
                        <select id="film_brand_id" name="film_brand_id" class="form-control" disabled="disabled">
                            <option value="">Выберите марку</option>
                            <?php
                            $film_brands = (new Grabber("select id, name from film_brand where supplier_id = $supplier_id"))->result;
                            foreach ($film_brands as $film_brand) {
                                $id = $film_brand['id'];
                                $name = $film_brand['name'];
                                $selected = '';
                                if($film_brand_id == $film_brand['id']) $selected = " selected='selected'";
                                echo "<option value='$id'$selected>$name</option>";
                            }
                            ?>
                        </select>
                    </div>
                    <div class="row">
                        <div class="col-6 form-group">
                            <label for="width">Ширина</label>
                            <input type="text" id="width" name="width" value="<?= $width ?>" class="form-control int-only" placeholder="Введите ширину" disabled="disabled" />
                        </div>
                        <div class="col-6 form-group">
                            <label for="thickness">Толщина</label>
                            <select id="thickness" name="thickness" class="form-control" disabled="disabled">
                                <option value="">Выберите толщину</option>
                                <?php
                                $film_brand_variations = (new Grabber("select thickness from film_brand_variation where film_brand_id = $film_brand_id order by thickness"))->result;
                                foreach ($film_brand_variations as $film_brand_variation) {
                                    $selected = '';
                                    if($thickness == $film_brand_variation['thickness']) $selected = " selected='selected'";
                                    echo "<option value='$thickness'$selected>$thickness</option>";
                                }
                                ?>
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-6 form-group">
                            <label for="length">Длина</label>
                            <input type="text" id="length" name="length" value="<?= $length ?>" class="form-control int-only" placeholder="Введите длину" />
                        </div>
                        <div class="col-6 form-group">
                            <label for="net_weight">Масса нетто</label>
                            <input type="text" id="net_weight" name="net_weight" value="<?= $net_weight ?>" class="form-control int-only" placeholder="Введите массу нетто" />
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-6 form-group">
                            <label for="rolls_number">Количество рулонов</label>
                            <select id="rolls_number" name="rolls_number" class="form-control">
                                <option value="">Выберите количество</option>
                                <?php
                                for($i=1; $i<7; $i++) {
                                    $selected = '';
                                    if($rolls_number == $i) $selected = " selected='selected'";
                                    echo "<option value='$i'$selected>$i</option>";
                                }
                                ?>
                            </select>
                        </div>
                        <div class="col-6 form-group">
                            <label for="cell">Ячейка на складе</label>
                            <input type="text" id="cell" name="cell" value="<?= $cell ?>" class="form-control" placeholder="Введите ячейку" />
                        </div>
                    </div>
                    <div class="form-group d-none">
                        <label for="manager_id">Менеджер</label>
                        <select id="manager_id" name="manager_id" class="form-control" disabled="disabled">
                            <option value="">Выберите менеджера</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="status_id">Статус</label>
                        <select id="status_id" name="status_id" class="form-control" required="required">
                            <?php
                            $statuses = (new Grabber("select s.id, s.name from pallet_status s order by s.name"))->result;
                            foreach ($statuses as $status) {
                                if(!(empty($status_id) && $status['id'] == $utilized_status_id)) { // Если статуса нет, то нельзя сразу поставить "Сработанный"
                                    $id = $status['id'];
                                    $name = $status['name'];
                                    $selected = '';
                                    if(empty($status_id)) $status_id = $free_status_id; // По умолчанию ставим статус "Свободный"
                                    if($status_id == $status['id']) $selected = " selected='selected'";
                                    echo "<option value='$id'$selected>$name</option>";
                                }
                            }
                            ?>
                        </select>
                        <div class="invalid-feedback">Статус обязательно</div>
                    </div>
                    <div class="form-group">
                        <label for="comment">Комментарий</label>
                        <textarea id="comment" name="comment" rows="4" class="form-control"><?= htmlentities($comment) ?></textarea>
                    </div>
                </div>
                <div class="form-inline" style="margin-top: 30px;">
                    <button type="submit" id="change-status-submit" name="change-status-submit" class="btn btn-dark" style="padding-left: 80px; padding-right: 80px; margin-right: 62px; padding-top: 14px; padding-bottom: 14px;">Сменить статус</button>
                    <button type="submit" formaction="<?=APPLICATION ?>/pallet/sticker.php" formtarget="output" id="sticker-submit" name="sticker-submit" class="btn btn-outline-dark" style="padding-top: 5px; padding-bottom: 5px; padding-left: 50px; padding-right: 50px;">Распечатать<br />стикер</button>
                </div>
            </form>
        </div>
        <?php
        include '../include/footer.php';
        ?>
    </body>
</html>
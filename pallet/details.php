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
    
    $id = filter_input(INPUT_POST, 'id');
    $date = date('Y-m-d');
    $inner_id = filter_input(INPUT_POST, 'inner_id');
    $status_id = filter_input(INPUT_POST, 'status_id');
    $user_id = GetUserId();
    
    $error_message = (new Executer("insert into pallet_status_history (pallet_id, date, status_id, user_id) values ($id, '$date', $status_id, $user_id)"))->error;
    
    if(empty($error_message)) {
        header('Location: '.APPLICATION.'/pallet/');
    }
}

// Получение данных
$inner_id = filter_input(INPUT_GET, 'inner_id');
$sql = "select p.id, p.inner_id, p.date, p.storekeeper_id, u.last_name, u.first_name, p.supplier_id, p.id_from_supplier, p.film_brand_id, p.width, p.thickness, p.length, "
        . "p.net_weight, p.rolls_number, p.cell, "
        . "(select psh.status_id from pallet_status_history psh where pallet_id = p.id order by date desc limit 0, 1) status_id, "
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

// СТАТУС "СРАБОТАННЫЙ" ДЛЯ ПАЛЛЕТА
$utilized_status_id = 4; 
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
                <input type="hidden" id="id" name="id" value="<?=$id ?>" />
                <input type="hidden" id="inner_id" name="inner_id" value="<?= filter_input(INPUT_GET, 'inner_id') ?>" />
                <div style="width: 423px;">
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
                            <input type="text" id="length" name="length" value="<?= $length ?>" class="form-control int-only" placeholder="Введите длину" disabled="disabled" />
                        </div>
                        <div class="col-6 form-group">
                            <label for="net_weight">Масса нетто</label>
                            <input type="text" id="net_weight" name="net_weight" value="<?= $net_weight ?>" class="form-control int-only" placeholder="Введите массу нетто" disabled="disabled" />
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-6 form-group">
                            <label for="rolls_number">Количество рулонов</label>
                            <select id="rolls_number" name="rolls_number" class="form-control" disabled="disabled">
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
                            <input type="text" id="cell" name="cell" value="<?= $cell ?>" class="form-control" placeholder="Введите ячейку" disabled="disabled" />
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="manager_id">Менеджер</label>
                        <select id="manager_id" name="manager_id" class="form-control" disabled="disabled">
                            <option value="">Выберите менеджера</option>
                            <?php
                            $managers = (new Grabber("select u.id, u.first_name, u.last_name from user u inner join role r on u.role_id = r.id where r.name in ('manager', 'seniormanager') order by u.last_name"))->result;
                            foreach ($managers as $manager) {
                                $id = $manager['id'];
                                $first_name = $manager['first_name'];
                                $last_name = $manager['last_name'];
                                $selected = '';
                                if($manager_id == $manager['id']) $selected = " selected='selected'";
                                echo "<option value='$id'$selected>$last_name $first_name</option>";
                            }
                            ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="status_id">Статус</label>
                        <select id="status_id" name="status_id" class="form-control" required="required">
                            <option value="">ВЫБРАТЬ СТАТУС</option>
                            <?php
                            $statuses = (new Grabber("select s.id, s.name from pallet_status s where s.id <> $utilized_status_id order by s.name"))->result;
                            foreach ($statuses as $status) {
                                $id = $status['id'];
                                $name = $status['name'];
                                $selected = '';
                                if($status_id == $status['id']) $selected = " selected='selected'";
                                echo "<option value='$id'$selected>$name</option>";
                            }
                            ?>
                        </select>
                        <div class="invalid-feedback">Статус обязательно</div>
                    </div>
                    <div class="form-group">
                        <label for="comment">Комментарий</label>
                        <textarea id="comment" name="comment" rows="4" class="form-control" disabled="disabled"><?= htmlentities($comment) ?></textarea>
                    </div>
                    <button type="submit" class="btn btn-dark" id="change-status-submit" name="change-status-submit" style="padding-top: 14px; padding-bottom: 14px; padding-left: 30px; padding-right: 30px; margin-top: 30px;">Сменить статус</button>
                </div>
            </form>
        </div>
        <?php
        include '../include/footer.php';
        ?>
    </body>
</html>
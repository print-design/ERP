<?php
include '../include/topscripts.php';

// Авторизация
if(!IsInRole(array('admin', 'dev', 'technologist', 'storekeeper'))) {
    header('Location: '.APPLICATION.'/unauthorized.php');
}

// Валидация формы
define('ISINVALID', ' is-invalid');
$form_valid = true;
$error_message = '';

$supplier_id_valid = '';
$id_from_supplier_valid = '';
$film_brand_id_valid = '';
$width_valid = '';
$thickness_valid = '';
$length_valid = '';
$net_weight_valid = '';
$cell_valid = '';
$status_id_valid = '';

// Обработка отправки формы
if(null !== filter_input(INPUT_POST, 'create-roll-submit')) {
    $supplier_id = filter_input(INPUT_POST, 'supplier_id');
    if(empty($supplier_id)) {
        $supplier_id_valid = ISINVALID;
        $form_valid = false;
    }
    
    $id_from_supplier = filter_input(INPUT_POST, 'id_from_supplier');
    if(empty($id_from_supplier)) {
        $id_from_supplier_valid = ISINVALID;
        $form_valid = false;
    }
    
    $film_brand_id = filter_input(INPUT_POST, 'film_brand_id');
    if(empty($film_brand_id)) {
        $film_brand_id = ISINVALID;
        $form_valid = false;
    }
    
    $width = filter_input(INPUT_POST, 'width');
    if(empty($width)) {
        $width_valid = ISINVALID;
        $form_valid = false;
    }
    
    if(intval($width) < 50 || intval($width) > 1600) {
        $width_valid = ISINVALID;
        $form_valid = false;
    }
    
    $thickness = filter_input(INPUT_POST, 'thickness');
    if(empty($thickness)) {
        $thickness_valid = ISINVALID;
        $form_valid = false;
    }
    
    $length = filter_input(INPUT_POST, 'length');
    if(empty($length)) {
        $length_valid = ISINVALID;
        $form_valid = false;
    }
    
    $net_weight = filter_input(INPUT_POST, 'net_weight');
    if(empty($net_weight)) {
        $net_weight_valid = ISINVALID;
        $form_valid = false;
    }
    
    $cell = filter_input(INPUT_POST, 'cell');
    if(empty($cell)) {
        $cell_valid = ISINVALID;
        $form_valid = false;
    }
    
    // Выбор менеджера пока необязательный.
    $manager_id = filter_input(INPUT_POST, 'manager_id');
    if(empty($manager_id)) {
        $manager_id = "NULL";
    }

    // Статус пока не обязательно.
    $status_id = filter_input(INPUT_POST, 'status_id');
    if(empty($status_id)) {
        $status_id = "NULL";
    }
    
    $comment = addslashes(filter_input(INPUT_POST, 'comment'));
    $inner_id = filter_input(INPUT_POST, 'inner_id');
    $date = filter_input(INPUT_POST, 'date');
    $storekeeper_id = filter_input(INPUT_POST, 'storekeeper_id');
    
    if($form_valid) {
        $sql = "insert into roll (supplier_id, id_from_supplier, film_brand_id, width, thickness, length, net_weight, cell, comment, inner_id, date, storekeeper_id) "
                . "values ($supplier_id, '$id_from_supplier', $film_brand_id, $width, $thickness, $length, $net_weight, '$cell', '$comment', '$inner_id', '$date', '$storekeeper_id')";
        $executer = new Executer($sql);
        $error_message = $executer->error;
        $roll_id = $executer->insert_id;
        $user_id = GetUserId();
        
        if(empty($error_message)) {
            $error_message = (new Executer("delete from new_roll_id where id=$inner_id"))->error;
            $error_message = (new Executer("insert into roll_status_history (roll_id, date, status_id, user_id) values ($roll_id, '$date', $status_id, $user_id)"))->error;
            
            if(empty($error_message)) {
                header('Location: '.APPLICATION."/roll/");
            }
        }
    }
}

// Получение данных
if(empty($error_message)) {
    $inner_id = 0;
    $row = (new Fetcher("select id from new_roll_id union select inner_id from roll order by id desc limit 1"))->Fetch();
    if(!empty($row)) {
        $inner_id = intval($row['id']);
    }
    $inner_id++;
    
    $error_message = (new Executer("insert into new_roll_id(id) value ($inner_id)"))->error;
}
else {
    $inner_id = filter_input(INPUT_POST, 'inner_id');
}
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
                echo "<div class='alert alert-danger'>$error_message</div>";
            }
            ?>
            <div class="backlink" style="margin-bottom: 56px;">
                <a href="<?=APPLICATION ?>/roll/"><i class="fas fa-chevron-left"></i>&nbsp;Назад</a>
            </div>
            <h1 style="font-size: 32px; line-height: 48px; font-weight: 600; margin-bottom: 20px;">Новый рулон</h1>
            <h2 style="font-size: 24px; line-height: 32px; font-weight: 600; margin-bottom: 20px;">Рулон № <?=$inner_id ?> от <?= date("d.m.Y") ?></h2>
            <form method="post">
                <div style="width: 423px;">
                    <input type="hidden" id="inner_id" name="inner_id" value="<?=$inner_id ?>" />
                    <input type="hidden" id="date" name="date" value="<?= date("Y-m-d") ?>" />
                    <input type="hidden" id="storekeeper_id" name="storekeeper_id" value="<?= GetUserId() ?>" />
                    <div class="form-group">
                        <label for="supplier_id">Поставщик</label>
                        <select id="supplier_id" name="supplier_id" class="form-control" required="required">
                            <option value="">Выберите поставщика</option>
                            <?php
                            $suppliers = (new Grabber("select id, name from supplier order by name"))->result;
                            foreach ($suppliers as $supplier) {
                                $id = $supplier['id'];
                                $name = $supplier['name'];
                                $selected = '';
                                if(filter_input(INPUT_POST, 'supplier_id') == $supplier['id']) $selected = " selected='selected'";
                                echo "<option value='$id'$selected>$name</option>";
                            }
                            ?>
                        </select>
                        <div class="invalid-feedback">Поставщик обязательно</div>
                    </div>
                    <div class="form-group">
                        <label for="id_from_supplier">ID рулона от поставщика</label>
                        <input type="text" id="id_from_supplier" name="id_from_supplier" value="<?= filter_input(INPUT_POST, 'id_from_supplier') ?>" class="form-control" placeholder="Введите ID" required="required" />
                        <div class="invalid-feedback">ID рулона от поставщика обязательно</div>
                    </div>
                    <div class="form-group">
                        <label for="film_brand_id">Марка пленки</label>
                        <select id="film_brand_id" name="film_brand_id" class="form-control" required="required">
                            <option value="">Выберите марку</option>
                            <?php
                            if(null !== filter_input(INPUT_POST, 'supplier_id')) {
                                $supplier_id = filter_input(INPUT_POST, 'supplier_id');
                                $film_brands = (new Grabber("select id, name from film_brand where supplier_id = $supplier_id"))->result;
                                foreach ($film_brands as $film_brand) {
                                    $id = $film_brand['id'];
                                    $name = $film_brand['name'];
                                    $selected = '';
                                    if(filter_input(INPUT_POST, 'film_brand_id') == $film_brand['id']) $selected = " selected='selected'";
                                    echo "<option value='$id'$selected>$name</option>";
                                }
                            }
                            ?>
                        </select>
                        <div class="invalid-feedback">Марка пленки обязательно</div>
                    </div>
                    <div class="row">
                        <div class="col-6 form-group">
                            <label for="width" id="label_width">Ширина</label>
                            <input type="text" id="width" name="width" value="<?= filter_input(INPUT_POST, 'width') ?>" class="form-control int-only<?=$width_valid ?>" placeholder="Введите ширину" required="required" />
                            <div class="invalid-feedback">От 50 до 1600</div>
                        </div>
                        <div class="col-6 form-group">
                            <label for="thickness" id="label_thickness">Толщина</label>
                            <select id="thickness" name="thickness" class="form-control" required="required">
                                <option value="">Выберите толщину</option>
                                <?php
                                if(null !== filter_input(INPUT_POST, 'film_brand_id')) {
                                    $film_brand_id = filter_input(INPUT_POST, 'film_brand_id');
                                    $film_brand_variations = (new Grabber("select thickness from film_brand_variation where film_brand_id = $film_brand_id order by thickness"))->result;
                                    foreach ($film_brand_variations as $film_brand_variation) {
                                        $thickness = $film_brand_variation['thickness'];
                                        $selected = '';
                                        if(filter_input(INPUT_POST, 'thickness') == $film_brand_variation['thickness']) $selected = " selected='selected'";
                                        echo "<option value='$thickness'$selected>$thickness</option>";
                                    }
                                }
                                ?>
                            </select>
                            <div class="invalid-feedback">Толщина обязательно</div>
                        </div>
                    </div>
                    <?php
                    $checked = '';
                    if(filter_input(INPUT_POST, 'caclulate_by_diameter') == 'on') {
                        $checked = " checked='checked'";
                    }
                    ?>
                    <div class="form-group">
                        <input type="checkbox" id="caclulate_by_diameter" name="caclulate_by_diameter"<?=$checked ?> />
                        <label class="form-check-label" for="caclulate_by_diameter">Рассчитать по радиусу</label>
                    </div>
                    <div class="row" id="controls-for-calculation">
                        <div class="col-6 form-group">
                            <label for="shpulya">Шпуля</label>
                            <select id="shpulya" name="shpulya" class="form-control">
                                <?php
                                $shpulya_selected_76 = '';
                                $shpulya_selected_152 = '';
                                $shpulya = filter_input(INPUT_POST, 'shpulya');
                                if($shpulya == 76) $shpulya_selected_76 = " selected='selected'";
                                if($shpulya == 152) $shpulya_selected_152 = " selected='selected'";
                                ?>
                                <option value="">Выберите шпулю</option>
                                <option value="76"<?=$shpulya_selected_76 ?>">76</option>
                                <option value="152"<?=$shpulya_selected_152 ?>">152</option>
                            </select>
                        </div>
                        <div class="col-6 form-group">
                            <label for="diameter">Расчет по радиусу (от вала)</label>
                            <input type="text" id="diameter" name="diameter" class="form-control int-only" value="<?= filter_input(INPUT_POST, 'diameter') ?>" />
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-6 form-group">
                            <label for="length">Длина</label>
                            <input type="text" id="length" name="length" value="<?= filter_input(INPUT_POST, 'length') ?>" class="form-control int-only<?=$length_valid ?>" placeholder="Введите длину" required="required" />
                            <div class="invalid-feedback">Длина обязательно</div>
                        </div>
                        <div class="col-6 form-group">
                            <label for="net_weight">Масса нетто</label>
                            <input type="text" id="net_weight" name="net_weight" value="<?= filter_input(INPUT_POST, 'net_weight') ?>" class="form-control int-only<?=$net_weight_valid ?>" placeholder="Введите массу нетто" required="required" />
                            <div class="invalid-feedback">Масса нетто обязательно</div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-6 form-group"></div>
                        <div class="col-6 form-group">
                            <label for="cell">Ячейка на складе</label>
                            <input type="text" id="cell" name="cell" value="<?= filter_input(INPUT_POST, 'cell') ?>" class="form-control" placeholder="Введите ячейку" required="required" />
                            <div class="invalid-feedback">Ячейка на складе обязательно</div>
                        </div>
                    </div>
                    <div class="form-group d-none">
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
                                if(filter_input(INPUT_POST, 'manager_id') == $manager['id']) $selected = " selected='selected'";
                                echo "<option value='$id'$selected>$last_name $first_name</option>";
                            }
                            ?>
                        </select>
                        <div class="invalid-feedback">Менеджер обязательно</div>
                    </div>
                    <input type="hidden" id="status_id" name="status_id" value="1" />
                    <div class="form-group d-none">
                        <label for="status_id_">Статус</label>
                        <select id="status_id_" name="status_id_" class="form-control" disabled="disabled">
                            <?php
                            $statuses = (new Grabber("select s.id, s.name from roll_status s order by s.name"))->result;
                            foreach ($statuses as $status) {
                                $id = $status['id'];
                                $name = $status['name'];
                                $selected = '';
                                if($status['id'] == 1) $selected = " selected='selected'";
                                echo "<option value='$id'$selected>$name</option>";
                            }
                            ?>
                        </select>
                        <div class="invalid-feedback">Статус обязательно</div>
                    </div>
                    <div class="form-group">
                        <label for="comment">Комментарий</label>
                        <textarea id="comment" name="comment" rows="4" class="form-control"><?= htmlentities(filter_input(INPUT_POST, 'comment')) ?></textarea>
                    </div>
                </div>
                <div class="form-inline" style="margin-top: 30px;">
                    <button type="submit" id="create-roll-submit" name="create-roll-submit" class="btn btn-dark" style="padding-left: 80px; padding-right: 80px; margin-right: 62px; padding-top: 14px; padding-bottom: 14px;">СОЗДАТЬ РУЛОН</button>
                    <button type="submit" formaction="<?=APPLICATION ?>/roll/sticker.php" formtarget="output" id="sticker-submit" name="sticker-submit" class="btn btn-outline-dark" style="padding-top: 5px; padding-bottom: 5px; padding-left: 50px; padding-right: 50px;">Распечатать<br />стикер</button>
                </div>
            </form>
        </div>
        <?php
        include '../include/footer.php';
        ?>
        <script>
            $('#supplier_id').change(function(){
                if($(this).val() == "") {
                    $('#film_brand_id').html("<option id=''>Выберите марку</option>");
                }
                else {
                    $.ajax({ url: "../ajax/film_brand.php?supplier_id=" + $(this).val() })
                            .done(function(data) {
                                $('#film_brand_id').html(data);
                            })
                            .fail(function() {
                                alert('Ошибка при выборе поставщика');
                            });
                }
            });
            
            $('#film_brand_id').change(function(){
                if($(this).val() == "") {
                    $('#thickness').html("<option id=''>Выберите толщину</option>");
                }
                else {
                    $.ajax({ url: "../ajax/thickness.php?film_brand_id=" + $(this).val() })
                            .done(function(data) {
                                $('#thickness').html(data);
                            })
                            .fail(function() {
                                alert('Ошибка при выборе марки пленки');
                            });
                }
            });
            
            if($('#caclulate_by_diameter').prop('checked') == true) {
                $('#controls-for-calculation').show();
                $('#length').prop('disabled', true);
                $('#net_weight').prop('disabled', true);
            }
            else {
                $('#controls-for-calculation').hide();
                $('#length').prop('disabled', false);
                $('#net_weight').prop('disabled', false);
            }
            
            $('#caclulate_by_diameter').change(function(e){
                if(e.target.checked) {
                    $('#controls-for-calculation').show();
                    $('#length').prop('disabled', true);
                    $('#net_weight').prop('disabled', true);
                }
                else {
                    $('#controls-for-calculation').hide();
                    $('#length').prop('disabled', false);
                    $('#net_weight').prop('disabled', false);
                }
            });
            
            // Все марки плёнки с их вариациями
            var films = new Map();
            
            <?php
            $sql = "SELECT fbv.film_brand_id, fbv.thickness, fbv.weight FROM film_brand_variation fbv";
            $fetcher = new Fetcher($sql);
            while ($row = $fetcher->Fetch()) {
                echo "films.set(".$row['film_brand_id'].", new Map());\n";
                echo "films.get(".$row['film_brand_id'].").set(".$row['thickness'].", ".$row['weight'].");\n";
            }
            ?>
            
            // Расчёт по радиусу
            function CalculateByRadius() {
                $('#length').val('');
                $('#net_weight').val('');
                
                film_brand_id = $('#film_brand_id').val();
                shpulya = $('#shpulya').val();
                thickness = $('#thickness').val();
                radiusotvala = $('#diameter').val();
                width = $('#width').val();
                
                if(!isNaN(shpulya) && !isNaN(thickness) && !isNaN(radiusotvala)) {
                    if(shpulya == 76) {
                        var length = (0.15 * radiusotvala * radiusotvala + 11.3961 * radiusotvala - 176.4427) * 20 / thickness;
                        $('#length').val(length.toFixed(2));
                        
                        var net_weight = (length * width) / 1000 / 1000;
                        $('#net_weight').val(net_weight.toFixed(2));
                        //Масса нетто(4)  = (Длинна (3) * Удельный вес (5) * ширину (6))/1000/1000
                    }
                    
                    if(shpulya == 152) {
                        var length = 0.1524 * radiusotvala * radiusotvala + 23.1245 * radiusotvala - 228.5017;
                        $('#length').val(length.toFixed(2));
                        
                        var net_weight = (length * width) / 1000 / 1000;
                        $('#net_weight').val(net_weight.toFixed(2));
                        //Масса нетто(4)  = (Длинна (3) * Удельный вес (5) * ширину (6))/1000/1000
                    }
                }
            }
            
            $('#shpulya').change(CalculateByRadius);
            
            $('#diameter').keypress(CalculateByRadius);
            
            $('#diameter').change(CalculateByRadius);
            
            $(document).ready(CalculateByRadius);
        </script>
   </body>
</html>
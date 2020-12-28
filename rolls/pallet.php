<?php
include '../include/topscripts.php';

// Авторизация
if(!LoggedIn()) {
    header('Location: '.APPLICATION.'/unauthorized.php');
}

        // Валидация формы
        define('ISINVALID', ' is-invalid');
        $form_valid = true;
        $error_message = '';
        
        $supplier_id_valid = '';
        $supplier_code_valid = '';
        $model_id_valid = '';
        $width_valid = '';
        $thickness_valid = '';
        $length_valid = '';
        $weight_valid = '';
        $number_valid = '';
        $cell_valid = '';
        $status_id_valid = '';
        
        // Обработка отправки формы
        if($_SERVER['REQUEST_METHOD'] == 'POST'){
            if($_POST['supplier_id'] == '') {
                $supplier_id_valid = ISINVALID;
                $form_valid = false;
            }
            if($_POST['supplier_code'] == '') {
                $supplier_code_valid = ISINVALID;
                $form_valid = false;
            }
            if($_POST['model_id'] == '') {
                $model_id_valid = ISINVALID;
                $form_valid = false;
            }
            if($_POST['width'] == '') {
                $width_valid = ISINVALID;
                $form_valid = false;
            }
            if($_POST['thickness'] == '') {
                $thickness_valid = ISINVALID;
                $form_valid = false;
            }
            if($_POST['length'] == '' && !is_int($_POST['length'])) {
                $length_valid = ISINVALID;
                $form_valid = false;
            }
            if($_POST['number'] == '' && !is_int($_POST['number'])) {
                $number_valid = ISINVALID;
                $form_valid = false;
            }
            if($_POST['cell'] == '') {
                $cell_valid = ISINVALID;
                $form_valid = false;
            }
            if($_POST['weight'] == '' && !is_float($_POST['weight'])) {
                $weight_valid = ISINVALID;
                $form_valid = false;
            }
            if($_POST['status_id'] == '') {
                $status_id_valid = ISINVALID;
                $form_valid = false;
            }
            
            if($form_valid){
                $conn = new mysqli('localhost', 'root', '', 'erp');
                if($conn->connect_error) {
                    die('Ошибка соединения: '.$conn->connect_error);
                }
                
                $number = intval($_POST['number']);
                
                $unix_time = time();
                $supplier_id = $_POST['supplier_id'];
                $model_id = $_POST['model_id'];
                $width = $_POST['width'];
                $thickness = $_POST['thickness'];
                $pallet_length = $_POST['length'];
                $length = intval($_POST['length']) / $number;
                $pallet_weight = $_POST['weight'];
                $weight = intval($_POST['weight']) / $number;
                $cell = $_POST['cell'];
                $status_id = $_POST['status_id'];
                $user_id = 1;
                $comment = $_POST['comment'];
                $in_pallet = 'true';
                $supplier_code = $_POST['supplier_code'];
                $qr_code = microtime();
                
                for($i=0; $i<$number; $i++) {
                    $sql = "insert into roll"
                            . "(date, supplier_id, model_id, width, thickness, length, pallet_length, weight, pallet_weight, cell, "
                            . "status_id, user_id, comment, in_pallet, supplier_code, qr_code) "
                            . "values "
                            . "(FROM_UNIXTIME($unix_time), $supplier_id, $model_id, $width, $thickness, $length, $pallet_length, $weight, $pallet_weight, '$cell', "
                            . "$status_id, $user_id, '$comment', $in_pallet, '$supplier_code', '$qr_code')";
                    
                    if ($conn->query($sql) === false) {
                        $error_message = $conn->error;
                    }
                }

                if($error_message == '') {
                    header('Location: '.APPLICATION.'/rolls/');
                }
                
                $conn->close();
            }
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
        <div class="container-fluid">
            <?php
            if(isset($error_message) && $error_message != '') {
               echo "<div class='alert alert-danger'>$error_message</div>";
            }
            ?>
            <div class="btn-group">
                <a href="<?=APPLICATION ?>/rolls/" class="btn btn-outline-dark">&LT; Назад</a>
            </div>
            <h1>Новый паллет</h1>
            <div class="row">
                <div class="col-12 col-md-6 col-lg-4">
                    <form method="post">
                        <div class="form-group">
                            <label for="supplier_id">Поставщик</label>
                            <select id="supplier_id" name="supplier_id" class="form-control<?=$supplier_id_valid ?>" required='required'>
                                <option value="">...</option>
                                <?php
                                $conn = new mysqli('localhost', 'root', '', 'erp');
                                if($conn->connect_error) {
                                    die('Ошибка соединения: ' . $conn->connect_error);
                                }
                                $result = $conn->query('select id, name from roll_supplier order by name');
                                if ($result->num_rows > 0) {
                                    while($row = $result->fetch_assoc()) {
                                        $selected = $_SERVER['REQUEST_METHOD'] == 'POST' && $_POST['supplier_id'] == $row['id'] ? " selected='selected'" : "";
                                        echo "<option value='".$row['id']."'".$selected.">".$row["name"]."</option>";
                                    }
                                }
                                $conn->close();
                                ?>
                            </select>
                            <div class="invalid-feedback">Поставщик обязательно</div>
                        </div>
                        <div class="form-group">
                            <label for="supplier_code">Штрих-код от поставщика</label>
                            <input type="text" id="supplier_code" name="supplier_code" class="form-control" value="<?=$_SERVER['REQUEST_METHOD'] == 'POST' ? $_POST['supplier_code'] : '' ?>" required="required" />
                            <div class="invalid-feedback">Штрих-код от поставщика обязательно</div>
                        </div>
                        <div class="form-group">
                            <label for="model_id">Марка</label>
                            <select id="model_id" name="model_id" class="form-control<?=$model_id_valid ?>" required='required'>
                                <option value="">...</option>
                                <?php
                                $conn = new mysqli('localhost', 'root', '', 'erp');
                                if($conn->connect_error) {
                                    die('Ошибка соединения: ' . $conn->connect_error);
                                }
                                $result = $conn->query('select id, name from roll_model order by name');
                                if ($result->num_rows > 0) {
                                    while($row = $result->fetch_assoc()) {
                                        $selected = $_SERVER['REQUEST_METHOD'] == 'POST' && $_POST['model_id'] == $row['id'] ? " selected='selected'" : "";
                                        echo "<option value='".$row['id']."'".$selected.">".$row["name"]."</option>";
                                    }
                                }
                                $conn->close();
                                ?>
                            </select>
                            <div class="invalid-feedback">Марка обязательно</div>
                        </div>
                        <div class="row">
                            <div class="col-6 form-group">
                                <label for="width">Ширина</label>
                                <input type="number" id="width" name="width" class="form-control int-only<?=$width_valid ?>" value="<?=$_SERVER['REQUEST_METHOD'] == 'POST' ? $_POST['width'] : '' ?>" required='required' />
                                <div class="invalid-feedback">Ширина обязательно</div>
                            </div>
                            <div class="col-6 form-group">
                                <label for="thickness">Толщина</label>
                                <input type="number" id="thickness" name="thickness" class="form-control int-only<?=$thickness_valid ?>" value="<?=$_SERVER['REQUEST_METHOD'] == 'POST' ? $_POST['thickness'] : '' ?>" required='required' />
                                <div class="invalid-feedback">Толщина обязательно</div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-6 form-group">
                                <label for="length">Длина (общая)</label>
                                <input type="number" id="length" name="length" class="form-control int-only<?=$length_valid ?>" value="<?=$_SERVER['REQUEST_METHOD'] == 'POST' ? $_POST['length'] : '' ?>" required="required" />
                                <div class="invalid-feedback">Длина обязательно</div>
                            </div>
                            <div class="col-6 form-group">
                                <label for="weight">Масса (общая)</label>
                                <input type="text" id="weight" name="weight" class="form-control float-only<?=$weight_valid ?>" value="<?=$_SERVER['REQUEST_METHOD'] == 'POST' ? $_POST['weight'] : '' ?>" required="required" />
                                <div class="invalid-feedback">Масса обязательно</div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-6 form-group">
                                <label for="number">Количество роликов</label>
                                <input type="number" id="number" name="number" class="form-control int-only<?=$number_valid ?>" value="<?=$_SERVER['REQUEST_METHOD'] == 'POST' ? $_POST['number'] : '' ?>" required="required" />
                                <div class="invalid-feedback">Количество роликов обязательно</div>
                            </div>
                            <div class="col-6 form-group">
                                <label for="cell">Ячейка на складе</label>
                                <input type="text" id="cell" name="cell" class="form-control<?=$cell_valid ?>" value="<?=$_SERVER['REQUEST_METHOD'] == 'POST' ? $_POST['cell'] : '' ?>" required="required" />
                                <div class="invalid-feedback">Ячейка на складе обязательно</div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="status_id">Статус</label>
                            <select id="status_id" name="status_id" class="form-control<?=$status_id_valid ?>" required="required">
                                <option value="">...</option>
                                <?php
                                $conn = new mysqli('localhost', 'root', '', 'erp');
                                if($conn->connect_error) {
                                    die('Ошибка соединения: ' . $conn->connect_error);
                                }
                                $result = $conn->query('select id, name from roll_status order by name');
                                if ($result->num_rows > 0) {
                                    while($row = $result->fetch_assoc()) {
                                        $selected = $_SERVER['REQUEST_METHOD'] == 'POST' && $_POST['status_id'] == $row['id'] ? " selected='selected'" : "";
                                        echo "<option value='".$row['id']."'".$selected.">".$row["name"]."</option>";
                                    }
                                }
                                $conn->close();
                                ?>
                            </select>
                            <div class="invalid-feedback">Статус обязательно</div>
                        </div>
                        <div class="form-group">
                            <label for="comment">Комментарий</label>
                            <textarea rows="5" id="comment" name="comment" class="form-control"><?=$_SERVER['REQUEST_METHOD'] == 'POST' ? $_POST['comment'] : '' ?></textarea>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <div class="p-1">
                                <button type="submit" class="btn btn-primary">Сохранить</button>
                            </div>
                            <div class="p-1">
                                <button type="button" class="btn btn-secondary">Стикер</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <?php
        include '../include/footer.php';
        ?>
    </body>
</html>
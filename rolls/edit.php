<?php
include '../include/topscripts.php';

// Получение объекта
        $id = '';
        $date = '';
        $supplier_id = '';
        $suplier_code = '';
        $model_id = '';
        $width = '';
        $thickness = '';
        $length = '';
        $weight = '';
        $cell = '';
        $status_id = '';
        $comment = '';
        $edited = '';
        $in_pallet = '';
        
        if($_GET['id'] != '') {
            $id = $_GET['id'];
            
            $conn = new mysqli('localhost', 'root', '', 'erp');
            $sql = "select date, supplier_id, model_id, width, thickness, length, pallet_length, weight, pallet_weight, 
                cell, status_id, user_id, comment, in_pallet, supplier_code, qr_code, edited 
                from roll
                where id=".$_GET['id'];
            
            if($conn->connect_error) {
                die('Ошибка соединения: ' . $conn->connect_error);
            }
            $result = $conn->query($sql);
            if ($result->num_rows > 0 && $row = $result->fetch_assoc()) {
                $date = $row['date'];
                $supplier_id = $row['supplier_id'];
                $suplier_code = $row['supplier_code'];
                $model_id = $row['model_id'];
                $width = $row['width'];
                $thickness = $row['thickness'];
                $length = $row['length'];
                $weight = $row['weight'];
                $cell = $row['cell'];
                $status_id = $row['status_id'];
                $comment = $row['comment'];
                $edited = $row['edited'];
                $in_pallet = $row['in_pallet'];
            }
            $conn->close();
        }
        
        // Валидация формы
        define('ISINVALID', ' is-invalid');
        $form_valid = true;
        $error_message = '';
        
        $supplier_id_valid = '';
        $model_id_valid = '';
        $width_valid = '';
        $thickness_valid = '';
        $length_valid = '';
        $weight_valid = '';
        $cell_valid = '';
        $status_id_valid = '';
        
        // Обработка отправки формы
        if($_SERVER['REQUEST_METHOD'] == 'POST'){
            if($_POST['id'] == '') {
                $error_message = "Ошибка при получении id объекта";
                $form_valid = false;
            }
            if($_POST['supplier_id'] == '') {
                $supplier_id_valid = ISINVALID;
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
            if($_POST['length'] == '') {
                $length_valid = ISINVALID;
                $form_valid = false;
            }
            if($_POST['cell'] == '') {
                $cell_valid = ISINVALID;
                $form_valid = false;
            }
            if($_POST['weight'] == '') {
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
                
                $supplier_id = $_POST['supplier_id'];
                $model_id = $_POST['model_id'];
                $width = $_POST['width'];
                $thickness = $_POST['thickness'];
                $length = $_POST['length'];
                $weight = $_POST['weight'];
                $cell = $_POST['cell'];
                $status_id = $_POST['status_id'];
                $comment = $_POST['comment'];
                $edited = (isset($_POST['edited']) && $_POST['edited'] == 'on') ? 1 : 0;
                
                $sql = "update roll "
                        . "set supplier_id=$supplier_id, model_id=$model_id, width=$width, thickness=$thickness, length=$length, weight=$weight, cell='$cell', "
                        . "status_id=$status_id, comment='$comment', edited=$edited "
                        . "where id=".$id;
                
                if ($conn->query($sql) === true) {
                    //header('Location: '.APPLICATION.'/rolls/details.php?id='.$id);
                }
                else {
                    $error_message = $conn->error;
                }
                
                $conn->close();
            }
        }
        
        // Корректирование значений длины и веса в других роллях этого паллета
        if($_POST['in_pallet'] == 1) {
            // Вычисляем количество неотредактированных роллей этого же паллета
            $unedited_siblings_number = 0;
            
            $conn = new mysqli('localhost', 'root', '', 'erp');
            if($conn->connect_error) {
                die('Ошибка соединения: '.$conn->connect_error);
            }
            
            $sql = "select count(id) "
                    ."from roll "
                    ."where "
                        . "set supplier_id=$supplier_id, model_id=$model_id, width=$width, thickness=$thickness, length=$length, weight=$weight, cell='$cell', "
                        . "status_id=$status_id, comment='$comment', supplier_code='$suplier_code', edited=$edited "
                        . "where id=".$id;
                
                if ($conn->query($sql) === true) {
                    //header('Location: '.APPLICATION.'/rolls/details.php?id='.$id);
                }
                else {
                    $error_message = $conn->error;
                }
                
                $conn->close();
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
                <a href="<?=APPLICATION ?>/rolls/" class="btn btn-outline-dark"><i class="fas fa-undo"></i>&nbsp;Назад</a>
            </div>
            <?php
            if($_GET['id'] == '') {
                echo '<h1>Редактирование ролика</h1>';
            }
            else {
                echo '<h1>Редактирование ролика №'.$_GET['id'].' от '.$date.'</h1>';
            }
            ?>
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
                                        $selected = ($_SERVER['REQUEST_METHOD'] == 'POST' && $_POST['supplier_id'] == $row['id']) || ($supplier_id == $row['id']) ? " selected='selected'" : "";
                                        echo "<option value='".$row['id']."'".$selected.">".$row["name"]."</option>";
                                    }
                                }
                                $conn->close();
                                ?>
                           </select>
                           <div class="invalid-feedback">Поставщик обязательно</div>
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
                                        $selected = ($_SERVER['REQUEST_METHOD'] == 'POST' && $_POST['model_id'] == $row['id']) || ($model_id == $row['id']) ? " selected='selected'" : "";
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
                               <input type="number" id="width" name="width" class="form-control int-only<?=$width_valid ?>" value="<?=$_SERVER['REQUEST_METHOD'] == 'POST' ? $_POST['width'] : $width ?>" required='required' />
                               <div class="invalid-feedback">Ширина обязательно</div>
                           </div>
                           <div class="col-6 form-group">
                               <label for="thickness">Толщина</label>
                               <input type="number" id="thickness" name="thickness" class="form-control int-only<?=$thickness_valid ?>" value="<?=$_SERVER['REQUEST_METHOD'] == 'POST' ? $_POST['thickness'] : $thickness ?>" required='required' />
                               <div class="invalid-feedback">Толщина обязательно</div>
                           </div>
                       </div>
                       <div class="row">
                           <div class="col-6 form-group">
                               <label for="length">Длина</label>
                               <input type="number" id="length" name="length" class="form-control int-only<?=$length_valid ?>" value="<?=$_SERVER['REQUEST_METHOD'] == 'POST' ? $_POST['length'] : $length ?>" required="required" />
                               <div class="invalid-feedback">Длина обязательно</div>
                           </div>
                           <div class="col-6 form-group">
                               <label for="weight">Масса</label>
                               <input type="text" id="weight" name="weight" class="form-control float-only<?=$weight_valid ?>" value="<?=$_SERVER['REQUEST_METHOD'] == 'POST' ? $_POST['weight'] : $weight ?>" required="required" />
                               <div class="invalid-feedback">Масса обязательно</div>
                           </div>
                       </div>
                       <div class="row">
                           <div class="col-6 form-group">
                               <label for="cell">Ячейка на складе</label>
                               <input type="text" id="cell" name="cell" class="form-control<?=$cell_valid ?>" value="<?=$_SERVER['REQUEST_METHOD'] == 'POST' ? $_POST['cell'] : $cell ?>" required="required" />
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
                                        $selected = ($_SERVER['REQUEST_METHOD'] == 'POST' && $_POST['status_id'] == $row['id']) || ($status_id == $row['id']) ? " selected='selected'" : "";
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
                           <textarea rows="5" id="comment" name="comment" class="form-control"><?=$_SERVER['REQUEST_METHOD'] == 'POST' ? $_POST['comment'] : $comment ?></textarea>
                       </div>
                       <div class="form-check">
                           <input type="checkbox" class="form-check-input" id="edited" name="edited" checked="checked" />
                           <label class="form-check-label" for="edited">Отредактировано</label>
                       </div>
                       <br />
                       <div class="d-flex justify-content-between mb-2">
                           <div class="p-1">
                               <button type="submit" class="btn btn-primary">Сохранить</button>
                           </div>
                           <div class="p-1">
                               <button type="button" class="btn btn-secondary">Стикер</button>
                           </div>
                       </div>
                       <input type="hidden" id="supplier_code" name="supplier_code" class="form-control" value="<?=$_SERVER['REQUEST_METHOD'] == 'POST' ? $_POST['supplier_code'] : $suplier_code ?>" />
                       <input type="hidden" id="in_pallet" name="in_pallet" value="<?=$in_pallet ?>" />
                       <input type="hidden" id="id" name="id" value="<?=$id ?>" />
                   </form>
               </div>
           </div>
        </div>
        <?php
        include '../include/footer.php';
        ?>
    </body>
</html>
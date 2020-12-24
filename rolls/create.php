<!DOCTYPE html>
<html>
    <head>
        <?php
        include '../include/head.php';
        
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
                
                $unix_time = time();
                $supplier_id = $_POST['supplier_id'];
                $model_id = $_POST['model_id'];
                $width = $_POST['width'];
                $thickness = $_POST['thickness'];
                $length = $_POST['length'];
                $weight = $_POST['weight'];
                $cell = $_POST['cell'];
                $status_id = $_POST['status_id'];
                $user_id = 1;
                $comment = $_POST['comment'];
                $in_pallet = 'false';
                $supplier_code = $_POST['supplier_code'];
                $qr_code = microtime();
                
                $sql = "insert into roll"
                        . "(date, supplier_id, model_id, width, thickness, length, weight, cell, "
                        . "status_id, user_id, comment, in_pallet, supplier_code, qr_code) "
                        . "values "
                        . "(FROM_UNIXTIME($unix_time), $supplier_id, $model_id, $width, $thickness, $length, $weight, '$cell', "
                        . "$status_id, $user_id, '$comment', $in_pallet, '$supplier_code', '$qr_code')";
                
                if ($conn->query($sql) === true) {
                    header('Location: '.APPLICATION.'/rolls/');
                }
                else {
                    $error_message = $conn->error;
                }
                
                $conn->close();
            }
        }
        ?>
        <script src='<?=APPLICATION ?>/js/jsQR.js'></script>
        <style>
            #canvas {
                width: 100%;
            }
        </style>
    </head>
   <body>
       <?php
       include '../include/header.php';
       ?>
       <div class="container">
           <?php
           if($error_message != '') {
               echo <<<ERROR
               <div class="alert alert-danger">$error_message</div>
               ERROR;
           }
           ?>
           <div class="btn-group">
               <a href="<?=APPLICATION ?>/rolls/" class="btn btn-outline-dark">&LT; Назад</a>
           </div>
           <h1>Новый ролик</h1>
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
                           <label for="supplier_qr_code">Штрих-код от поставщика</label>
                           <input type="text" id="supplier_qr_code" name="supplier_qr_code" class="form-control" value="<?=$_SERVER['REQUEST_METHOD'] == 'POST' ? $_POST['supplier_qr_code'] : '' ?>" />
                           <div id="loadingMessage">🎥 Unable to access video stream (please make sure you have a webcam enabled)</div>
                           <canvas id="canvas" hidden></canvas>
                           <script>
                               var video = document.createElement("video");
                               var canvasElement = document.getElementById("canvas");
                               var canvas = canvasElement.getContext("2d");
                               var loadingMessage = document.getElementById("loadingMessage");
                               var outputData = document.getElementById("supplier_qr_code");
                               
                                function drawLine(begin, end, color) {
                                    canvas.beginPath();
                                    canvas.moveTo(begin.x, begin.y);
                                    canvas.lineTo(end.x, end.y);
                                    canvas.lineWidth = 4;
                                    canvas.strokeStyle = color;
                                    canvas.stroke();
                                }
                                
                                // Use facingMode: environment to attemt to get the front camera on phones
                                navigator.mediaDevices.getUserMedia({ video: { facingMode: "environment" } }).then(function(stream) {
                                    video.srcObject = stream;
                                    video.setAttribute("playsinline", true); // required to tell iOS safari we don't want fullscreen
                                    video.play();
                                    requestAnimationFrame(tick);
                                });
                                
                                function tick() {
                                    loadingMessage.innerText = "⌛ Loading video..."
                                    if (video.readyState === video.HAVE_ENOUGH_DATA) {
                                        loadingMessage.hidden = true;
                                        canvasElement.hidden = false;
                                        canvasElement.height = video.videoHeight;
                                        canvasElement.width = video.videoWidth;
                                        canvas.drawImage(video, 0, 0, canvasElement.width, canvasElement.height);
                                        var imageData = canvas.getImageData(0, 0, canvasElement.width, canvasElement.height);
                                        var code = jsQR(imageData.data, imageData.width, imageData.height, {
                                            inversionAttempts: "dontInvert",
                                        });
                                        
                                        if (code) {
                                            drawLine(code.location.topLeftCorner, code.location.topRightCorner, "#FF3B58");
                                            drawLine(code.location.topRightCorner, code.location.bottomRightCorner, "#FF3B58");
                                            drawLine(code.location.bottomRightCorner, code.location.bottomLeftCorner, "#FF3B58");
                                            drawLine(code.location.bottomLeftCorner, code.location.topLeftCorner, "#FF3B58");
                                            outputData.value = code.data;
                                        }
                                    }
                                    requestAnimationFrame(tick);
                                }
                            </script>
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
                               <label for="length">Длина</label>
                               <input type="number" id="length" name="length" class="form-control int-only<?=$length_valid ?>" value="<?=$_SERVER['REQUEST_METHOD'] == 'POST' ? $_POST['length'] : '' ?>" required="required" />
                               <div class="invalid-feedback">Длина обязательно</div>
                           </div>
                           <div class="col-6 form-group">
                               <label for="weight">Масса</label>
                               <input type="text" id="weight" name="weight" class="form-control float-only<?=$weight_valid ?>" value="<?=$_SERVER['REQUEST_METHOD'] == 'POST' ? $_POST['weight'] : '' ?>" required="required" />
                               <div class="invalid-feedback">Масса обязательно</div>
                           </div>
                       </div>
                       <div class="row">
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
<?php
include '../include/topscripts.php';

// Авторизация
if(!IsInRole(array('admin', 'dev', 'technologist', 'storekeeper'))) {
    header('Location: '.APPLICATION.'/unauthorized.php');
}

// Если не задано значение inner_id, перенаправляем на список
if(empty(filter_input(INPUT_GET, 'id'))) {
    header('Location: '.APPLICATION.'/pallet/');
}

// Валидация формы
define('ISINVALID', ' is-invalid');
$form_valid = true;
$error_message = '';

// Обработка отправки формы
if(null !== filter_input(INPUT_POST, 'cut-request-submit')) {
    $keys = array_keys($_POST);
    $sorted_keys = sort($keys);
    
    $streams = array();
    
    foreach ($keys as $key) {
        if(substr($key, 0, strlen('width')) == 'width') {
            $stream_number = substr($key, strlen('width'));
            $width = filter_input(INPUT_POST, $key);
            $request = filter_input(INPUT_POST, 'request'.$stream_number);
            
            // Валидация поля "Ширина"
            if(empty($width)) {
                $error_message = 'Обязательно укажите ширину каждого ручья';
                $form_valid = false;
            }
            else {
                $stream = array();
                $stream['width'] = $width;
                $stream['request'] = $request;
                array_push($streams, $stream);
            }
        }
    }
    
    if(count($streams) > 0) {
        $error_message = (new Executer("insert into "))->error;
        
        if(empty($error_message)) {
            //
        }
    }
}

// Получение данных
$id = filter_input(INPUT_GET, 'id');
$sql = "select p.id, p.inner_id, p.date, p.storekeeper_id, p.supplier_id, sp.name supplier, p.id_from_supplier, "
        . "p.film_brand_id, fb.name film_brand, p.width, p.thickness, p.length, "
        . "p.net_weight, p.rolls_number, p.cell, psh.status_id status_id, s.name status, s.colour colour, p.comment "
        . "from pallet p "
        . "left join supplier sp on p.supplier_id = sp.id "
        . "left join film_brand fb on p.film_brand_id = fb.id "
        . "left join (select * from pallet_status_history where id in (select max(id) from pallet_status_history group by pallet_id)) psh on psh.pallet_id = p.id "
        . "left join pallet_status s on psh.status_id = s.id "
        . "where p.id=$id";

$fetcher = (new Fetcher($sql));
$row = $fetcher->Fetch();
$error_message = $fetcher->error;

$id = $row['id'];
$inner_id = $row['inner_id'];
$date = $row['date'];
$storekeeper_id = $row['storekeeper_id'];
$supplier_id = $row['supplier_id'];
$supplier = $row['supplier'];
$id_from_supplier = $row['id_from_supplier'];
$film_brand_id = $row['film_brand_id'];
$film_brand = $row['film_brand'];
$width = $row['width'];
$thickness = $row['thickness'];
$length = $row['length'];
$net_weight = $row['net_weight'];
$rolls_number = $row['rolls_number'];
$cell = $row['cell'];
$status_id = $row['status_id'];
$status = $row['status'];
$colour = $row['colour'];
$comment = $row['comment'];

// Формирование порядковых числительных
function GetOrdinal($param) {
    switch ($param) {
        case 1:
            return 'Первый';
        case 2:
            return 'Второй';
        case 3:
            return 'Третий';
        case 4:
            return 'Четвёртый';
        case 5:
            return 'Пятый';
        case 6:
            return 'Шестой';
        case 7:
            return 'Седьмой';
        case 8:
            return 'Восьмой';
        case 9:
            return 'Девятый';
        case 10:
            return 'Десятый';
        default :
            return $param.'-й';
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
                        <input type="hidden" id="pallet_id" name="pallet_id" value="<?= filter_input(INPUT_POST, 'id') ?>" />
                        <div class="form-group">
                            <label for="length">Длина</label>
                            <input type="text" class="form-control int-only" style="width: 200px;" id="length" name="length" value="<?= filter_input(INPUT_POST, 'length') ?>" required="required" />
                            <div class="invalid-feedback">Длина обязательно и не больше, чем длина паллета.</div>
                        </div>
                        <input type="hidden" class="stream_number" value="1"/>
                        <p>Первый ручей</p>
                        <div class="form-group">
                            <label for="width1">Ширина</label>
                            <input type="text" class="form-control int-only" style="width: 200px;" id="width1" name="width1" value="<?= filter_input(INPUT_POST, 'width1') ?>" required="required" />
                            <div class="invalid-feedback">Ширина обязательно.</div>
                        </div>
                        <div class="form-group">
                            <label for="request1">Под какой заказ режем?</label>
                            <textarea id="request1" name="request1" class="form-control" rows="5" style="width: 500px;"><?= filter_input(INPUT_POST, 'request1') ?></textarea>
                        </div>
                        <?php
                        // Добавляем другие ручьи
                        $keys = array_keys($_POST);
                        $sorted_keys = sort($keys);
                        
                        foreach ($keys as $key) {
                            if(substr($key, 0, strlen('width')) == 'width') {
                                $stream_number = substr($key, strlen('width'));
                                if(!empty(intval($stream_number)) && intval($stream_number) > 1):
                                ?>
                        <input type="hidden" class="stream_number" value="<?=$stream_number ?>"/>
                        <p style="margin-top: 30px;"><?= GetOrdinal($stream_number) ?> ручей</p>
                        <div class="form-group">
                            <label for="width<?=$stream_number ?>">Ширина</label>
                            <input type="text" class="form-control int-only" style="width: 200px;" id="width<?=$stream_number ?>" name="width<?=$stream_number ?>" value="<?= filter_input(INPUT_POST, 'width'.$stream_number) ?>" required="required" />
                            <div class="invalid-feedback">Ширина обязательно.</div>
                        </div>
                        <div class="form-group">
                            <label for="request<?=$stream_number ?>">Под какой заказ режем?</label>
                            <textarea id="request<?=$stream_number ?>" name="request<?=$stream_number ?>" class="form-control" rows="5" style="width: 500px;"><?= filter_input(INPUT_POST, 'request'.$stream_number) ?></textarea>
                        </div>
                                <?php
                                endif;
                            }
                        }
                        ?>
                        <button type="button" class="btn btn-link" id="add-stream-btn" style="margin-bottom: 50px;"><i class="fas fa-plus" style="font-size: 10px; vertical-align: top; margin-top: 3px;"></i>&nbsp;Добавить ручей</button>
                        <div class="form-group">
                            <label for="remainder">Остаток</label>
                            <input type="text" class="form-control" style="width: 200px;" id="remainder" name="remainder" value="<?= filter_input(INPUT_POST, 'remainder') ?>" disabled="disabled" />
                        </div>
                        <button type="submit" class="btn btn-dark" id="cut-request-submit" name="cut-request-submit" style="margin-top: 20px; padding-top: 14px; padding-bottom: 14px; padding-left: 50px; padding-right: 50px;">ОТПРАВИТЬ НА РАСКРОЙ</button>
                    </form>                    
                </div>
                <div class="col-6">
                    <h1>Паллет №<?=$inner_id ?></h1>
                    <p><a href="<?=APPLICATION ?>/pallet/details.php?inner_id=<?=$inner_id ?>">К информации о паллете&nbsp;></a></p>
                    <br/>
                    <?php
                    $colour_style = '';
                    if(!empty($colour)) {
                        $colour_style = " style='color: $colour;'";
                    }
                    ?>
                    Статус: <span<?=$colour_style ?>"><?= mb_strtoupper($status) ?></span><br />
                    ID: <?=$inner_id ?><br/>
                    Дата: <?= DateTime::createFromFormat('Y-m-d', $date)->format('d.m.Y') ?><br />
                    Поставщик: <?=$supplier ?><br />
                    Марка пленки: <?=$film_brand ?><br />
                    Толщина: <?=$thickness ?> мкм<br />
                    Ширина: <?=$width ?><br />
                    Масса нетто: <?=$net_weight ?> кг<br />
                    Длина: <?= number_format($length, 0, ',', ' ') ?><br />
                    Ячейка на складе: <?=$cell ?><br />
                    Количество рулонов: <?=$rolls_number ?><br />
                    <br />
                    <strong>Комментарий:</strong><br />
                    <i><?=$comment ?></i>
                </div>
            </div>
        </div>
        <?php
        include '../include/footer.php';
        ?>
        <script>
            $('#width1').keyup(function(){
                $('#remainder').val(GetWidthRemainder());
            });
            
            var max_stream = 0;
            
            $('#add-stream-btn').click(function(){
                $('.stream_number').each(function(){
                    max_stream = Math.max(parseInt(max_stream), parseInt($(this).val()));
                });
                
                new_stream = parseInt(max_stream) + 1;
                
                var new_controls = '<input type="hidden" class="stream_number" value="' + new_stream + '"/>' + 
                        '<p style="margin-top:30px;">' + GetOrdinal(new_stream) + ' ручей</p>' +
                        '<div class="form-group">' + 
                        '<label for="width' + new_stream + '">Ширина</label>' + 
                        '<input type="text" class="form-control int-only" style="width: 200px;" id="width' + new_stream + '" name="width' + new_stream + '" required="required" />' + 
                        '<div class="invalid-feedback">Ширина обязательно.</div>' + 
                        '</div>' + 
                        '<div class="form-group">' + 
                        '<label for="request' + new_stream + '">Под какой заказ режем?</label>' + 
                        '<textarea id="request' + new_stream + '" name="request' + new_stream + '" class="form-control" rows="5" style="width: 500px;"></textarea>' + 
                        '</div>';
                
                $('textarea#request' + max_stream).parent('.form-group').append(new_controls);
                stream_widths.push('width' + new_stream);
                $('#width' + new_stream).keyup(function(){
                    $('#remainder').val(GetWidthRemainder());
                });
            });
            
            var stream_widths = ['width1'];
            
            $('#remainder').val(GetWidthRemainder());
            
            // Подсчёт остатка ширины
            function GetWidthRemainder() {
                var result = 0;
                var parsed = parseInt(<?=$width ?>);
                if(!isNaN(parsed)) {
                    result = parsed;
                }
                
                $.each(stream_widths, function(index, value) {
                    parsed = parseInt($('#' + value).val());
                    
                    if(!isNaN(parsed)) {
                        result = result - parsed;
                    }
                });
                
                return result;
            }
            
            // Формирование порядковых числительных
            function GetOrdinal(param) {
                switch(param) {
                    case 1:
                        return 'Первый';
                    case 2:
                        return 'Второй';
                    case 3:
                        return 'Третий';
                    case 4:
                        return 'Четвёртый';
                    case 5:
                        return 'Пятый';
                    case 6:
                        return 'Шестой';
                    case 7:
                        return 'Седьмой';
                    case 8:
                        return 'Восьмой';
                    case 9:
                        return 'Девятый';
                    case 10:
                        return 'Десятый';
                    default:
                        return param + '-й';
            }
        }
        </script>
    </body>
</html>
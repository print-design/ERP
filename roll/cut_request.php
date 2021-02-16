<?php
include '../include/topscripts.php';

// Авторизация
if(!IsInRole(array('admin', 'dev', 'technologist'))) {
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
        $date = date('Y-m-d');
        $length = filter_input(INPUT_POST, 'length');
        $roll_id = filter_input(INPUT_POST, 'roll_id');
        $executer = new Executer("insert into cut_request (date, length, pallet_id, roll_id) values ('$date', '$length', NULL, '$roll_id')");
        $error_message = $executer->error;
        $cut_request_id = $executer->insert_id;
        
        if(empty($error_message)) {
            foreach ($streams as $stream) {
                $width = $stream['width'];
                $request = addslashes($stream['request']);
                $error_message = (new Executer("insert into stream (cut_request_id, width, request) values ($cut_request_id, '$width', '$request')"))->error;
            }
            
            if(empty($error_message)) {
                header('Location: '.APPLICATION.'/cut_request/');
            }
        }
    }
}

// Получение данных
$id = filter_input(INPUT_GET, 'id');
$sql = "select r.id, r.inner_id, r.date, r.storekeeper_id, r.supplier_id, sp.name supplier, r.id_from_supplier, "
        . "r.film_brand_id, fb.name film_brand, r.width, r.thickness, r.length, "
        . "r.net_weight, r.cell, psh.status_id status_id, s.name status, s.colour colour, r.comment "
        . "from roll r "
        . "left join supplier sp on r.supplier_id = sp.id "
        . "left join film_brand fb on r.film_brand_id = fb.id "
        . "left join (select * from pallet_status_history where id in (select max(id) from pallet_status_history group by pallet_id)) psh on psh.pallet_id = r.id "
        . "left join pallet_status s on psh.status_id = s.id "
        . "where r.id=$id";

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
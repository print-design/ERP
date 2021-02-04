<?php
include '../include/topscripts.php';

// Авторизация
if(!IsInRole(array('admin', 'dev', 'technologist', 'storekeeper'))) {
    header('Location: '.APPLICATION.'/unauthorized.php');
}

// Получение данных
$inner_id = filter_input(INPUT_POST, 'inner_id');
$date = filter_input(INPUT_POST, 'date');
$supplier_id = filter_input(INPUT_POST, 'supplier_id');
$id_from_supplier = filter_input(INPUT_POST, 'id_from_supplier');
$film_brand_id = filter_input(INPUT_POST, 'film_brand_id');
$width = filter_input(INPUT_POST, 'width');
$thickness = filter_input(INPUT_POST, 'thickness');
$length = filter_input(INPUT_POST, 'length');
$net_weight = filter_input(INPUT_POST, 'net_weight');
$rolls_number = filter_input(INPUT_POST, 'rolls_number');
$cell = filter_input(INPUT_POST, 'cell');
$manager_id = filter_input(INPUT_POST, 'manager_id');
$status_id = filter_input(INPUT_POST, 'status_id');
$comment = filter_input(INPUT_POST, 'comment');

$supplier = '';
$film_brand = '';
$manager = '';
$status = '';

if(!empty($supplier_id) || !empty($film_brand_id) || !empty($manager_id) || !empty($status_id)) {
    $sql = '';
    
    if(!empty($supplier_id)) {
        $sql .= "s.name supplier"; 
    }
    
    if(!empty($film_brand_id)) {
        if(!empty($sql)) $sql .= ", ";
        $sql .= "fb.name film_brand";
    }
    
    if(!empty($manager_id)) {
        if(!empty($sql)) $sql .= ", ";
        $sql .= "u.last_name, u.first_name";
    }
    
    if(!empty($status_id)) {
        if(!empty($sql)) $sql .= ", ";
        $sql .= "st.name status";
    }
    
    $sql = "select $sql from pallet p "
            . "left join supplier s on p.supplier_id = s.id "
            . "left join film_brand fb on p.film_brand_id = fb.id "
            . "left join user u on p.manager_id = u.id "
            . "left join pallet_status st on p.status_id = st.id";
    
    $row = (new Fetcher($sql))->Fetch();
    if(!empty($row['supplier'])) $supplier = $row['supplier'];
    if(!empty($row['film_brand'])) $film_brand = $row['film_brand'];
    if(!empty($row['last_name']) || !empty($row['first_name'])) $manager = $row['last_name'].' '.$row['first_name'];
    if(!empty($row['status'])) $status = $row['status'];
}
?>
<!DOCTYPE html>
<html>
    <head>
        <?php
        include '../include/head.php';
        ?>
    </head>
    <body class="print">
        <div style="width: 400px; margin-left: 20px;">
            <h1 style="font-size: 26px; margin-top: 10px; margin-bottom: 30px;">Паллет №<?=$inner_id ?> от <?=$date ?></h1>
            <table class="table table-bordered">
                <tbody>
                    <tr>
                        <td colspan="2">
                            <strong>Поставщик:</strong><br/>
                            <?=$supplier ?>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2">
                            <strong>ID от поставщика:</strong><br/>
                            <?=$inner_id ?>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2">
                            <strong>Марка пленки</strong><br/>
                            <?=$film_brand ?>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <strong>Ширина</strong><br/>
                            <?=$width ?>
                        </td>
                        <td>
                            <strong>Толщина</strong><br/>
                            <?=$thickness ?>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <strong>Длина</strong><br/>
                            <?=$length ?>
                        </td>
                        <td>
                            <strong>Масса нетто</strong><br/>
                            <?=$net_weight ?>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <strong>Количество рулонов</strong><br/>
                            <?=$rolls_number ?>
                        </td>
                        <td>
                            <strong>Ячейка на складе</strong><br/>
                            <?=$cell ?>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2">
                            <strong>Менеджер</strong><br/>
                            <?=$manager ?>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2">
                            <strong>Статус</strong><br/>
                            <?=$status ?>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2">
                            <strong>Комментарий</strong><br/>
                            <div style="white-space: pre-wrap;"><?= $comment ?></div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        <script>
            var css = '@page { size: landscape; margin: 8mm; }',
                    head = document.head || document.getElementsByTagName('head')[0],
                    style = document.createElement('style');
            
            style.type = 'text/css';
            style.media = 'print';
            
            if (style.styleSheet){
                style.styleSheet.cssText = css;
            } else {
                style.appendChild(document.createTextNode(css));
            }
            
            head.appendChild(style);
            
            window.print();</script>
    </body>
</html>
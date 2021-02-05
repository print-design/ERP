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

if(!empty($supplier_id)) {
    $row = (new Fetcher("select name from supplier where id = $supplier_id"))->Fetch();
    $supplier = $row['name'];
}

if(!empty($film_brand_id)) {
    $row = (new Fetcher("select name from film_brand where id = $film_brand_id"))->Fetch();
    $film_brand = $row['name'];
}

if(!empty($manager_id)) {
    $row = (new Fetcher("select first_name, last_name from user where id = $manager_id"))->Fetch();
    $manager = $row['last_name'].' '.$row['first_name'];
}

if(!empty($status_id)) {
    $row = (new Fetcher("select name from pallet_status where id = $status_id"))->Fetch();
    $status = $row['name'];
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
        <div style="margin-left: 20px;">
            <h1 style="font-size: 26px; margin-top: 10px; margin-bottom: 30px;">Паллет №<?=$inner_id ?> от <?=$date ?></h1>
            <table>
                <tr>
                    <td>
                        <table class="table table-bordered" style="width: 400px;">
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
                            <?=$id_from_supplier ?>
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
                        </td>
                        <td style="vertical-align: top; padding-left: 50px;">
                            <?php
                            $PNG_TEMP_DIR = APPLICATION.'/temp/';
                            $PNG_WEB_DIR = APPLICATION.'/temp/';
                            include '../qr/qrlib.php';
                            $errorCorrectionLevel = 'M'; // 'L','M','Q','H'
                            $matrixPointSize = 4;
                            $data = filter_input(INPUT_SERVER, 'HTTP_ORIGIN').APPLICATION.'/pallet/details.php?inner_id='.$inner_id;
                            $filename = '../temp/test.png';
                            QRcode::png(addslashes($data), $filename, $errorCorrectionLevel, 10, 4, true);
                            echo "<img src='$filename' />";
                            ?>
                        </td>
                </tr>
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
            
            window.print();
        </script>
    </body>
</html>
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
$cell = filter_input(INPUT_POST, 'cell');
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
            <h1 style="font-size: 26px; margin-top: 10px; margin-bottom: 30px;">Рулон №<?=$inner_id ?> от <?=$date ?></h1>
            <table>
                <tr>
                    <td>
                        <table class="table table-bordered" style="width: 400px;">
                            <tbody>
                                <tr><td colspan="2"><strong>Поставщик:</strong><br/><?=$supplier ?></td></tr>
                                <tr><td colspan="2"><strong>ID от поставщика:</strong><br/><?=$id_from_supplier ?></td></tr>
                                <tr><td colspan="2"><strong>Марка пленки</strong><br/><?=$film_brand ?></td></tr>
                                <tr>
                                    <td><strong>Ширина</strong><br/><?=$width ?></td>
                                    <td><strong>Толщина</strong><br/><?=$thickness ?></td>
                                </tr>
                                <tr>
                                    <td><strong>Длина</strong><br/><?=$length ?></td>
                                    <td><strong>Масса нетто</strong><br/><?=$net_weight ?></td>
                                </tr>
                                <tr>
                                    <td></td>
                                    <td><strong>Ячейка на складе</strong><br/><?=$cell ?></td>
                                </tr>
                                <tr><td colspan="2"><strong>Статус</strong><br/><?=$status ?></td></tr>
                                <tr><td colspan="2"><strong>Комментарий</strong><br/><div style="white-space: pre-wrap;"><?= $comment ?></div></td></tr>
                            </tbody>
                        </table>
                    </td>
                    <td style="vertical-align: top; padding-left: 50px;">
                        <?php
                        include '../qr/qrlib.php';
                        $errorCorrectionLevel = 'M'; // 'L','M','Q','H'
                        $data = filter_input(INPUT_SERVER, 'HTTP_ORIGIN').APPLICATION.'/roll/details.php?inner_id='.$inner_id;
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
            
            window.print();</script>
    </body>
</html>
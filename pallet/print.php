<?php
include '../include/topscripts.php';

// Если не задано значение id, перенаправляем на список
$id = filter_input(INPUT_GET, 'id');
if(empty($id)) {
    header('Location: '.APPLICATION.'/pallet/');
}

// Получение данных
$sql = "select p.date, p.storekeeper_id, u.last_name, u.first_name, p.supplier_id, s.name supplier, p.id_from_supplier, "
        . "p.film_brand_id, fb.name film_brand, p.width, p.thickness, p.length, "
        . "p.net_weight, p.rolls_number, p.cell, "
        . "(select ps.name from pallet_status_history psh left join pallet_status ps on psh.status_id = ps.id where psh.pallet_id = p.id order by psh.id desc limit 0, 1) status, "
        . "p.comment "
        . "from pallet p "
        . "left join user u on p.storekeeper_id = u.id "
        . "left join supplier s on p.supplier_id = s.id left join film_brand fb on p.film_brand_id = fb.id "
        . "where p.id=$id";

$row = (new Fetcher($sql))->Fetch();
$date = $row['date'];
$storekeeper_id = $row['storekeeper_id'];
$storekeeper = $row['last_name'].' '.$row['first_name'];
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
$status = $row['status'];
$comment = $row['comment'];
?>
<!DOCTYPE html>
<html>
    <head>
        <?php
        include '../include/head.php';
        ?>
        <style>
            @media print{@page {size: landscape}}
        </style>
    </head>
    <body class="print">
        <div style="margin-left: 20px;">
            <div class="backlink d-print-none" style="margin-bottom: 56px;">
                <a href="<?=APPLICATION ?>/pallet/"><i class="fas fa-chevron-left"></i>&nbsp;Назад</a>
            </div>
            <h1 style="font-size: 26px; margin-top: 10px; margin-bottom: 30px;">Паллет №<?=$id ?> от <?=$date ?></h1>
            <table>
                <tr>
                    <td>
                        <table class="table table-bordered" style="width: 400px;">
                            <tbody>
                                <tr><td colspan="2"><strong>Поставщик</strong><br /><?=$supplier ?></td></tr>
                                <tr><td colspan="2"><strong>ID от поставщика</strong><br/><?=$id_from_supplier ?></td></tr>
                                <tr><td colspan="2"><strong>Кладовщик</strong><br /><?=$storekeeper ?></td></tr>
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
                                    <td><strong>Количество рулонов</strong><br/><?=$rolls_number ?></td>
                                    <td><strong>Ячейка на складе</strong><br/><?=$cell ?></td>
                                </tr>
                                <tr><td colspan="2"><strong>Статус</strong><br/><?=$status ?></td></tr>
                                <tr><td colspan="2"><strong>Комментарий</strong><br/><?= $comment ?></td></tr>
                            </tbody>
                        </table>
                    </td>
                    <td style="vertical-align: top; padding-left: 50px;">
                        <?php
                        include '../qr/qrlib.php';
                        $errorCorrectionLevel = 'L'; // 'L','M','Q','H'
                        $data = $_SERVER['REQUEST_SCHEME'].'://'.$_SERVER['HTTP_HOST'].APPLICATION.'/pallet/pallet.php?id='.$id;
                        $current_date_time = date("dmYHis");
                        $filename = "../temp/$current_date_time.png";
                        QRcode::png(htmlspecialchars($data), $filename, $errorCorrectionLevel, 5, 2, true);
                        echo "<img src='$filename' />";
            
                        // Удаление всех файлов, кроме текущего (чтобы диск не переполнился).
                        $files = scandir("../temp/");
                        foreach ($files as $file) {
                            if($file != "$current_date_time.png" && !is_dir($file)) {
                                unlink("../temp/$file");
                            }
                        }
                        ?>
                    </td>
                </tr>
            </table>
        </div>
    </body>
</html>
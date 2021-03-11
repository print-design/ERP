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
        . "left join supplier s on p.supplier_id = s.id "
        . "left join film_brand fb on p.film_brand_id = fb.id "
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

// Определяем удельный вес
$ud_ves = null;
$sql = "select weight from film_brand_variation where film_brand_id=$film_brand_id and thickness=$thickness";
$fetcher = new Fetcher($sql);
if($row = $fetcher->Fetch()) {
    $ud_ves = $row[0];
}
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
        <div style="margin-left: 30px;">
            <div style="margin-bottom: 20px; margin-top: 30px;"><a href="<?=APPLICATION ?>/pallet/pallet.php?id=<?=$id ?>"><i class="fas fa-chevron-left"></i>&nbsp;Назад</a></div>
            <table>
                <tr>
                    <td>
                        <table class="table table-bordered print" style="width: 400px;">
                            <tbody>
                                <tr>
                                    <td><strong>Поставщик</strong><br /><?=$supplier ?></td>
                                    <td><strong>Ширина</strong><br /><?=$width ?> мм</td>
                                </tr>
                                <tr>
                                    <td><strong>ID от поставщика</strong><br /><?=$id_from_supplier ?></td>
                                    <td><strong>Толщина, уд.вес</strong><br /><?=$thickness ?> мкм <?=$ud_ves ?> г/м<sup>2</sup></td>
                                </tr>
                                <tr>
                                    <td><strong>Кладовщик</strong><br /><?=$storekeeper ?></td>
                                    <td><strong>Длина</strong><br /><?=$length ?> м</td>
                                </tr>
                                <tr>
                                    <td><strong>Марка пленки</strong><br /><?=$film_brand ?></td>
                                    <td><strong>Масса нетто</strong><br /><?=$net_weight ?> кг</td>
                                </tr>
                                <tr>
                                    <td><strong>Статус</strong><br /><?=$status ?></td>
                                    <td><strong>Ячейка на складе</strong><br /><?=$cell ?></td>
                                </tr>
                                <tr>
                                    <td colspan="2"><strong>Комментарий</strong><br /><?= $comment ?></td>
                                </tr>
                            </tbody>
                        </table>
                    </td>
                    <td style="vertical-align: top; padding-left: 50px;">
                        <h1 style="font-size: 26px;">Паллет №<?=$id ?> от <?=$date ?></h1>
                        <?php
                        include '../qr/qrlib.php';
                        $errorCorrectionLevel = 'L'; // 'L','M','Q','H'
                        $data = $_SERVER['REQUEST_SCHEME'].'://'.$_SERVER['HTTP_HOST'].APPLICATION.'/pallet/pallet.php?id='.$id;
                        $current_date_time = date("dmYHis");
                        $filename = "../temp/$current_date_time.png";
                        QRcode::png(addslashes($data), $filename, $errorCorrectionLevel, 10, 4, true);
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
<?php
include '../include/topscripts.php';


// Если не задано значение id, перенаправляем на список
$id = filter_input(INPUT_GET, 'id');
if(empty($id)) {
    header('Location: '.APPLICATION.'/roll/');
}

// Получение данных
$sql = "select r.date, r.storekeeper_id, u.last_name, u.first_name, r.supplier_id, s.name supplier, r.id_from_supplier, "
        . "r.film_brand_id, fb.name film_brand, r.width, r.thickness, r.length, "
        . "r.net_weight, r.cell, "
        . "(select rs.name status from roll_status_history rsh left join roll_status rs on rsh.status_id = rs.id where rsh.roll_id = r.id order by rsh.id desc limit 0, 1) status, "
        . "r.comment "
        . "from roll r "
        . "left join user u on r.storekeeper_id = u.id "
        . "left join supplier s on r.supplier_id = s.id "
        . "left join film_brand fb on r.film_brand_id = fb.id "
        . "where r.id=$id";

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
        <div style="margin-left: 20px;">
            <div class="backlink d-print-none" style="margin-bottom: 56px;">
                <a href="<?=APPLICATION ?>/roll/roll.php?id=<?=$id ?>"><i class="fas fa-chevron-left"></i>&nbsp;Назад</a>
            </div>
            <h1 style="font-size: 26px; margin-top: 10px; margin-bottom: 30px;">Рулон №<?=$id ?> от <?=$date ?></h1>
            <table>
                <tr>
                    <td>
                        <table class="table table-bordered" style="width: 400px;">
                            <tbody>
                                <tr><td colspan="2"><strong>Поставщик</strong><br/><?=$supplier ?></td></tr>
                                <tr><td colspan="2"><strong>ID от поставщика</strong><br/><?=$id_from_supplier ?></td></tr>
                                <tr><td colspan="2"><strong>Кладовщик</strong><br /><?=$storekeeper ?></td></tr>
                                <tr><td colspan="2"><strong>Марка пленки</strong><br/><?=$film_brand ?></td></tr>
                                <tr>
                                    <td><strong>Ширина</strong><br/><?=$width ?> мм</td>
                                    <td><strong>Толщина</strong><br/><?=$thickness ?> мкм <?=$ud_ves ?> г/м<sup>2</sup></td>
                                </tr>
                                <tr>
                                    <td><strong>Длина</strong><br/><?=$length ?> м</td>
                                    <td><strong>Масса нетто</strong><br/><?=$net_weight ?> кг</td>
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
                        $data = $_SERVER['REQUEST_SCHEME'].'://'.$_SERVER['HTTP_HOST'].APPLICATION.'/roll/roll.php?id='.$id;
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
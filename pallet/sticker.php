<?php
include '../include/topscripts.php';

// Авторизация
if(!IsInRole(array('technologist', 'dev', 'storekeeper', 'manager'))) {
    header('Location: '.APPLICATION.'/unauthorized.php');
}

// Получение данных
session_start();
$data = $_SESSION['formdata'];

$inner_id = $data['inner_id'];
$date = $data['date'];
$supplier_id = $data['supplier_id'];
$id_from_supplier = $data['id_from_supplier'];
$film_brand_id = $data['film_brand_id'];
$width = $data['width'];
$thickness = $data['thickness'];
$length = $data['length'];
$net_weight = $data['net_weight'];
$rolls_number = $data['rolls_number'];
$cell = $data['cell'];
$status_id = $data['status_id'];
$comment = $data['comment'];

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
            <h1 style="font-size: 26px; margin-top: 10px; margin-bottom: 30px;">Паллет №<?=$inner_id ?> от <?=$date ?></h1>
            <table>
                <tr>
                    <td>
                        <table class="table table-bordered" style="width: 400px;">
                            <tbody>
                                <tr><td colspan="2"><strong>Поставщик</strong><br /><?=$supplier ?></td></tr>
                                <tr><td colspan="2"><strong>ID от поставщика</strong><br/><?=$id_from_supplier ?></td></tr>
                                <tr>
                                    <td colspan="2">
                                        <strong>Кладовщик</strong>
                                        <br />
                                        <?php
                                        $storekeeper = filter_input(INPUT_POST, 'storekeeper');
                                        if(null === $storekeeper) {
                                            echo filter_input(INPUT_COOKIE, LAST_NAME);
                                            
                                            if(!empty(filter_input(INPUT_COOKIE, LAST_NAME)) && !empty(filter_input(INPUT_COOKIE, FIRST_NAME))) {
                                                echo ' ';
                                            }
                                            
                                            echo filter_input(INPUT_COOKIE, FIRST_NAME);
                                        }
                                        else {
                                            echo $storekeeper;;
                                        }
                                        ?>
                                    </td>
                                </tr>
                                <tr><td colspan="2"><strong><strong>Марка пленки</strong><br/><?=$film_brand ?></strong></td></tr>
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
                        $data = $_SERVER['REQUEST_SCHEME'].'://'.$_SERVER['HTTP_HOST'].APPLICATION.'/pallet/details.php?inner_id='.$inner_id;
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
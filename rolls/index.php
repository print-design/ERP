<?php
include '../include/topscripts.php';
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
        <div class="container-fluid">
            <?php
            if(isset($error_message) && $error_message != '') {
               echo "<div class='alert alert-danger'>$error_message</div>";
            }
            ?>
            <div class="d-flex justify-content-between mb-2">
                <div class="p-1">
                    <div style="float:left; margin-right: 10px;">
                        <h1>Ролики</h1>
                    </div>
                    <a href="#" title="Оставить заявку на раскрой" class="btn btn-dark" style="height:65px;">
                        <div style="float:left; padding-top: 12px;">+&nbsp;&nbsp;</div>
                        Оставить заявку<br />на раскрой
                    </a>
                    <a href="#" title="Оставить заявку на печать" class="btn btn-dark" style="height:65px;">
                        <div style="float:left; padding-top: 12px;">+&nbsp;&nbsp;</div>
                        Оставить заявку<br />на печать
                    </a>
                </div>
                <div class="p-1">
                    <a href="<?=APPLICATION ?>/rolls/create.php" title="Новый ролик" class="btn btn-outline-dark" style="height:65px; padding-top: 18px;">+&nbsp;&nbsp;Новый ролик</a>
                    <a href="<?=APPLICATION ?>/rolls/pallet.php" title="Новый паллет" class="btn btn-outline-dark" style="height:65px; padding-top: 18px;">+&nbsp;&nbsp;Новый паллет</a>
                    <a href="#" title="Фильтр" class="btn btn-outline-dark" style="height:65px; padding-top: 18px;"><img src="<?=APPLICATION ?>/images/icons/Filter-2-icon.png">&nbsp;&nbsp;Фильтр</a>
                </div>
            </div>
            <table class="table">
                <thead>
                    <tr>
                        <th>Дата создания</th>
                        <th>Марка пленки</th>
                        <th>Толщина</th>
                        <th>Ширина</th>
                        <th>Вес</th>
                        <th>Длина</th>
                        <th>Поставщик</th>
                        <th>ID поставщика</th>
                        <th>ID ролля</th>
                        <th>№ ячейки</th>
                        <th>Менеджер</th>
                        <th>Статус</th>
                        <th>Комментарий</th>
                        <th></th>
                        <th></th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $conn = new mysqli('localhost', 'root', '', 'erp');
                    $sql = "select r.date, rm.name model, r.width, r.thickness, r.weight, r.pallet_weight, r.length, r.pallet_length, 
                        rs.name supplier, r.supplier_id, r.id, r.cell, u.last_name user, rst.name status, r.comment, r.edited 
                        from `roll` r 
                        inner join roll_model rm on r.model_id = rm.id 
                        inner join roll_supplier rs on r.supplier_id = rs.id 
                        inner join `user` u on r.user_id = u.id 
                        inner join roll_status rst on r.status_id = rst.id 
                        order by r.id desc";
                    
                    if($conn->connect_error) {
                        die('Ошибка соединения: ' . $conn->connect_error);
                    }
                    $result = $conn->query($sql);
                    if ($result->num_rows > 0) {
                        while($row = $result->fetch_assoc()) {
                            echo "<tr>"
                                    ."<td>".$row['date']."</td>"
                                    ."<td>".$row['model']."</td>"
                                    ."<td>".$row['width']."</td>"
                                    ."<td>".$row['thickness']."</td>"
                                    ."<td>".$row['weight']."</td>"
                                    ."<td>".$row['length']."</td>"
                                    ."<td>".$row['supplier']."</td>"
                                    ."<td>".$row['supplier_id']."</td>"
                                    ."<td>".$row['id']."</td>"
                                    ."<td>".$row['cell']."</td>"
                                    ."<td>".$row['user']."</td>"
                                    ."<td>".$row['status']."</td>"
                                    ."<td>".$row['comment']."</td>"
                                    ."<td><a href='edit.php?id=".$row['id']."'>Редактировать</td>"
                                    ."<td>".($row['edited'] == 1 ? '&#10003;' : '')."</td>"
                                    ."<td><a href='details.php?id=".$row['id']."'>&bullet;&bull;&bull;</td></td>"
                                    ."</tr>";
                        }
                    }
                    $conn->close();
                    ?>
                </tbody>
            </table>
        </div>
        <?php
        include '../include/footer.php';
        ?>
    </body>
</html>
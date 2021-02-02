<?php
include '../include/topscripts.php';

// Авторизация
if(!IsInRole(array('admin', 'dev', 'technologist', 'storekeeper'))) {
    header('Location: '.APPLICATION.'/unauthorized.php');
}
?>
<!DOCTYPE html>
<html>
    <head>
        <?php
        include '../include/head.php';
        ?>
        <link href="<?=APPLICATION ?>/css/jquery-ui.css" rel="stylesheet"/>
    </head>
    <body>
        <?php
        include '../include/header.php';
        include '../include/pager_top.php';
        ?>
        <div class="container-fluid">
            <?php
            if(!empty($error_message)) {
                echo "<div class='alert alert-danger'>$error_message</div>";
            }
            ?>
            <div class="d-flex justify-content-between mb-auto">
                <div class="p-1">
                    <h1 style="font-size: 32px; line-height: 48px; font-weight: 600;">Паллеты</h1>
                </div>
                <div class="p-1">
                    <a href="create.php" class="btn btn-outline-dark" style="margin-right: 12px; padding-left: 33px; padding-right: 44px;"><i class="fas fa-plus" style="font-size: 10px; margin-right: 18px;"></i>Новый паллет</a>
                    <button class="btn btn-outline-dark" data-toggle="modal" data-target="#filterModal" data-text="Фильтр" style="padding-left: 14px; padding-right: 42px; padding-bottom: 14px; padding-top: 14px;"><img src="../images/icons/filter.svg" style="margin-right: 20px;" />Фильтр</button>
                </div>
            </div>
            <table class="table">
                <thead>
                    <tr style="border-top: 1px solid #dee2e6; border-left: 1px solid #dee2e6; border-right: 1px solid #dee2e6;">
                        <th><input type="checkbox" class="form-check" /></th>
                        <th>Дата прихода</th>
                        <th>Марка пленки</th>
                        <th>Толщина</th>
                        <th>Ширина</th>
                        <th>Вес</th>
                        <th>Длина</th>
                        <th>Поставщик</th>
                        <th>ID поставщика</th>
                        <th>ID паллета</th>
                        <th>Кол-во рулонов</th>
                        <th>№ ячейки</th>
                        <th>Кто заказал</th>
                        <th>Статус</th>
                        <th>Комментарий</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $sql = "select p.id, p.date, fb.name film_brand, p.width, p.thickness, p.net_weight, p.length, "
                            . "s.name supplier, p.id_from_supplier, p.inner_id, p.rolls_number, p.cell, u.first_name, u.last_name, "
                            . "st.name status, p.comment "
                            . "from pallet p "
                            . "inner join film_brand fb on p.film_brand_id = fb.id "
                            . "inner join supplier s on p.supplier_id = s.id "
                            . "inner join user u on p.manager_id = u.id "
                            . "inner join status st on p.status_id = st.id "
                            . "order by p.id desc limit $pager_skip, $pager_take";
                    $fetcher = new Fetcher($sql);
                    
                    while ($row = $fetcher->Fetch()):
                    ?>
                    <tr style="border-left: 1px solid #dee2e6; border-right: 1px solid #dee2e6;">
                        <td><input type="checkbox" id="" name="" class="form-check" /></td>
                        <td><?= date_create_from_format("Y-m-d", $row['date'])->format("d.m.Y") ?></td>
                        <td><?= $row['film_brand'] ?></td>
                        <td><?= $row['thickness'] ?></td>
                        <td><?= $row['width'] ?></td>
                        <td><?= $row['net_weight'] ?></td>
                        <td><?= $row['length'] ?></td>
                        <td><?= $row['supplier'] ?></td>
                        <td><?= $row['id_from_supplier'] ?></td>
                        <td><?= $row['inner_id'] ?></td>
                        <td><?= $row['rolls_number'] ?></td>
                        <td><?= $row['cell'] ?></td>
                        <td><?= $row['last_name'].' '.$row['first_name'] ?></td>
                        <td><?= $row['status'] ?></td>
                        <td><?= htmlentities($row['comment']) ?></td>
                        <td></td>
                    </tr>
                    <?php
                    endwhile;
                    ?>
                </tbody>
            </table>
            <?php
            include '../include/pager_bottom.php';
            ?>
        </div>
        <?php
        include '../include/footer.php';
        ?>
        <div class="modal fade" id="filterModal">
            <div class="modal-dialog">
                <div class="modal-content" style="width: 535px; padding-left: 35px; padding-right: 35px;">
                    <button type="button" class="close" data-dismiss="modal" style="position: absolute; right: 15px; top: 5px;">&times;</button>
                    <h1>Фильтр</h1>
                    <form method="get">
                        <h2>Статус</h2>
                        <div class="form-group form-check">
                            <input type="checkbox" class="form-check-input" id="chkPrint" name="chkPrint" />
                            <label class="form-check-label" for="chkPrint">В печать</label>
                        </div>
                        <div class="form-group form-check">
                            <input type="checkbox" class="form-check-input" id="chkUnavailable" name="chkUnavailable" />
                            <label class="form-check-label" for="chkUnavailable">Не доступен для раскроя</label>
                        </div>
                        <div class="form-group form-check">
                            <input type="checkbox" class="form-check-input" id="chkFree" name="chkFree" />
                            <label class="form-check-label" for="chkFree">Свободен</label>
                        </div>
                        <div class="form-group form-check">
                            <input type="checkbox" class="form-check-input" id="chkPartial" name="chkPartial" />
                            <label class="form-check-label" for="chkPartial">Частично свободен</label>
                        </div>
                        <div class="form-group form-check">
                            <input type="checkbox" class="form-check-input" id="chkReserved" name="chkReserved" />
                            <label class="form-check-label" for="chkReserved">Забронирован</label>
                        </div>
                        <div class="form-group form-check">
                            <input type="checkbox" class="form-check-input" id="chkReturn" name="chkReturn" />
                            <label class="form-check-label" for="chkReturn">На возврат</label>
                        </div>
                        <div class="form-group">
                            <select id="film_brand" class="form-control" name="film_brand" style="background-color: #8B90A0; color: white;">
                                <option value="">МАРКА ПЛЕНКИ</option>
                                <?php
                                $film_brands = (new Grabber("select distinct fb.id, fb.name from pallet p inner join film_brand fb on p.film_brand_id = fb.id order by fb.name"))->result;
                                foreach ($film_brands as $film_brand) {
                                    $id = $film_brand['id'];
                                    $name = $film_brand['name'];
                                    echo "<option value='$id'>$name</option>";
                                }
                                ?>
                            </select>
                        </div>
                        <h2>Толщина</h2>
                        <div id="width_slider" style="width: 465px;">
                            <label for="amount">Price range:</label>
                            <input type="text" id="amount" readonly style="border:0; color:#f6931f; font-weight:bold;">
                            <div id="width_slider_values" style="height: 50px;">
                                <div style="position: absolute; bottom: 10px; left: 0;">8 мкм</div>
                                <div style="position: absolute; bottom: 10px;  left: <?=(80 - 8) * 50 / 535 ?>px;">50</div>
                                <div style="position: absolute; bottom: 10px; right: 0;">80 мкм</div>
                            </div>
                            <div id="slider-range"></div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <?php
        include '../include/footer.php';
        ?>
        <script src="<?=APPLICATION ?>/js/jquery-ui.js"></script>
        <script>
            $( function() {
                $( "#slider-range" ).slider({
                    range: true,
                    min: 8,
                    max: 80,
                    values: [ 20, 50 ],
                    slide: function( event, ui ) {
                        $("#amount").val(ui.values[ 0 ] + "мкм" + " - " + ui.values[ 1 ] + "мкм");
                    }
                });
                $("#amount").val($("#slider-range").slider("values", 0) + "мкм" +
                        " - " + $("#slider-range").slider("values", 1) + "мкм");
            });
        </script>
    </body>
</html>
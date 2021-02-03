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
                        <th><input type="checkbox" class="form-check" id="chkMain" /></th>
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
                    $where = '';
                    
                    $film_brand_id = filter_input(INPUT_GET, 'film_brand_id');
                    if(!empty($film_brand_id)) {
                        if(!empty($where)) {
                            $where = "$where and ";
                        }
                        $where .= "film_brand_id = $film_brand_id";
                    }
                    
                    $thickness_from = filter_input(INPUT_GET, 'thickness_from');
                    if(!empty($thickness_from)) {
                        if(!empty($where)) {
                            $where = "$where and ";
                        }
                        $where .= "thickness >= ".$thickness_from;
                    }
                    
                    $thickness_to = filter_input(INPUT_GET, 'thickness_to');
                    if(!empty($thickness_to)) {
                        if(!empty($where)) {
                            $where = "$where and ";
                        }
                        $where .= "thickness <= $thickness_to";
                    }
                    
                    $width_from = filter_input(INPUT_GET, 'width_from');
                    if(!empty($width_from)) {
                        if(!empty($where)) {
                            $where = "$where and ";
                        }
                        $where .= "width >= $width_from";
                    }
                    
                    $width_to = filter_input(INPUT_GET, 'width_to');
                    if(!empty($width_to)) {
                        if(!empty($where)) {
                            $where = "$where and ";
                        }
                        $where .= "width <= $width_to";
                    }
                    
                    $statuses = array();
                    
                    if(filter_input(INPUT_GET, 'chkPrint') == 'on') {
                        array_push($statuses, 1); // В печать
                    }
                    
                    if(filter_input(INPUT_GET, 'chkCut') == 'on') {
                        array_push($statuses, 2); // На раскрой
                    }
                    
                    if(filter_input(INPUT_GET, 'chkFree') == 'on') {
                        array_push($statuses, 3); // Свободен
                    }
                    
                    $strStatuses = implode(", ", $statuses);
                    
                    if(!empty($strStatuses)) {
                        if(!empty($where)) {
                            $where = "$where and ";
                        }
                        $where .= "status_id in ($strStatuses)";
                    }
                    
                    if(!empty($where)) {
                        $where = "where $where ";
                    }
                    
                    $sql = "select p.id, p.date, fb.name film_brand, p.width, p.thickness, p.net_weight, p.length, "
                            . "s.name supplier, p.id_from_supplier, p.inner_id, p.rolls_number, p.cell, u.first_name, u.last_name, "
                            . "st.name status, p.comment "
                            . "from pallet p "
                            . "inner join film_brand fb on p.film_brand_id = fb.id "
                            . "inner join supplier s on p.supplier_id = s.id "
                            . "inner join user u on p.manager_id = u.id "
                            . "inner join pallet_status st on p.status_id = st.id "
                            . $where
                            . "order by p.id desc limit $pager_skip, $pager_take";
                    $fetcher = new Fetcher($sql);
                    
                    while ($row = $fetcher->Fetch()):
                    ?>
                    <tr style="border-left: 1px solid #dee2e6; border-right: 1px solid #dee2e6;">
                        <td><input type="checkbox" id="" name="" class="form-check chkPallet" /></td>
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
                        <td><a class="black" href="<?=APPLICATION ?>/pallet/details.php?id=<?=$row['id'] ?>"><i class="fas fa-ellipsis-h"></i></a></td>
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
                    <button type="button" class="close" data-dismiss="modal" style="position: absolute; right: 32px; top: 55px;">&times;</button>
                    <h1 style="margin-top: 53px; margin-bottom: 20px; font-size: 32px; line-height: 48px; font-weight: 600;">Фильтр</h1>
                    <form method="get">
                        <h2 style="font-size: 24px; line-height: 32px; font-weight: 600; margin-bottom: 24px;">Статус</h2>
                        <div class="form-group form-check">
                            <input type="checkbox" class="form-check-input" id="chkPrint" name="chkPrint"<?= filter_input(INPUT_GET, 'chkPrint') == 'on' ? " checked='checked'" : "" ?> />
                            <label class="form-check-label" for="chkPrint">В печать</label>
                        </div>
                        <div class="form-group form-check">
                            <input type="checkbox" class="form-check-input" id="chkCut" name="chkCut"<?= filter_input(INPUT_GET, 'chkCut') == 'on' ? " checked='checked'" : "" ?> />
                            <label class="form-check-label" for="chkCut">На раскрой</label>
                        </div>
                        <div class="form-group form-check">
                            <input type="checkbox" class="form-check-input" id="chkFree" name="chkFree"<?= filter_input(INPUT_GET, 'chkFree') == 'on' ? " checked='checked'" : "" ?> />
                            <label class="form-check-label" for="chkFree">Свободен</label>
                        </div>
                        <div class="form-group">
                            <select id="film_brand_id" name="film_brand_id" class="form-control" style="background-color: #8B90A0; color: white; margin-top: 30px; margin-bottom: 30px;">
                                <option value="">МАРКА ПЛЕНКИ</option>
                                <?php
                                $film_brands = (new Grabber("select distinct fb.id, fb.name from pallet p inner join film_brand fb on p.film_brand_id = fb.id order by fb.name"))->result;
                                foreach ($film_brands as $film_brand) {
                                    $id = $film_brand['id'];
                                    $name = $film_brand['name'];
                                    $selected = '';
                                    if(filter_input(INPUT_GET, 'film_brand_id') == $film_brand['id']) $selected = " selected='selected'";
                                    echo "<option value='$id'$selected>$name</option>";
                                }
                                ?>
                            </select>
                        </div>
                        <h2 style="font-size: 24px; line-height: 32px; font-weight: 600;">Толщина</h2>
                        <div id="width_slider" style="width: 465px;">
                            <div id="width_slider_values" style="height: 50px; position: relative; font-size: 14px; line-height: 18px;">
                                <div style="position: absolute; bottom: 10px; left: 0;">8 мкм</div>
                                <div style="position: absolute; bottom: 10px;  left: <?=(465 * 10 / 72) + 6 ?>px;">20</div>
                                <div style="position: absolute; bottom: 10px;  left: <?=(465 * 20 / 72) + 6 ?>px;">30</div>
                                <div style="position: absolute; bottom: 10px;  left: <?=(465 * 30 / 72) + 6 ?>px;">40</div>
                                <div style="position: absolute; bottom: 10px;  left: <?=(465 * 40 / 72) + 6 ?>px;">50</div>
                                <div style="position: absolute; bottom: 10px;  left: <?=(465 * 50 / 72) + 6 ?>px;">60</div>
                                <div style="position: absolute; bottom: 10px;  left: <?=(465 * 60 / 72) + 6 ?>px;">70</div>
                                <div style="position: absolute; bottom: 10px; right: -7px;">80</div>
                                <div style="position: absolute; bottom: 10px; right: -34px;">мкм</div>
                            </div>
                            <div id="slider-range"></div>
                        </div>
                        <input type="hidden" id="thickness_from" name="thickness_from" />
                        <input type="hidden" id="thickness_to" name="thickness_to" />
                        <h2 style="font-size: 24px; line-height: 32px; font-weight: 600; margin-top: 43px; margin-bottom: 18px;">Ширина</h2>
                        <div class="row">
                            <div class="col-5 form-group">
                                <label for="width_from">От</label>
                                <input type="number" min="1" id="width_from" name="width_from" class="form-control" value="<?= filter_input(INPUT_GET, 'width_from') ?>" />
                            </div>
                            <div class="col-2 text-center" style="padding-top: 30px;"><strong>&ndash;</strong></div>
                            <div class="col-5">
                                <label for="width_to">До</label>
                                <input type="number" min="1" id="width_to" name="width_to" class="form-control" value="<?= filter_input(INPUT_GET, 'width_to') ?>" />
                            </div>
                        </div>
                        <button type="submit" class="btn btn-dark" id="filter_submit" name="filter_submit" style="margin-top: 20px; margin-bottom: 35px;">Применить</button>
                    </form>
                </div>
            </div>
        </div>
        <?php
        include '../include/footer.php';
        ?>
        <script src="<?=APPLICATION ?>/js/jquery-ui.js"></script>
        <script>
            var slider_start_from = <?= null === filter_input(INPUT_GET, 'thickness_from') ? "20" : filter_input(INPUT_GET, 'thickness_from') ?>;
            var slider_start_to = <?= null === filter_input(INPUT_GET, 'thickness_to') ? "50" : filter_input(INPUT_GET, 'thickness_to') ?>;
            
            $( "#slider-range" ).slider({
                range: true,
                min: 8,
                max: 80,
                values: [slider_start_from, slider_start_to],
                slide: function(event, ui) {
                    $("#thickness_from").val(ui.values[0]);
                    $("#thickness_to").val(ui.values[1]);
                }
            });
            
            $("#thickness_from").val(slider_start_from);
            $("#thickness_to").val(slider_start_to);
            
            $('#chkMain').change(function(){
                if($(this).is(':checked')) {
                    $('.chkPallet').prop('checked', true);
                }
                else {
                    $('.chkPallet').prop('checked', false);
                }
            });
            
            $('.chkPallet').change(function(){
                $('#chkMain').prop('checked', false);
            });
        </script>
    </body>
</html>
<?php
include '../include/topscripts.php';

// Авторизация
if(!IsInRole(array('admin', 'dev', 'technologist', 'storekeeper'))) {
    header('Location: '.APPLICATION.'/unauthorized.php');
}

// Обработка отправки формы
if(null !== filter_input(INPUT_POST, 'delete-film-submit')) {
    $id = filter_input(INPUT_POST, 'id');
    $type = filter_input(INPUT_POST, 'type');
    
    $sql_history = '';
    $sql = '';
    
    switch ($type) {
        case 'pallet':
            $sql_history = "delete from pallet_status_history where pallet_id = $id";
            $sql = "delete from pallet where id = $id";
            break;
        case 'roll':
            $sql_history = "delete from roll_status_history where roll_id = $id";
            $sql = "delete from roll where id = $id";
            break;
    }
    
    if(!empty($sql)) {
        $error_message = (new Executer($sql_history))->error;
        
        if(empty($error_message)) {
            $error_message = (new Executer($sql))->error;
        }
    }
}

// СТАТУС "СРАБОТАННЫЙ" ДЛЯ ПАЛЛЕТА
$utilized_status_pallet_id = 4;

// СТАТУС "СРАБОТАННЫЙ" ДЛЯ РУЛОНА
$utilized_status_roll_id = 2;+

// Получение общей массы сработанной пленки
$total_weight = 0;
$pallet_row = (new Fetcher("select sum(p.net_weight) total_weight from pallet p left join (select * from pallet_status_history where id in (select max(id) from pallet_status_history group by pallet_id)) psh on psh.pallet_id = p.id where psh.status_id = $utilized_status_pallet_id"))->Fetch();
$pallet_total_weight = $pallet_row['total_weight'];
$roll_row = (new Fetcher("select sum(r.net_weight) total_weight from roll r left join (select * from roll_status_history where id in (select max(id) from roll_status_history group by roll_id)) rsh on rsh.roll_id = r.id where rsh.status_id = $utilized_status_roll_id"))->Fetch();
$roll_total_weight = $roll_row['total_weight'];
$total_weight = intval($pallet_total_weight) + intval($roll_total_weight);
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
                    <table>
                        <tr>
                            <td><h1>Сработанная пленка</h1></td>
                            <td style="padding-left: 20px; padding-right: 20px; font-weight: bold;">(<?= number_format($total_weight, 0, ',', ' ') ?> кг)</td>
                        </tr>
                    </table>
                </div>
                <div class="p-1">
                    <button class="btn btn-outline-dark" data-toggle="modal" data-target="#filterModal" data-text="Фильтр" style="padding-left: 14px; padding-right: 42px; padding-bottom: 14px; padding-top: 14px;"><img src="../images/icons/filter.svg" style="margin-right: 20px;" />Фильтр</button>
                </div>
            </div>
            <table class="table">
                <thead>
                    <tr style="border-top: 1px solid #dee2e6; border-left: 1px solid #dee2e6; border-right: 1px solid #dee2e6;">
                        <th></th>
                        <th>Дата срабатывания</th>
                        <th>Марка пленки</th>
                        <th>Толщина</th>
                        <th>Ширина</th>
                        <th>Вес</th>
                        <th>Длина</th>
                        <th>Поставщик</th>
                        <th>ID поставщика</th>
                        <th>ID пленки</th>
                        <th>Кол-во рулонов</th>
                        <th>Кто заказал</th>
                        <th>Статус</th>
                        <th>Комментарий</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $where_pallet = "psh.status_id = $utilized_status_pallet_id";
                    $where_roll = "rsh.status_id = $utilized_status_roll_id";
                    
                    if(!empty(filter_input(INPUT_GET, 'chkPallet')) && filter_input(INPUT_GET, 'chkPallet') == 'on' && empty(filter_input(INPUT_GET, 'chkRoll'))) {
                        if(!empty($where_roll)) {
                            $where_roll .= " and ";
                        }
                        $where_roll .= "false";
                    }
                    
                    if(!empty(filter_input(INPUT_GET, 'chkRoll')) && filter_input(INPUT_GET, 'chkRoll') == 'on' && empty(filter_input(INPUT_GET, 'chkPallet'))) {
                        if(!empty($where_pallet)) {
                            $where_pallet .= " and ";
                        }
                        $where_pallet .= "false";
                    }
                    
                    $film_brand_id = filter_input(INPUT_GET, 'film_brand_id');
                    if(!empty($film_brand_id)) {
                        if(!empty($where_pallet)) {
                            $where_pallet .= " and ";
                        }
                        $where_pallet .= "p.film_brand_id = $film_brand_id";
                        
                        if(!empty($where_roll)) {
                            $where_roll .= " and ";
                        }
                        $where_roll .= "r.film_brand_id = $film_brand_id";
                    }
                    
                    $thickness_from = filter_input(INPUT_GET, 'thickness_from');
                    if(!empty($thickness_from)) {
                        if(!empty($where_pallet)) {
                            $where_pallet .= " and ";
                        }
                        $where_pallet .= "p.thickness >= $thickness_from";
                        
                        if(!empty($where_roll)) {
                            $where_roll .= " and ";
                        }
                        $where_roll .= "r.thickness >= $thickness_from";
                    }
                    
                    $thickness_to = filter_input(INPUT_GET, 'thickness_to');
                    if(!empty($thickness_to)) {
                        if(!empty($where_pallet)) {
                            $where_pallet .= " and ";
                        }
                        $where_pallet .= "p.thickness <= $thickness_to";
                        
                        if(!empty($where_roll)) {
                            $where_roll .= " and ";
                        }
                        $where_roll .= "r.thickness <= $thickness_to";
                    }
                    
                    $width_from = filter_input(INPUT_GET, 'width_from');
                    if(!empty($width_from)){
                        if(!empty($where_pallet)) {
                            $where_pallet .= " and ";
                        }
                        $where_pallet .= "p.width >= $width_from";
                        
                        if(!empty($where_roll)) {
                            $where_roll .= " and ";
                        }
                        $where_roll .= "r.width >= $width_from";
                    }
                    
                    $width_to = filter_input(INPUT_GET, 'width_to');
                    if(!empty($width_to)) {
                        if(!empty($where_pallet)) {
                            $where_pallet .= " and ";
                        }
                        $where_pallet .= "p.width <= $width_to";
                        
                        if(!empty($where_roll)) {
                            $where_roll .= " and ";
                        }
                        $where_roll .= "r.width <= $width_to";
                    }
                    
                    $sql = "select distinct id, name, colour from pallet_status";
                    $grabber = (new Grabber($sql));
                    $error_message = $grabber->error;
                    $pallet_statuses = $grabber->result;
                    
                    $sql = "select distinct id, name, colour from roll_status";
                    $grabber = (new Grabber($sql));
                    $error_message = $grabber->error;
                    $roll_statuses = $grabber->result;
                    
                    $pallet_statuses1 = array();
                    foreach ($pallet_statuses as $status) {
                        $pallet_statuses1[$status['id']] = $status;
                    }
                    
                    $roll_statuses1 = array();
                    foreach ($roll_statuses as $status) {
                        $roll_statuses1[$status['id']] = $status;
                    }
                    
                    if(!empty($where_pallet)) {
                        $where_pallet = " where $where_pallet";
                    }
                    
                    if(!empty($where_roll)) {
                        $where_roll = " where $where_roll";
                    }
                    
                    $sql = "select 'pallet' type, p.id id, psh.date date, fb.name film_brand, p.width width, p.thickness thickness, p.net_weight net_weight, p.length length, "
                            . "s.name supplier, p.id_from_supplier id_from_supplier, p.inner_id inner_id, p.rolls_number rolls_number, u.first_name first_name, u.last_name last_name, "
                            . "psh.status_id status_id, p.comment comment "
                            . "from pallet p "
                            . "left join film_brand fb on p.film_brand_id = fb.id "
                            . "left join supplier s on p.supplier_id = s.id "
                            . "left join user u on p.storekeeper_id = u.id "
                            . "left join (select * from pallet_status_history where id in (select max(id) from pallet_status_history group by pallet_id)) psh on psh.pallet_id = p.id "
                            . "$where_pallet "
                            . "union "
                            . "select 'roll' type, r.id id, rsh.date date, fb.name film_brand, r.width width, r.thickness thickness, r.net_weight net_weight, r.length length, "
                            . "s.name supplier, r.id_from_supplier id_from_supplier, r.inner_id inner_id, '-' rolls_number, u.first_name first_name, u.last_name last_name, "
                            . "rsh.status_id status_id, r.comment comment "
                            . "from roll r "
                            . "left join film_brand fb on r.film_brand_id = fb.id "
                            . "left join supplier s on r.supplier_id = s.id "
                            . "left join user u on r.storekeeper_id = u.id "
                            . "left join (select * from roll_status_history where id in (select max(id) from roll_status_history group by roll_id)) rsh on rsh.roll_id = r.id "
                            . "$where_roll "
                            . "order by id desc limit $pager_skip, $pager_take";
                    $fetcher = new Fetcher($sql);
                    
                    while ($row = $fetcher->Fetch()):

                    $status = '';
                    $colour_style = '';
                    
                    if($row['type'] == 'pallet') {
                        if(!empty($pallet_statuses1[$row['status_id']]['name'])) {
                            $status = $pallet_statuses1[$row['status_id']]['name'];
                        }
                        
                        if(!empty($pallet_statuses1[$row['status_id']]['colour'])) {
                            $colour = $pallet_statuses1[$row['status_id']]['colour'];
                            $colour_style = " color: $colour";
                        }
                    }
                    elseif ($row['type'] == 'roll') {
                        if(!empty($roll_statuses1[$row['status_id']]['name'])) {
                            $status = $roll_statuses1[$row['status_id']]['name'];
                        }
                        
                        if(!empty($roll_statuses1[$row['status_id']]['colour'])) {
                            $colour = $roll_statuses1[$row['status_id']]['colour'];
                            $colour_style = " color: $colour";
                        }
                    }
                    ?>
                    <tr style="border-left: 1px solid #dee2e6; border-right: 1px solid #dee2e6;">
                        <td><input type="checkbox" id="chk<?=$row['id'] ?>" name="chk<?=$row['id'] ?>" data-inner-id="<?=$row['inner_id'] ?>" class="form-check chkFilm" /></td>
                        <td><?= empty($row['date']) ? '' : date_create_from_format('Y-m-d', $row['date'])->format("d.m.Y") ?></td>
                        <td><?=$row['film_brand'] ?></td>
                        <td><?=$row['thickness'] ?></td>
                        <td><?=$row['width'] ?></td>
                        <td><?=$row['net_weight'] ?></td>
                        <td><?=$row['length'] ?></td>
                        <td><?=$row['supplier'] ?></td>
                        <td><?=$row['id_from_supplier'] ?></td>
                        <td><?=$row['inner_id'] ?></td>
                        <td><?=$row['rolls_number'] ?></td>
                        <td><?=$row['last_name'].' '.$row['first_name'] ?></td>
                        <td style="font-size: 10px; line-height: 14px; font-weight: 600;<?=$colour_style ?>"><?= mb_strtoupper($status) ?></td>
                        <td style="white-space: pre-wrap"><?= htmlentities($row['comment']) ?></td>
                        <td style="position: relative;">
                            <a class="black film_menu_trigger" href="javascript: void(0);"><i class="fas fa-ellipsis-h"></i></a>
                            <div class="film_menu">
                                <div class="command"><a href="<?=APPLICATION ?>/<?=$row['type'] ?>/details.php?inner_id=<?=$row['inner_id'] ?>">Просмотреть детали</a></div>
                                <?php
                                if(IsInRole(array('admin', 'dev', 'technologist'))):
                                ?>
                                <div class="command">
                                    <form method="post">
                                        <input type="hidden" id="id" name="id" value="<?=$row['id'] ?>" />
                                        <input type="hidden" id="scroll" name="scroll" />
                                        <input type="hidden" id="type" name="type" value="<?=$row['type'] ?>" />
                                        <button type="submit" class="btn btn-link confirmable" id="delete-film-submit" name="delete-film-submit" style="font-size: 14px;">Удалить</button>
                                    </form>
                                </div>
                                <?php
                                endif;
                                ?>
                            </div>
                        </td>
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
        <div class="modal fade" id="filterModal">
            <div class="modal-dialog">
                <div class="modal-content" style="width: 535px; padding-left: 35px; padding-right: 35px;">
                    <button type="button" class="close" data-dismiss="modal" style="position: absolute; right: 32px; top: 55px;">&times;</button>
                    <h1 style="margin-top: 53px; margin-bottom: 20px; font-size: 32px; line-height: 48px; font-weight: 600;">Фильтр</h1>
                    <form method="get">
                        <div class="form-group form-check">
                            <input type="checkbox" class="form-check-input" id="chkPallet" name="chkPallet"<?= filter_input(INPUT_GET, 'chkPallet') == 'on' ? " checked='checked'" : "" ?> />
                            <label class="form-check-label" for="chkPallet">Паллет</label>
                        </div>
                        <div class="form-group form-check">
                            <input type="checkbox" class="form-check-input" id="chkRoll" name="chkRoll"<?= filter_input(INPUT_GET, 'chkRoll') == 'on' ? " checked='checked'" : "" ?> />
                            <label class="form-check-label" for="chkRoll">Рулон</label>
                        </div>
                        <div class="form-group">
                            <select id="film_brand_id" name="film_brand_id" class="form-control" style="margin-top: 30px; margin-bottom: 30px;">
                                <option value="">МАРКА ПЛЕНКИ</option>
                                    <?php
                                    $film_brands = (new Grabber("select fb.id, fb.name from pallet p inner join film_brand fb on p.film_brand_id = fb.id union distinct select fb.id id, fb.name name1 from roll r inner join film_brand fb on r.film_brand_id = fb.id order by name"))->result;
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
                            <div class="col-5 form-group">
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
                if($(this).is(':checked')) {
                    $('.chkPallet').not($(this)).prop('checked', false);
                    $('#btn-cut-request').removeClass('disabled');
                    $('#btn-cut-request').attr('href', 'cut_request.php?inner_id=' + $(this).attr('data-inner-id'));
                    $('#btn-reserve-request').removeClass('disabled');
                    $('#btn-reserve-request').attr('href', 'reserve_request.php?inner_id=' + $(this).attr('data-inner-id'));
                    $('tr.selected').removeClass('selected');
                    $(this).closest('tr').addClass('selected');
                }
                else {
                    $('#btn-cut-request').addClass('disabled');
                    $('#btn-cut-request').removeAttr('href');
                    $('#btn-reserve-request').addClass('disabled');
                    $('#btn-reserve-request').removeAttr('href');
                    $(this).closest('tr').removeClass('selected');
                }
            });
            
            $('.film_menu_trigger').click(function() {
                var menu = $(this).next('.film_menu');
                $('.film_menu').not(menu).hide();
                menu.slideToggle();
            });
            
            $(document).click(function(e) {
                if($(e.target).closest($('.film_menu')).length || $(e.target).closest($('.film_menu_trigger')).length) return;
                $('.film_menu').slideUp();
            });
            
            // Прокрутка на прежнее место после отправки формы
            $(window).on("scroll", function(){
                $('input[name="scroll"]').val($(window).scrollTop());
            });
            
            <?php if(!empty($_REQUEST['scroll'])): ?>
                window.scrollTo(0, <?php echo intval($_REQUEST['scroll']); ?>);
            <?php endif; ?>
        </script>
    </body>
</html>
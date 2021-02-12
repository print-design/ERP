<?php
include '../include/topscripts.php';

// Авторизация
if(!IsInRole(array('admin', 'dev', 'technologist', 'storekeeper'))) {
    header('Location: '.APPLICATION.'/unauthorized.php');
}

// Обработка отправки формы
if(null !== filter_input(INPUT_POST, 'delete-utilized-submit')) {
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
$utilized_status_roll_id = 2;
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
                    <h1>Сработанная пленка</h1>
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
                                <div class="command"><a href="<?=APPLICATION ?>/pallet/details.php?inner_id=<?=$row['inner_id'] ?>">Просмотреть детали</a></div>
                                <div class="command">
                                    <form method="post">
                                        <input type="hidden" id="id" name="id" value="<?=$row['id'] ?>" />
                                        <input type="hidden" id="scroll" name="scroll" />
                                        <button type="submit" class="btn btn-link confirmable" id="delete-film-submit" name="delete-film-submit" style="font-size: 14px;">Удалить</button>
                                    </form>
                                </div>
                            </div>
                        </td>
                    </tr>
                    <?php
                    endwhile;
                    ?>
                </tbody>
            </table>
        </div>
        <?php
        include '../include/footer.php';
        ?>
        <script src="<?=APPLICATION ?>/js/jquery-ui.js"></script>
        <script>
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
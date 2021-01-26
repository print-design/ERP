<?php
include '../include/topscripts.php';

// Авторизация
if(!IsInRole(array('admin', 'dev', 'technologist'))) {
    header('Location: '.APPLICATION.'/unauthorized.php');
}

// Если не задано значение id, перенаправляем на список
if(empty(filter_input(INPUT_GET, 'id'))) {
    header('Location: '.APPLICATION.'/supplier/');
}

// Валидация формы
define('ISINVALID', ' is-invalid');
$form_valid = true;
$error_message = '';

// создание марки плёнки
$name_valid = '';

// создание вариации
$width_valid = '';
$weight_valid = '';

// Обработка отправки формы создания марки пленки
if(null !== filter_input(INPUT_POST, 'film_brand_create_submit')) {
    $name = filter_input(INPUT_POST, 'name');
    if(empty($name)) {
        $name_valid = ISINVALID;
        $form_valid = false;
    }
    
    if($form_valid) {
        $name = addslashes($name);
        $supplier_id = filter_input(INPUT_POST, 'supplier_id');
        $executer = new Executer("insert into film_brand (name, supplier_id) values ('$name', $supplier_id)");
        $error_message = $executer->error;
    }
}

// Обработка отправки формы создания вариации марки пленки
if(null !== filter_input(INPUT_POST, 'film_brand_variation_create_submit')) {
    $width = filter_input(INPUT_POST, 'width');
    if(empty($width)) {
        $width_valid = ISINVALID;
        $form_valid = false;
    }
    
    $weight = filter_input(INPUT_POST, 'weight');
    if(empty($weight)) {
        $weight_valid = ISINVALID;
        $form_valid = false;
    }
    
    if($form_valid) {
        $supplier_id = filter_input(INPUT_POST, 'supplier_id');
        $film_brand_id = filter_input(INPUT_POST, 'film_brand_id');
        $executer = new Executer("insert into film_brand_variation (film_brand_id, width, weight) values ($film_brand_id, $width, $weight)");
        $error_message = $executer->error;
    }
}

// Обработка отправки формы удаления марки
if(null !== filter_input(INPUT_POST, 'delete_brand_submit')) {
    $id = filter_input(INPUT_POST, 'id');
    $error_message = (new Executer("delete from film_brand where id=$id"))->error;
}

// Обработка отправки формы удаления вариации
if(null !== filter_input(INPUT_POST, 'delete_variation_submit')) {
    $id = filter_input(INPUT_POST, 'id');
    $film_brand_id = filter_input(INPUT_POST, 'film_brand_id');
    $supplier_id = filter_input(INPUT_POST, 'supplier_id');
    $error_message = (new Executer("delete from film_brand_variation where id=$id"))->error;
}

// Получение объекта
$row = (new Fetcher("select name from supplier where id=". filter_input(INPUT_GET, 'id')))->Fetch();
$name = htmlentities($row['name']);
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
            if(!empty($error_message)) {
                echo "<div class='alert alert-danger'>$error_message</div>";
            }
            ?>
            <div class="d-flex justify-content-between mb-2 nav2">
                <div class="p-1 row">
                    <div class="col-6">
                        <a href="<?=APPLICATION ?>/user/">Сотрудники</a>
                    </div>
                    <div class="col-6">
                        <a class="active" href="<?=APPLICATION ?>/supplier/">Поставщики</a>    
                    </div>
                </div>
                <div class="p-1"></div>
            </div>
            <div class="supplier-page" style="width: 472px;">
                <div class="backlink">
                    <a href="<?=APPLICATION ?>/supplier/"><i class="fas fa-chevron-left"></i>&nbsp;Назад</a>
                </div>
                <h1><?=$name ?></h1>
                <h2>Пленки</h2>
                <?php
                $film_brands = (new Grabber("select id, name from film_brand where supplier_id=". filter_input(INPUT_GET, 'id')." order by name"))->result;
                $film_brand_variations = (new Grabber("select v.id, v.film_brand_id, v.width, v.weight from film_brand_variation v inner join film_brand b on v.film_brand_id=b.id where b.supplier_id=". filter_input(INPUT_GET, 'id')." order by width, weight"))->result;

                foreach ($film_brands as $film_brand):
                ?>
                <div id="film_brand_<?=$film_brand['id'] ?>" style="transform: translateY(-70px);"></div>
                <table class="table film-table">
                    <?php
                    $current_film_brand_variations = array_filter($film_brand_variations, function($param) use($film_brand) { return $param['film_brand_id'] == $film_brand['id']; });
                    if(count($current_film_brand_variations) == 0):
                    ?>
                    <tr>
                        <td><?=$film_brand['name'] ?></td>
                        <td></td>
                        <td></td>
                        <!--td class="text-right">
                            <form method="post">
                                <input type="hidden" id="id" name="id" value="<?=$film_brand['id'] ?>"/>
                                <input type="hidden" id="scroll" name="scroll" />
                                <button type="submit" class="btn btn-link confirmable" id="delete_brand_submit" name="delete_brand_submit"><i class="fas fa-trash-alt"></i></button>
                        </form>
                        </td-->
                    </tr>
                    <?php
                    else:
                        $first = true;
                    foreach ($current_film_brand_variations as $current_film_brand_variation):
                    ?>
                    <tr>
                        <td><?=($first ? $film_brand['name'] : '') ?></td>
                        <td style="width:70px;"><?=$current_film_brand_variation['width'] ?></td>
                        <td style="width:70px;"><?=$current_film_brand_variation['weight'] ?></td>
                        <!--td class="text-right" style="width:50px;">
                            <form method="post">
                                <input type="hidden" id="id" name="id" value="<?=$current_film_brand_variation['id'] ?>"/>
                                <input type="hidden" id="film_brand_id" name="film_brand_id" value="<?=$film_brand['id'] ?>"/>
                                <input type="hidden" id="supplier_id" name="supplier_id" value="<?= filter_input(INPUT_GET, 'id') ?>"/>
                                <input type="hidden" id="scroll" name="scroll" />
                                <button type="submit" class="btn btn-link confirmable" id="delete_variation_submit" name="delete_variation_submit"><i class="fas fa-trash-alt"></i></button>
                            </form>                                
                        </td-->
                    </tr>
                    <?php
                    $first = false;
                    endforeach;
                    endif;
                    ?>
                    <tr style="border-bottom: 0;">
                        <td colspan="3" class="text-right" style="border-bottom: 0;">
                            <form method="post" class="form-inline add-variation-form">
                                <input type="hidden" id="supplier_id" name="supplier_id" value="<?= filter_input(INPUT_GET, 'id') ?>"/>
                                <input type="hidden" id="film_brand_id" name="film_brand_id" value="<?=$film_brand['id'] ?>"/>
                                <input type="hidden" id="scroll" name="scroll" />
                                <div class="form-group">
                                    <label for="width" class="mr-2">Толщина</label>
                                    <input type="number" min="1" step="1" max="999" id="width" name="width" required="required"/>
                                    <div class="invalid-feedback">Толщина обязательно</div>
                                </div>
                                <div class="form-group">
                                    <label for="weight" class="mr-2 ml-2">Удельный вес</label>
                                    <input type="number" min="1" step="0.1" max="999" id="weight" name="weight" required="required"/>
                                    <div class="invalid-feedback">Удельный вес обязательно</div>
                                </div>
                                <button type="submit" class="btn btn-dark ml-2" id="film_brand_variation_create_submit" name="film_brand_variation_create_submit"><i class="fas fa-plus"></i>&nbsp;Добавить</button>
                                <button class="btn btn-outline-dark ml-2 add-variation-cancel"><i class="fas fa-undo"></i></button>
                            </form>
                            <button class="btn btn-link add-variation-button"><i class="fas fa-plus" style="font-size: 8px; vertical-align: top; padding-top: 9px;"></i>&nbsp;Добавить</button>
                        </td>
                    </tr>
                </table>
                <?php endforeach; ?>
                    
                <form method="post" class="form-inline" id="add-brand-form">
                    <input type="hidden" id="supplier_id" name="supplier_id" value="<?= filter_input(INPUT_GET, 'id') ?>"/>
                    <input type="hidden" id="scroll" name="scroll" />
                    <div class="form-group">
                        <label for="name" class="mr-2">Марка пленки</label>
                        <input type="text" class="form-control mr-2" id="name" name="name" required="required"/>
                        <div class="invalid-feedback">Марка пленки обязательно</div>
                    </div>
                    <button type="submit" class="btn btn-dark" id="film_brand_create_submit" name="film_brand_create_submit"><i class="fas fa-plus"></i>&nbsp;Добавить</button>
                    <button class="btn btn-outline-dark ml-2" id="add-brand-cancel"><i class="fas fa-undo"></i>&nbsp;Отмена</button>
                </form>
                <button class="btn btn-outline-dark" id="add-brand-button"><i class="fas fa-plus"></i>&nbsp;Добавить марку пленки</button>
            </div>
        </div>
        <?php
        include '../include/footer.php';
        ?>
        <script>
            $('#add-brand-form').hide();
            
            $('#add-brand-button').click(function(){
                $(this).hide();
                $('#add-brand-form').show();
                $('#add-brand-form').find('input[id="name"]').focus();
            });
            
            $('#add-brand-cancel').click(function(){
                $('#add-brand-form').hide();
                $('#add-brand-button').show();
            });
            
            $('.add-variation-form').hide();
            
            $('.add-variation-button').click(function(){
                $(this).hide();
                var frm = $(this).prev('.add-variation-form');
                frm.show();
                frm.find('input[id="width"]').focus();
            });
            
            $('.add-variation-cancel').click(function(){
                var frm = $(this).parent();
                frm.hide();
                frm.next('.add-variation-button').show();
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
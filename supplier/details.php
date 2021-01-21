<?php
include '../include/topscripts.php';

// Авторизация
if(!IsInRole(array('admin', 'dev', 'technologist'))) {
    header('Location: '.APPLICATION.'/unauthorized.php');
}

// Если не задано значение id, перенаправляем на список
if(filter_input(INPUT_GET, 'id') == null) {
    header('Location: '.APPLICATION.'/supplier/');
}

// Валидация формы создания марки пленки
define('ISINVALID', ' is-invalid');
$form_valid = true;
$error_message = '';

$name_valid = '';

// Обработка отправки формы создания марки пленки
$film_brand_create_submit = filter_input(INPUT_POST, 'film_brand_create_submit');
if($film_brand_create_submit !== null) {
    $name = filter_input(INPUT_POST, 'name');
    if($name == '') {
        $name_valid = ISINVALID;
        $form_valid = false;
    }
    
    if($form_valid) {
        $name = addslashes($name);
        $supplier_id = filter_input(INPUT_POST, 'supplier_id');
        $executer = new Executer("insert into film_brand (name, supplier_id) values ('$name', $supplier_id)");
        
        if($error_message == '') {
            header('Location: '.APPLICATION."/supplier/details.php?id=$supplier_id");
        }
    }
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
            if(isset($error_message) && $error_message != '') {
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
            <div class="row">
                <div class="col-12 col-md-6 col-lg-4">
                    <a href="<?=APPLICATION ?>/supplier/"><i class="fas fa-chevron-left"></i>&nbsp;Назад</a>
                    <h1><?=$name ?></h1>
                    <h2>Пленки</h2>
                    <?php
                    $film_brands = (new Grabber("select name from film_brand where supplier_id=". filter_input(INPUT_GET, 'id')))->result;
                    $film_brand_variations = (new Grabber("select v.film_brand_id, v.width, v.weight from film_brand_variation v inner join film_brand b on v.film_brand_id=b.id where b.supplier_id=". filter_input(INPUT_GET, 'id')))->result;
                    foreach ($film_brands as $film_brand) {
                        echo "<p>".$film_brand['name']."</p>";
                        
                        foreach ($film_brand_variations as $film_brand_variation) {
                            echo '<ul>';
                            echo "<li>".$film_brand_variation['width']." ".$film_brand_variation['weight']."</li>";
                            echo '</ul>';
                        }
                    }
                    ?>
                    <form method="post" class="form-inline" id="add-brand-form">
                        <input type="hidden" id="supplier_id" name="supplier_id" value="<?= filter_input(INPUT_GET, 'id') ?>"/>
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
        </script>
    </body>
</html>
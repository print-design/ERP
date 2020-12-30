<?php
include '../include/topscripts.php';

// Авторизация
if(!IsInRole(array('admin', 'administrator', 'technologist'))) {
    header('Location: '.APPLICATION.'/unauthorized.php');
}

// Валидация формы
define('ISINVALID', ' is-invalid');
$form_valid = true;
$error_message = '';

$name_valid = '';

// Обработка отправки формы
$supplier_create_submit = filter_input(INPUT_POST, 'supplier_create_submit');
$film_type_create_submit = filter_input(INPUT_POST, 'film_type_create_submit');
if($supplier_create_submit !== null || $film_type_create_submit !== null) {
    $name = filter_input(INPUT_POST, 'name');
    if($name == '') {
        $name_valid = ISINVALID;
        $form_valid = false;
    }
    
    if($form_valid) {
        $name = addslashes($name);
        
        $executer = new Executer("insert into supplier (name) values ('$name')");
        $error_message = $executer->error;
        $id = $executer->insert_id;

        if($error_message == '') {
            if($supplier_create_submit !== null) {
                echo 'supplier_create_submit';
                header('Location: '.APPLICATION."/supplier/");
            }
            
            if($film_type_create_submit !== null) {
                echo 'film_type_create_submit';
                header('Location: '.APPLICATION."/supplier/details=$id");
            }
        }
    }
}
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
            <div class="row">
                <div class="col-12 col-md-6 col-lg-4">
                    <a href="<?=APPLICATION ?>/supplier/"><i class="fas fa-chevron-left"></i>&nbsp;Назад</a>
                    <h1>Добавление поставщика</h1>
                    <form method="post">
                        <div class="form-group">
                            <label for="name">Название поставщика</label>
                            <input type="text" id="name" name="name" class="form-control<?=$name_valid ?>" value="<?= filter_input(INPUT_POST, 'name') ?>" required="required"/>
                            <div class="invalid-feedback">Название поставщика обязательно</div>
                        </div>
                        <div class="form-group">
                            <button type="submit" class="btn btn-outline-dark" id="film_type_create_submit" name="film_type_create_submit"><i class="fas fa-plus"></i>&nbsp;Добавить марку пленки</button>
                        </div>
                        <div class="form-group">
                            <button type="submit" class="btn btn-dark" id="supplier_create_submit" name="supplier_create_submit">Создать поставщика</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <?php
        include '../include/footer.php';
        ?>
    </body>
</html>
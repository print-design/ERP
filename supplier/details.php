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
                </div>
            </div>
        </div>
        <?php
        include '../include/footer.php';
        ?>
    </body>
</html>
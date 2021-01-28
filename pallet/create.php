<?php
include '../include/topscripts.php';

// Авторизация
if(!IsInRole(array('admin', 'dev', 'technologist', 'storekeeper'))) {
    header('Location: '.APPLICATION.'/unauthorized.php');
}

// Валидация формы

// Обработка отправки формы

// Получение данных
$id = 0;
$date = date("d.m.Y");
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
        <div class="container-fluid" style="padding-left: 40px;">
            <?php
            if(!empty($error_message)) {
                echo "<div class='alert alert-danger>$error_message</div>";
            }
            ?>
            <div class="backlink" style="margin-bottom: 56px;">
                <a href="<?=APPLICATION ?>/pallet/"><i class="fas fa-chevron-left"></i>&nbsp;Назад</a>
            </div>
            <h1 style="font-size: 32px; line-height: 48px; font-weight: 600; margin-bottom: 20px;">Новый паллет</h1>
            <h2 style="font-size: 24px; line-height: 32px; font-weight: 600; margin-bottom: 20px;">Паллет № <?=$id ?> от <?=$date ?></h2>
            <div style="width: 423px;">
                <form method="post">
                    <input type="hidden" />
                    <div class="form-group">
                        <label for="supplier_id">Поставщик</label>
                        <select id="supplier_id" name="supplier_id" class="form-control" required="required">
                            <option value="">Выберите поставщика</option>
                        </select>
                        <div class="invalid-feedback">Поставщик обязательно</div>
                    </div>
                    <div class="form-group">
                        <label for="id_from_supplier">ID паллета от поставщика</label>
                        <input type="text" id="id_from_supplier" name="id_from_supplier" class="form-control" placeholder="Введите ID" required="required" />
                        <div class="invalid-feedback">ID паллета от поставщика обязательно</div>
                    </div>
                </form>
            </div>
        </div>
        <?php
        include '../include/footer.php';
        ?>
    </body>
</html>
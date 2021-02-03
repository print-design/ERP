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
                    <h1 style="font-size: 32px; line-height: 48px; font-weight: 600;">Рулоны</h1>
                </div>
                <div class="p-1">
                    <a href="create.php" class="btn btn-outline-dark" style="margin-right: 12px; padding-left: 33px; padding-right: 44px;"><i class="fas fa-plus" style="font-size: 10px; margin-right: 18px;"></i>Новый ролик</a>
                    <button class="btn btn-outline-dark" data-toggle="modal" data-target="#filterModal" data-text="Фильтр" style="padding-left: 14px; padding-right: 42px; padding-bottom: 14px; padding-top: 14px;"><img src="../images/icons/filter.svg" style="margin-right: 20px;" />Фильтр</button>
                </div>
            </div>
            <table class="table">
                <thead>
                    <tr>
                        <th><input type="checkbox" class="form-check" id="chkMain" /></th>
                        <th>Дата создания</th>
                        <th>Марка пленки</th>
                        <th>Толщина</th>
                        <th>Ширина</th>
                        <th>Вес</th>
                        <th>Длина</th>
                        <th>Поставщик</th>
                        <th>ID поставщика</th>
                        <th>ID руллона</th>
                        <th>№ ячейки</th>
                        <th>Менеджер</th>
                        <th>Статус</th>
                        <th>Комментарий</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>
        <?php
        include '../include/footer.php';
        ?>
        <script src="<?=APPLICATION ?>/js/jquery-ui.js"></script>
    </body>
</html>
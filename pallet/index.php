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
    </head>
    <body>
        <?php
        include '../include/header.php';
        ?>
        <div class="container-fluid list-page">
            <?php
            if(!empty($error_message)) {
                echo "<div class='alert alert-danger'>$error_message</div>";
            }
            ?>
            <div class="d-flex justify-content-between mb-auto">
                <div class="p-1">
                    <h1>Паллеты</h1>
                </div>
                <div class="p-1">
                    <a href="create.php" class="btn btn-outline-dark" style="margin-right: 12px; padding-left: 33px; padding-right: 44px;"><i class="fas fa-plus" style="font-size: 10px; margin-right: 18px;"></i>Новый паллет</a>
                    <a class="btn btn-outline-dark" style="padding-left: 14px; padding-right: 42px; padding-bottom: 14px; padding-top: 15px;"><img src="../images/icons/filter.svg" style="margin-right: 20px;" />Фильтр</a>
                </div>
            </div>
        </div>
        <?php
        include '../include/footer.php';
        ?>
    </body>
</html>
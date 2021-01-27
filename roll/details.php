<?php
include '../include/topscripts.php';

// Авторизация
if(!LoggedIn()) {
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
        <div class="container-fluid">
            <?php
            if(isset($error_message) && $error_message != '') {
               echo "<div class='alert alert-danger'>$error_message</div>";
            }
            ?>
            <div class="btn-group">
                <a href="<?=APPLICATION ?>/rolls/" class="btn btn-outline-dark"><i class="fas fa-undo"></i>&nbsp;Назад</a>
           </div>
            <h1>Информация о ролике</h1>
        </div>
        <?php
        include 'include/footer.php';
        ?>
    </body>
</html>
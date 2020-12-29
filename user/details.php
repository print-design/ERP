<?php
include '../include/topscripts.php';

// Авторизация
if(!IsInRole('admin')) {
    header('Location: '.APPLICATION.'/unauthorized.php');
}

// Валидация формы
define('ISINVALID', ' is-invalid');
$form_valid = true;
$error_message = '';
        
$role_id_valid = '';
        
// Если нет параметра id, переход к списку
$id = filter_input(INPUT_GET, 'id');
if($id === null) {
    header('Location: '.APPLICATION.'/user/');
}
        
// Получение объекта
$username = '';
$fio = '';
        
$sql = "select u.username, u.fio, u.email, u.quit "
        . "from user u where u.id = $id";

$fetcher = new Fetcher($sql);
$error_message = $fetcher->error;

$row = $fetcher->Fetch();
$username = $row['username'];
$fio = htmlentities($row['fio']);
$email = $row['email'];
$quit = $row['quit'];
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
                <div class="col-12 col-md-6">
                    <div class="d-flex justify-content-between mb-2">
                        <div class="p-1">
                            <h1><?=$username ?></h1>
                        </div>
                        <div class="p-1">
                            <div class="btn-group">
                                <a href="<?=APPLICATION ?>/user/" class="btn btn-outline-dark"><i class="fas fa-undo"></i>&nbsp;К списку</a>
                                <a href="<?=APPLICATION ?>/user/edit.php?id=<?=$id ?>" class="btn btn-outline-dark"><i class="fas fa-edit"></i>&nbsp;Редактировать</a>
                                <?php
                                if(filter_input(INPUT_COOKIE, USERNAME) != $username):
                                ?>
                                <a href="<?=APPLICATION ?>/user/delete.php?id=<?=$id ?>" class="btn btn-outline-dark"><i class="fas fa-trash-alt"></i>&nbsp;Удалить</a>
                                <?php
                                endif;
                                ?>
                            </div>
                        </div>
                    </div>
                    <table class="table table-bordered">
                        <tr>
                            <th>Логин</th>
                            <td><?=$username ?></td>
                        </tr>
                        <tr>
                            <th>ФИО</th>
                            <td><?=$fio ?></td>
                        </tr>
                        <tr>
                            <th>E-Mail</th>
                            <td><?=$email ?></td>
                        </tr>
                        <tr>
                            <th>Уволился</th>
                            <td><?=($quit == 0 ? 'Нет' : 'Да') ?></td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
        <?php
        include '../include/footer.php';
        ?>
    </body>
</html>
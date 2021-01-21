<?php
include '../include/topscripts.php';

// Авторизация
if(!LoggedIn()) {
    header('Location: '.APPLICATION.'/unauthorized.php');
}
        
// Получение личных данных
$row = (new Fetcher("select username, last_name, first_name, email, phone from user where id=".GetUserId()))->Fetch();
$username = $row['username'];
$last_name = $row['last_name'];
$first_name = $row['first_name'];
$email = $row['email'];
$phone = $row['phone'];
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
            
            $password = filter_input(INPUT_GET, 'password');
            
            if($password == 'true') {
                echo "<div class='alert alert-info'>Пароль успешно изменён</div>";
            }
            ?>
            <div class="row">
                <div class="col-12 col-md-6">
                    <div class="d-flex justify-content-between">
                        <div class="p-1">
                            <h1>Мои настройки</h1>
                        </div>
                        <div class="p-1">
                            <div class="btn-group">
                                <a href="<?=APPLICATION ?>/personal/edit.php" class="btn btn-outline-dark"><i class="fas fa-pencil-alt"></i>&nbsp;Редактировать</a>
                                <a href="<?=APPLICATION ?>/personal/password.php" class="btn btn-outline-dark"><i class="fas fa-key"></i>&nbsp;Сменить пароль</a>
                            </div>
                        </div>
                    </div>
                    <table class="table table-bordered">
                        <tr>
                            <th>Имя</th>
                            <td><?=$first_name ?></td>
                        </tr>
                        <tr>
                            <th>Фамилия</th>
                            <td><?=$last_name ?></td>
                        </tr>
                        <tr>
                            <th>E-Mail</th>
                            <td><?=$email ?></td>
                        </tr>
                        <tr>
                            <th>Телефон</th>
                            <td><?=$phone ?></td>
                        </tr>
                        <tr>
                            <th>Логин</th>
                            <td><?=$username ?></td>
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
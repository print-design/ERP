<?php
include '../include/topscripts.php';
include '../include/restrict_admin.php';

// Валидация формы
define('ISINVALID', ' is-invalid');
$form_valid = true;
$error_message = '';
        
$username_valid = '';
$password_valid = '';
$fio_valid = '';
$email_valid = '';
        
// Обработка отправки формы
$user_create_submit = filter_input(INPUT_POST, 'user_create_submit');
if($user_create_submit !== null) {
    $username = filter_input(INPUT_POST, 'username');
    if($username == '') {
        $username_valid = ISINVALID;
        $form_valid = false;
    }
    
    $password = filter_input(INPUT_POST, 'password');
    if($password == '') {
        $password_valid = ISINVALID;
        $form_valid = false;
    }
    
    $fio = filter_input(INPUT_POST, 'fio');
    if($fio == '') {
        $fio_valid = ISINVALID;
        $form_valid = false;
    }
    
    $email = filter_input(INPUT_POST, 'email');
    if($email != '' && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $email_valid = ISINVALID;
        $form_valid = false;
    }
    
    if($form_valid) {
        $username = addslashes($username);
        $fio = addslashes($fio);
        $email = addslashes($email);
        $executer = new Executer("insert into user (username, password, fio, email, quit) values ('$username', password('$password'), '$fio', '$email', 0)");
        $error_message = $executer->error;
        $id = $executer->insert_id;
        
        if($error_message == '') {
            header('Location: '.APPLICATION."/user/details.php?id=$id");
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
                    <div class="d-flex justify-content-between mb-2">
                        <div class="p-1">
                            <h1>Новый пользователь</h1>
                        </div>
                        <div class="p-1">
                            <a href="<?=APPLICATION ?>/user/" class="btn btn-outline-dark"><i class="fas fa-undo-alt"></i>&nbsp;Отмена</a>
                        </div>
                    </div>
                    <hr/>
                    <form method="post">
                        <div class="form-group">
                            <label for="username">Логин</label>
                            <input type="text" id="username" name="username" class="form-control<?=$username_valid ?>" value="<?= isset($username) ? $username : '' ?>" required="required" autocomplete="off"/>
                            <div class="invalid-feedback">Логин обязательно</div>
                        </div>
                        <div class="form-group">
                            <label for="password">Пароль</label>
                            <input type="password" id="password" name="password" class="form-control<?=$password_valid ?>" required="required" autocomplete="off"/>
                            <div class="invalid-feedback">Пароль обязательно</div>
                        </div>
                        <div class="form-group">
                            <label for="name">ФИО</label>
                            <input type="text" id="fio" name="fio" class="form-control<?=$fio_valid ?>" value="<?= isset($fio) ? htmlentities($fio) : '' ?>" autocomplete="off" required="required"/>
                            <div class="invalid-feedback">ФИО обязательно</div>
                        </div>
                        <div class="form-group">
                            <label for="email">E-Mail</label>
                            <input type="email" id="email" name="email" class="form-control<?=$email_valid ?>" value="<?= isset($email) ? htmlentities($email) : '' ?>" autocomplete="off"/>
                            <div class="invalid-feedback">Неправильный формат E-Mail</div>
                        </div>
                        <div class="form-group">
                            <button type="submit" class="btn btn-outline-dark" id="user_create_submit" name="user_create_submit">Сохранить</button>
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
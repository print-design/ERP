<?php
include '../include/topscripts.php';

// Авторизация
if(!IsInRole(array('admin', 'administrator', 'technologist'))) {
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
            <div class="d-flex justify-content-between mb-2">
                <div class="p-1 row">
                    <div class="col-6">
                        <a href="<?=APPLICATION ?>/user/">Сотрудники</a>
                    </div>
                    <div class="col-6">
                        <a href="<?=APPLICATION ?>/supplier/">Поставщики</a>    
                    </div>
                </div>
                <div class="p-1">
                    <a href="create.php" title="Добавить пользователя" class="btn btn-outline-dark mr-sm-2">
                        <i class="fas fa-plus"></i>&nbsp;Добавить сотрудника
                    </a>
                </div>
            </div>
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>ФИО</th>
                        <th>Должность</th>
                        <th>Логин</th>
                        <th>E-Mail</th>
                        <th>Телефон</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $sql = "select u.id, u.first_name, u.last_name, r.local_name role, u.username, u.email, u.phone "
                            . "from user u inner join role r on u.role_id = r.id "
                            . "order by u.first_name asc";
                    $fetcher = new Fetcher($sql);
                    $error_message = $fetcher->error;
                    
                    while ($row = $fetcher->Fetch()) {
                        echo "<tr>"
                                ."<td>".$row['first_name'].' '.$row['last_name']."</td>"
                                ."<td>".$row['role']."</td>"
                                ."<td>".$row['username']."</td>"
                                ."<td>".$row['email']."</td>"
                                ."<td>".$row['phone']."</td>"
                                ."<td><a href='".APPLICATION."/user/delete.php?id=".$row['id']."'><i class='fas fa-trash'></i></td>"
                                ."</tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
        <?php
        include '../include/footer.php';
        ?>
    </body>
</html>
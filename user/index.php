<?php
include '../include/topscripts.php';

// Авторизация
if(!IsInRole(array('admin', 'dev', 'technologist'))) {
    header('Location: '.APPLICATION.'/unauthorized.php');
}

// Обработка отправки формы
$delete_user_submit = filter_input(INPUT_POST, 'delete_user_submit');
if($delete_user_submit !== null) {
    $id = filter_input(INPUT_POST, 'id');
    $error_message = (new Executer("delete from user where id=$id"))->error;
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
            <div class="d-flex justify-content-between mb-2 nav2">
                <div class="p-1 row">
                    <div class="col-6">
                        <a class="active" href="<?=APPLICATION ?>/user/">Сотрудники</a>
                    </div>
                    <div class="col-6">
                        <a href="<?=APPLICATION ?>/supplier/">Поставщики</a>    
                    </div>
                </div>
                <div class="p-1">
                    <a href="create.php" title="Добавить пользователя" class="btn btn-outline-dark">
                        <i class="fas fa-plus"></i>&nbsp;Добавить сотрудника
                    </a>
                </div>
            </div>
            <table class="table">
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
                                ."<td>".$row['phone']."</td>";
                        echo '<td>';
                        if(filter_input(INPUT_COOKIE, USER_ID) != $row['id']) {
                            echo "<form method='post'>";
                            echo "<input type='hidden' id='id' name='id' value='".$row['id']."' />";
                            echo "<button type='submit' class='btn btn-link confirmable' id='delete_user_submit' name='delete_user_submit'><i class='fas fa-trash'></i></button>";
                            echo '</form>';
                        }
                        echo '</td>';
                        echo "</tr>";
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
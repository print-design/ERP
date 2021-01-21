<?php
include '../include/topscripts.php';

// Авторизация
if(!IsInRole(array('admin', 'dev', 'technologist'))) {
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
            <div class="d-flex justify-content-between mb-2 nav2">
                <div class="p-1 row">
                    <div class="col-6">
                        <a href="<?=APPLICATION ?>/user/">Сотрудники</a>
                    </div>
                    <div class="col-6">
                        <a class="active" href="<?=APPLICATION ?>/supplier">Поставщики</a>
                    </div>
                </div>
                <div class="p-1">
                    <a href="create.php" title="Добавить поставщика" class="btn btn-outline-dark">
                        <i class="fas fa-plus"></i>&nbsp;Добавить поставщика
                    </a>
                </div>
            </div>
            <table class="table">
                <thead>
                    <tr>
                        <th>Название поставщика</th>
                        <th>Типы пленок</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $sql = "select s.id, s.name from supplier s order by s.name";
                    $fetcher = new Fetcher($sql);
                    $error_message = $fetcher->error;
                    
                    while ($row = $fetcher->Fetch()) {
                        $name = htmlentities($row['name']);
                        echo "<tr>"
                        . "<td>$name</td>"
                                . "<td>!</td>"
                                . "<td><a href='".APPLICATION."/supplier/edit.php?id=".$row['id']."'><i class='fas fa-edit'></i></a></td>"
                                . "</tr>";
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
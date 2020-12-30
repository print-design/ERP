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
                        <a href="<?=APPLICATION ?>/supplier">Поставщики</a>
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
                        <td></td>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $sql = "select s.id, s.name, "
                            . "(select count(id) from film_type where supplier_id=s.id) count, "
                            . "(select name from film_type where supplier_id=s.id limit 1) first "
                            . "from supplier s order by s.name";
                    $fetcher = new Fetcher($sql);
                    $error_message = $fetcher->error;
                    
                    while ($row = $fetcher->Fetch()) {
                        $name = htmlentities($row['name']);
                        $first = htmlentities($row['first']);
                        $count = intval($row['count']);
                        $more = '';
                        if($count > 1) {
                            $more = ' и еще '.($count - 1);
                        }
                        echo "<tr>"
                        . "<td>$name</td>"
                                . "<td>$first$more</td>"
                                . "<td><a href=''><i class='fas fa-edit'></i></a></td>"
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
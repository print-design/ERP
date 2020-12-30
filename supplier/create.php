<?php
include '../include/topscripts.php';

// Авторизация
if(!IsInRole(array('admin', 'administrator', 'technologist'))) {
    header('Location: '.APPLICATION.'/unauthorized.php');
}

// Валидация формы
define('ISINVALID', ' is-invalid');
$form_valid = true;
$error_message = '';

$name_valid = '';

// Обработка отправки формы
$supplier_create_submit = filter_input(INPUT_POST, 'supplier_create_submit');
$film_type_create_submit = filter_input(INPUT_POST, 'film_type_create_submit');
if($supplier_create_submit !== null || $film_type_create_submit !== null) {
    $name = filter_input(INPUT_POST, 'name');
    if($name == '') {
        $name_valid = ISINVALID;
        $form_valid = false;
    }
    
    if($form_valid) {
        $name = addslashes($name);
        
        $executer = new Executer("insert into supplier (name) values ('$name')");
        $error_message = $executer->error;
        $id = $executer->insert_id;
        
        if($error_message == '') {
            if($supplier_create_submit != null) {
                header('Location: '.APPLICATION."/supplier/");
            }
            
            if($film_type_create_submit !== null) {
                header('Location: '.APPLICATION."/supplier/details=$id");
            }
        }
    }
}
?>
<!DOCTYPE html>
<html>
    <head>
        <?php
        include '../include/';
        ?>
    </head>
</html>
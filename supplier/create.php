<?php
include '../include/topscripts.php';

// Авторизация
if(!IsInRole(array('admin', 'dev', 'technologist'))) {
    header('Location: '.APPLICATION.'/unauthorized.php');
}

// Валидация формы
define('ISINVALID', ' is-invalid');
$form_valid = true;
$error_message = '';

$name_valid = '';

// Обработка отправки формы
if(null !== filter_input(INPUT_POST, 'supplier_create_submit')) {
    $name = filter_input(INPUT_POST, 'name');
    if(empty($name)) {
        $name_valid = ISINVALID;
        $form_valid = false;
    }
    
    //-------------------------------------------------------------
    print_r($_POST);
    //-------------------------------------------------------------
    
    /*if($form_valid) {
        $name = addslashes($name);
        
        $executer = new Executer("insert into supplier (name) values ('$name')");
        $error_message = $executer->error;
        $id = $executer->insert_id;

        if(empty($error_message)) {
            header('Location: '.APPLICATION."/supplier/details.php?id=$id");
        }
    }*/
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
            if(!empty($error_message)) {
                echo "<div class='alert alert-danger'>$error_message</div>";
            }
            ?>
            <div class="d-flex justify-content-between mb-2 nav2">
                <div class="p-1 row">
                    <div class="col-6">
                        <a href="<?=APPLICATION ?>/user/">Сотрудники</a>
                    </div>
                    <div class="col-6">
                        <a class="active" href="<?=APPLICATION ?>/supplier/">Поставщики</a>    
                    </div>
                </div>
                <div class="p-1"></div>
            </div>
            <div class="backlink">
                <a href="<?=APPLICATION ?>/supplier/"><i class="fas fa-chevron-left"></i>&nbsp;Назад</a>
            </div>
            <h1>Добавление поставщика</h1>
            <form method="post">
                <div class="form-group row">
                    <div class="col-12 col-md-6 col-lg-4">
                        <label for="name">Название поставщика</label>
                        <input type="text" id="name" name="name" class="form-control<?=$name_valid ?>" value="<?= filter_input(INPUT_POST, 'name') ?>" required="required"/>
                        <div class="invalid-feedback">Название поставщика обязательно</div>                            
                    </div>
                </div>
                <div class="form-group row">
                    <div class="col-12 col-md-6">
                        <table class="table" id="variations-table">
                            <tr id="add-variation-tr">
                                <td colspan="4" class="text-right">
                                    <button type="button" class="btn btn-link" id="add-brand-table-link"><i class="fas fa-plus"></i>&nbsp;Добавить</button>
                                </td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-12 col-md-10">
                        <div class="row" id="add-brand-form">
                            <div class="col-4">
                                <input type="text" id="film_brand" name="film_brand" class="form-control" placeholder="Название" />
                            </div>
                            <div class="col-2">
                                <input type="number" min="1" step="1" id="width" name="width" class="form-control" placeholder="Толщина" />
                            </div>
                            <div class="col-2">
                                <input type="number" min="1" step="0.1" id="weight" name="weight" class="form-control" placeholder="Удельный вес" />
                            </div>
                            <div class="col-4">
                                <button type="button" class="btn btn-link" id="add-brand-link">Добавить</button>
                                <button type="button" class="btn btn-link" id="add-brand-cancel">Отмена</button>
                            </div>
                        </div>
                        <button type="button" class="btn btn-outline-dark" id="add-brand-button"><i class="fas fa-plus"></i>&nbsp;Добавить марку пленки</button>
                    </div>
                </div>
                <div class="form-group">
                    <button type="submit" class="btn btn-dark" id="supplier_create_submit" name="supplier_create_submit">Создать поставщика</button>
                </div>
            </form>
        </div>
        <?php
        include '../include/footer.php';
        ?>
        <script>
            $('#add-brand-form').hide();
            $('#variations-table').hide();
            
            $('#add-brand-button').click(function(){
                $(this).hide();
                $('#add-brand-form').show();
                $('#add-brand-form').find('input[id="film_brand"]').focus();
            });
            
            $('#add-brand-cancel').click(function(){
                $('#add-brand-form').find('input').val('');
                $('#add-brand-form').hide();
                $('#add-brand-button').show();
            });
            
            $('#add-brand-table-link').click(function(){
                $('#add-brand-button').click();
            });
            
            function RemoveVariation(param) {
                if(confirm("Действительно удалить?")) {
                    param.closest("tr").remove();
                    
                    var tblrows = $('#variations-table').find("tr");
                    var tblrnumber = tblrows.length;
                    
                    if(tblrnumber == 1) {
                        $('#variations-table').hide();
                    }
                }
            }
            
            $('#add-brand-link').click(function(){
                var empties = $('#add-brand-form input').filter(function(){return $(this).val() == ''});
                if(empties.length > 0) {
                    $('#add-brand-form').find('input').filter(function(){return $(this).val() == ''}).first().focus();
                }
                else {
                    // Показ таблицы
                    if($('#variations-table').is(':hidden')) {
                        $('#variations-table').show();
                    }
                    
                    // Добавление строки в таблицу
                    var film_brand = $('#add-brand-form').find('input[id="film_brand"]').val();
                    var width = $('#add-brand-form').find('input[id="width"]').val();
                    var weight = $('#add-brand-form').find('input[id="weight"]').val();
                    var rowscount = $('#variations-table').find("tr").length;
                    
                    var tblrow = '<tr>' + 
                            '<td>' + film_brand + '<input type="hidden" id="film_brand_' + rowscount + '" name="film_brand_' + rowscount + '" value="' + film_brand + '" /></td>' + 
                            '<td class="text-right">' + width + '<input type="hidden" id="width_' + rowscount + '" name="width_' + rowscount + '" value="' + width + '" /></td>' + 
                            '<td class="text-right">' + weight + '<input type="hidden" id="weight_' + rowscount + '" name="weight_' + rowscount + '" value="' + weight + '" /></td>' + 
                            '<td class="text-right"><button type="button" class="btn btn-link" onclick="javascript:RemoveVariation($(this));"><i class="fas fa-trash-alt"></i></button></td>'
                            '</tr>';
                    $('#variations-table tr:last').before(tblrow);
                    
                    // Скрытие формы и показ кнопки
                    $('#add-brand-form').find('input').val('');
                    $('#add-brand-form').hide();
                    $('#add-brand-button').show();
                }
            });
        </script>
    </body>
</html>
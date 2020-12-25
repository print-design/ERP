<hr />
<div class="container-fluid">
    &COPY;&nbsp;Принт-дизайн
</div>

<script src='<?=APPLICATION ?>/js/jquery-3.5.1.min.js'></script>
<script src='<?=APPLICATION ?>/js/bootstrap.min.js'></script>

<script>
    // Фильтрация ввода
    $(document).ready(function(){
        $('.int-only').keypress(function(e) {
            if(/\D/.test(String.fromCharCode(e.charCode))) {
                return false;
            }
        });
    });
    
    $(document).ready(function(){
        $('.float-only').keypress(function(e) {
            if(!/[\.\d]/.test(String.fromCharCode(e.charCode))) {
                return false;
            }
        });
    });
    
    // Валидация
    $(document).ready(function(){
        $('input').keypress(function(){
            $(this).removeClass('is-invalid');
        });
    });
    
    $(document).ready(function(){
        $('select').change(function(){
            $(this).removeClass('is-invalid');
        });
    });
    
    // Подтверждение удаления
    $('button.confirmable').click(function(){
        return confirm('Действительно удалить?');
    });
</script>
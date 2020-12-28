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
    
    <?php if(IsInRole('cutter')): ?>
        // Автологаут через 20 минут
        let unix_timestamp = <?= filter_input(INPUT_COOKIE, LOGIN_TIME) ?>;
        // Create a new JavaScript Date object based on the timestamp
        // multiplied by 1000 so that the argument is in milliseconds, not seconds.
        var begin_date = new Date(unix_timestamp * 1000);
        var end_date = new Date(unix_timestamp * 1000 + (20 * 60 * 1000));
        
        var beforeLogout = end_date - (new Date());       
        var beforeLogoutSec = Math.floor(beforeLogout / 1000);
        var beforeLogoutMin = Math.floor(beforeLogoutSec / 60);
        var beforeLogoutLastSec = beforeLogoutSec - (beforeLogoutMin * 60);
        
        $('#autologout').html(String(beforeLogoutMin).padStart(2, '0') + ':' + String(beforeLogoutLastSec).padStart(2, '0'));
    <?php endif; ?>
</script>
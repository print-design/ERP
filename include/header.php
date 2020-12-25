<div class="container-fluid">
    <nav class="navbar navbar-expand-sm">
        <a class="navbar-brand" href="<?=APPLICATION ?>/">
            <i class="fas fa-home"></i>
        </a>
        <ul class="navbar-nav mr-auto">
            <?php
            $pallets_status = filter_input(INPUT_SERVER, 'PHP_SELF') == APPLICATION.'/pallets/index.php' ? ' disabled' : '';
            $rolls_status = filter_input(INPUT_SERVER, 'PHP_SELF') == APPLICATION.'/rolls/index.php' ? ' disabled' : '';
            $cut_requests_status = filter_input(INPUT_SERVER, 'PHP_SELF') == APPLICATION.'/cut_requests/index.php' ? ' disabled' : '';
            ?>
            <li class='nav-item'>
                <a class="nav-link<?=$rolls_status ?>" href="<?=APPLICATION ?>/rolls/">Ролики</a>
            </li>
            <li class="nav-item">
                <a class="nav-link<?=$cut_requests_status ?>" href="<?=APPLICATION ?>/cut_requests/">Заявки на раскрой</a>
            </li>
        </ul>
    </nav>
</div>
<hr />
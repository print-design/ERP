<div class="container">
    <nav class="navbar navbar-expand-sm bg-light">
        <a class="navbar-brand" href="<?=APPLICATION ?>/">
            <img src="<?=APPLICATION ?>/images/icons/home.png" title="На главную" />
        </a>
        <ul class="navbar-nav">
            <?php
            $pallets_status = $_SERVER['PHP_SELF'] == APPLICATION.'/pallets/index.php' ? ' disabled' : '';
            $rolls_status = $_SERVER['PHP_SELF'] == APPLICATION.'/rolls/index.php' ? ' disabled' : '';
            $cut_requests_status = $_SERVER['PHP_SELF'] == APPLICATION.'/cut_requests/index.php' ? ' disabled' : '';
            $application = APPLICATION;
            echo <<<NAVBAR
            <li class='nav-item'>
                <a class="nav-link$rolls_status" href='$application/rolls/'>Ролики</a>
            </li>
            <li class="nav-item">
                <a class="nav-link$cut_requests_status" href="$application/cut_requests/">Заявки на раскрой</a>
            </li>
            NAVBAR;
            ?>
        </ul>
    </nav>
</div>
<hr />
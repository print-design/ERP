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
            $user_status = filter_input(INPUT_SERVER, 'PHP_SELF') == APPLICATION.'/user/index.php' ? ' disabled' : '';
            $personal_status = filter_input(INPUT_SERVER, 'PHP_SELF') == APPLICATION.'/personal/index.php' ? ' disabled' : '';
            if(LoggedIn()):
            ?>
            <li class='nav-item'>
                <a class="nav-link<?=$rolls_status ?>" href="<?=APPLICATION ?>/rolls/">Ролики</a>
            </li>
            <li class="nav-item">
                <a class="nav-link<?=$cut_requests_status ?>" href="<?=APPLICATION ?>/cut_requests/">Заявки на раскрой</a>
            </li>
            <li class="nav-item">
                <a class="nav-link<?=$personal_status ?>" href="<?=APPLICATION ?>/personal/">Мои настройки</a>
            </li>
            <?php
            endif;
            if(IsInRole('admin')):
            ?>
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" href="#" id="navbardrop" data-toggle="dropdown">
                    Администратор
                </a>
                <div class="dropdown-menu">
                    <a class="dropdown-item<?=$user_status ?>" href="<?=APPLICATION ?>/user/">Пользователи</a>
                </div>
            </li>
            <?php
            endif;
            ?>
        </ul>
        <?php
        $user_name = filter_input(INPUT_COOKIE, USERNAME);
        if($user_name !== null):
        ?>
        <?php
        
        ?>
        <form class="form-inline" method="post">
            <label>
                <?php
                echo filter_input(INPUT_COOKIE, FIO);
                if(IsInRole('cutter')) {
                    echo ', автовыход через&nbsp;';
                    echo '<div id="autologout">';
                    echo filter_input(INPUT_COOKIE, LOGIN_TIME);
                    echo '</div>';
                }
                ?>
                &nbsp;
            </label>
            <button type="submit" class="btn btn-outline-dark" id="logout_submit" name="logout_submit">Выход&nbsp;<i class="fas fa-sign-out-alt"></i></button>
        </form>
        <?php
        else:
        ?>
        <form class="form-inline my-2 my-lg-0" method="post">
            <div class="form-group">
                <input class="form-control mr-sm-2<?=$login_username_valid ?>" type="text" id="login_username" name="login_username" placeholder="Логин" value="<?=$_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['login_username']) ? $_POST['login_username'] : '' ?>" required="required" autocomplete="on" />
                <div class="invalid-feedback">*</div>
            </div>
            <div class="form-group">
                <input class="form-control mr-sm-2<?=$login_password_valid ?>" type="password" id="login_password" name="login_password" placeholder="Пароль" required="required" />
                <div class="invalid-feedback">*</div>
            </div>
            <button type="submit" class="btn btn-outline-dark my-2 my-sm-2" id="login_submit" name="login_submit">Войти&nbsp;<i class="fas fa-sign-in-alt"></i></button>
        </form>
        <?php
        endif;
        ?>
    </nav>
</div>
<hr />
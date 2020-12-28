<?php
include 'define.php';

global $weekdays;

$weekdays = array();
$weekdays[0] = 'Вс';
$weekdays[1] = 'Пн';
$weekdays[2] = 'Вт';
$weekdays[3] = 'Ср';
$weekdays[4] = 'Чт';
$weekdays[5] = 'Пт';
$weekdays[6] = 'Сб';

// Функции
function LoggedIn() {
    $username = filter_input(INPUT_COOKIE, USERNAME);
    return $username !== null;
}

function GetUserId() {
    return filter_input(INPUT_COOKIE, USER_ID);
}

function IsInRole($role) {
    $roles = filter_input(INPUT_COOKIE, ROLES);
    if($roles !== null) {
        $arr_roles = unserialize($roles);
        return in_array($role, $arr_roles);
    }
    
    return false;
}

// Классы
class Executer {
    public $error = '';
    public $insert_id = 0;
            
    function __construct($sql) {
        $conn = new mysqli(DATABASE_HOST, DATABASE_USER, DATABASE_PASSWORD, DATABASE_NAME);

        if($conn->connect_error) {
            $this->error = 'Ошибка соединения: '.$conn->connect_error;
            return;
        }
        
        $conn->query('set names utf8');
        $result = $conn->query($sql);
        $this->error = $conn->error;
        $this->insert_id = $conn->insert_id;
        
        $conn->close();
    }
}

class Grabber {
    public  $error = '';
    public $result = array();
            
    function __construct($sql) {
        $conn = new mysqli(DATABASE_HOST, DATABASE_USER, DATABASE_PASSWORD, DATABASE_NAME);
        
        if($conn->connect_error) {
            $this->error = 'Ошибка соединения: '.$conn->connect_error;
            return;
        }
        
        $conn->query('set names utf8');
        $result = $conn->query($sql);
        
        if(is_bool($result)) {
            $this->error = $conn->error;
        }
        else {
            $this->result = mysqli_fetch_all($result, MYSQLI_ASSOC);
        }
        
        $conn->close();
    }
}

class Fetcher {
    public $error = '';
    private $result;
            
    function __construct($sql) {
        $conn = new mysqli(DATABASE_HOST, DATABASE_USER, DATABASE_PASSWORD, DATABASE_NAME);
        
        if($conn->connect_error) {
            $this->error = 'Ошибка соединения: '.$conn->connect_error;
            return;
        }
        
        $conn->query('set names utf8');
        $this->result = $conn->query($sql);
        
        if(is_bool($this->result)) {
            $this->error = $conn->error;
        }
        
        $conn->close();
    }
    
    function Fetch() {
        return mysqli_fetch_array($this->result);
    }
}

// Валидация формы логина
define('LOGINISINVALID', ' is-invalid');
$login_form_valid = true;

$login_username_valid = '';
$login_password_valid = '';

// Обработка отправки формы логина
$login_submit = filter_input(INPUT_POST, 'login_submit');
if($login_submit !== null){
    $login_username = filter_input(INPUT_POST, 'login_username');
    if($login_username == '') {
        $login_username_valid = LOGINISINVALID;
        $login_form_valid = false;
    }
    
    $login_password = filter_input(INPUT_POST, 'login_password');
    if($login_password == '') {
        $login_password_valid = LOGINISINVALID;
        $login_form_valid = false;
    }
    
    if($login_form_valid) {
        $user_id = '';
        $username = '';
        $fio = '';
        $twofactor = 0;
        
        $sql = "select u.id, u.username, u.fio, u.email, "
                . "(select count(*) from user_role ur inner join role r on ur.role_id=r.id where ur.user_id=u.id and r.twofactor=true) twofactor "
                . "from user u where u.username='$login_username' and u.password=password('$login_password') and u.quit = 0";
        
        $users_result = (new Grabber($sql))->result;
        
        foreach ($users_result as $row) {
            $user_id = $row['id'];
            $username = $row['username'];
            $fio = $row['fio'];
            $email = $row['email'];
            $twofactor = $row['twofactor'];
        }
        
        if($user_id == '' || $username == '') {
            $error_message = "Неправильный логин или пароль";
        }
        else {
            //*******************************
            // Двухфакторная аутентификация
            if($twofactor == 1) {
                $code_valid = '';
                include 'twofactor_email.php';
            }
            // ****************************
            
            setcookie(USER_ID, $user_id, 0, "/");
            setcookie(USERNAME, $username, 0, "/");
            setcookie(FIO, $fio, 0, "/");
            
            $roles = array();
            $role_i = 0;
            $roles_result = (new Grabber("select r.name from user_role ur inner join role r on ur.role_id = r.id where ur.user_id = $user_id"))->result;
            
            foreach ($roles_result as $role_row) {
                $roles[$role_i++] = $role_row['name'];
            }
            
            setcookie(ROLES, serialize($roles), 0, '/');
            header("Refresh:0");
        }
    }
}

// Обработка формы отправки кода безопасности
$security_code_submit = filter_input(INPUT_POST, 'security_code_submit');
if($security_code_submit !== null) {
    $id = filter_input(INPUT_POST, 'id');
    $sql = "select id, username, fio, email, code from user where id=$id";
    $result = (new Grabber($sql))->result;
    
    foreach ($result as $row) {
        $user_id = $row['id'];
        $username = $row['username'];
        $fio = $row['fio'];
        $email = $row['email'];
        $code = $row['code'];
        
        if(filter_input(INPUT_POST, 'code') == $code) {
            $error_message = (new Executer("update user set code=NULL where id=$user_id"))->error;
            
            if($error_message == '') {
                setcookie(USER_ID, $user_id, 0, "/");
                setcookie(USERNAME, $username, 0, "/");
                setcookie(FIO, $fio, 0, "/");
            
                $roles = array();
                $role_i = 0;
                $roles_result = (new Grabber("select r.name from user_role ur inner join role r on ur.role_id = r.id where ur.user_id = $user_id"))->result;
            
                foreach ($roles_result as $role_row) {
                    $roles[$role_i++] = $role_row['name'];
                }
            
                setcookie(ROLES, serialize($roles), 0, '/');
                header("Refresh:0");
            }
        }
        else {
            define('ISINVALID', ' is-invalid');
            $code_valid = ISINVALID;
            include 'twofactor_email.php';
        }
    }
}

// Выход из системы
$logout_submit = filter_input(INPUT_POST, 'logout_submit');
if($logout_submit !== null) {
    setcookie(USER_ID, '', 0, "/");
    setcookie(USERNAME, '', 0, "/");
    setcookie(FIO, '', 0, "/");
    setcookie(ROLES, '', 0, "/");
    header("Refresh:0");
    header('Location: '.APPLICATION.'/');
}
?>
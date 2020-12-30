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
    $cookie = filter_input(INPUT_COOKIE, ROLES);
    
    if(is_array($role)) {
        return in_array($cookie, $role);
    }
    else {
        return $cookie == $role;
    }
    
    return false;
}

function Abbreviate($text) {
    $substrings = explode(' ', $text);
    $result = '';
    
    $i = 0;
    foreach ($substrings as $substring) {
        if(strlen($substring) > 0) {
            if(++$i < 3) {
                $result .= mb_substr($substring, 0, 1);
            }
        }
    }
    
    return $result;
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
        $conn->query($sql);
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
        $last_name = '';
        $first_name = '';
        $role = '';
        $twofactor = 0;
        
        $sql = "select u.id, u.username, u.last_name, u.first_name, u.email, r.name role, r.twofactor "
                . "from user u "
                . "inner join role r on u.role_id=r.id "
                . "where u.username='$login_username' and u.password=password('$login_password') and u.quit = 0";
        
        $users_result = (new Grabber($sql))->result;
        
        foreach ($users_result as $row) {
            $user_id = $row['id'];
            $username = $row['username'];
            $last_name = $row['last_name'];
            $first_name = $row['first_name'];
            $role = $row['role'];
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
            setcookie(LAST_NAME, $last_name, 0, "/");
            setcookie(FIRST_NAME, $first_name, 0, "/");
            setcookie(ROLE, $role, 0, "/");
            setcookie(LOGIN_TIME, (new DateTime())->getTimestamp(), 0, "/");
            header("Refresh:0");
        }
    }
}

// Обработка формы отправки кода безопасности
$security_code_submit = filter_input(INPUT_POST, 'security_code_submit');
if($security_code_submit !== null) {
    $id = filter_input(INPUT_POST, 'id');
    $sql = "select u.id, u.username, u.last_name, u.first_name, u.email, u.code, r.name role "
            . "from user u inner join role r on u.role_id = r.id "
            . "where u.id=$id";
    $result = (new Grabber($sql))->result;
    
    foreach ($result as $row) {
        $user_id = $row['id'];
        $username = $row['username'];
        $last_name = $row['last_name'];
        $first_name = $row['first_name'];
        $role = $row['role'];
        $email = $row['email'];
        $code = $row['code'];
        
        if(filter_input(INPUT_POST, 'code') == $code) {
            $error_message = (new Executer("update user set code=NULL where id=$user_id"))->error;
            
            if($error_message == '') {
                setcookie(USER_ID, $user_id, 0, "/");
                setcookie(USERNAME, $username, 0, "/");
                setcookie(LAST_NAME, $last_name, 0, "/");
                setcookie(FIRST_NAME, $first_name, 0, "/");
                setcookie(ROLE, $role, 0, '/');
                setcookie(LOGIN_TIME, (new DateTime())->getTimestamp(), 0, "/");
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
    setcookie(LAST_NAME, '', 0, "/");
    setcookie(FIRST_NAME, '', 0, "/");
    setcookie(LOGIN_TIME, '', 0, "/");
    setcookie(ROLE, '', 0, "/");
    header("Refresh:0");
    header('Location: '.APPLICATION.'/');
}
?>
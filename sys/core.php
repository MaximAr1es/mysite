<?php
/* 
* This core functions for application
*/

// Обработка текста
function clearInt($num){
    return abs((int)$num);
}

function clearStr($str){
    return trim(strip_tags($str));
}

function clearHTML($html){
    return trim(htmlspecialchars($html));
}

/* Route functions */
function route($item = 1) {
    $request = explode("/", $_SERVER["REQUEST_URI"]);
    return $request[$item];
}

// Соединение с БД
function init(){
    $config = parse_ini_file(ROOT.'/sys/config.ini');
    //print_r($config);
    $dsn = "{$config['driver']}:host={$config['host']};dbname={$config['schema']}";
    return new PDO($dsn, $config['user'], $config['password']);
}

// Регистрация
    function register($pdo, $email, $password){
        $email = $pdo->quote($email);
        $password = md5($password);
        $password = $pdo->quote($password);
        //print $email.' '.$password;
        // TODO: Проверить правильность мыла регулярным выражением
        $sql_check = "SELECT COUNT(id) FROM ts_users WHERE email=$email";
        $stmt = $pdo->query($sql_check);
        $row = $stmt->fetch(PDO::FETCH_NUM);
        if($row[0] > 0){
            print 'Учетная запись уже существует. Забыл пароль?';
        }else{
            // Добавляем учетную запись в таблицу ts_users
            $sql_insert = "INSERT INTO ts_users (email, password, status) VALUES ($email, $password, 1)";
            //print $sql_insert;
            
            if($pdo->exec($sql_insert)){
                /* это расскомментируем в следующем уроке
                $sql = "SELECT id FROM mc_user WHERE mail=$mail";
                $stmt = $pdo->query($sql);
                $row = $stmt->fetch(PDO::FETCH_ASSOC);
                $uid = $row['id'];
                $sql_insert = "INSERT INTO mc_profile (user_id) VALUES ('$uid')";
                $pdo->exec($sql_insert);
                mkdir('content/'.$uid.'/');
                */
                return true;
            }else{
                return false;
            }
        }
    }

    // Авторизация
    function login($pdo, $mail, $password){
        $mail = $pdo->quote($mail);
        $password = md5($password);
        //print $mail.' '.$password;
        $sql = "SELECT id, password FROM ts_users WHERE email=$mail";
        if(!$stmt = $pdo->query($sql)){
                return false;
            } else {
                $row = $stmt->fetch(PDO::FETCH_ASSOC);
                if(!$row){
                    return false; // нет такого мыла в базе
                } else {
                    $db_password = $row['password'];
                    $db_id = $row['id'];
                    
                    if($password == $db_password){
                        $hash = md5(rand(0, 6400000));
                        $sql_update = "UPDATE ts_users SET hash='$hash' WHERE id='$db_id'";
                        if($pdo->exec($sql_update)){
                            setcookie("id", $db_id, time() + 3600);
                            setcookie("hash", $hash, time() + 3600);
                            return true;
                        }else{
                            print 'Exception';
                        }
                    }
                    return false;
                }
            }
    }
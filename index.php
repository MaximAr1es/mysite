<?php 
    define(ROOT, $_SERVER['DOCUMENT_ROOT']);
    require(ROOT.'/sys/core.php');

    $page = route(1);
    $ext = route(2);
    
    //echo 'Запрашиваемая страница: '.$page;
    //echo '<br />Дополнительные данные: '.$ext;

    $pdo = init();
    
    switch($page){
        case 'login':
            $tpl = 'login';
            if($_SERVER['REQUEST_METHOD'] == 'POST'){
                $mail = clearStr($_POST['mail']);
                $password = clearStr($_POST['password']);
                if(strlen($mail) > 3 && strlen($password) > 2){
                    //print $mail.' '.$password;
                    if(login($pdo, $mail, $password)){
                        header("location: /user");
                    }else{
                        print 'Неверный логин или пароль';
                    }
                }
            }
            break;
        case 'logout':
            echo '<h2>Тут мы разлогиваемся</h2>';
            break;
        case 'register':
            $title = "Регистрация";
            $tpl = 'register';
            if($_SERVER['REQUEST_METHOD'] == 'POST'){
                $mail = clearStr($_POST['mail']);
                $password = clearStr($_POST['password']);
                $password_double = clearStr($_POST['password_double']);
                        
                if(!empty($mail) && !empty($password) && !empty($password_double)){
                    if($password != $password_double){
                        print 'Пароли не совпадают';
                    }else{
                        if(register($pdo, $mail, $password)){
                            header('location: /login');
                        }else{
                            print 'Регистрация не получилась :(';
                        }
                    }
                }
            }
            break;
        case 'user':
            echo '<h2>Профиль пользователя</h2>';
            break;
        case 'post':
            echo '<h2>Конкретный пост</h2>';
            break;
        case 'news':
            echo '<h2>Страница новостей</h2>';
            break;
        default:
            $title = "Страница по умолчанию";
            $tpl = "default";
    }

    include_once(ROOT.'/sys/templates/index.tpl.php');
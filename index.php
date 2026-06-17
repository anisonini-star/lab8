<?php
// Запуск сессии (должен быть первой командой ДО вывода HTML)
session_start();

// Подключение файла с пользователями
include('users.php');

// Обработка выхода из системы
if (isset($_GET['action']) && $_GET['action'] == 'exit') {
    // Очищаем все переменные сессии
    session_unset();
    // Уничтожаем сессию
    session_destroy();
    header('Location: index.php');
    exit;
}

// Обработка авторизации
if (isset($_POST['login']) && isset($_POST['password'])) {
    $login = $_POST['login'];
    $password = $_POST['password'];
    
    // Проверка логина и пароля
    if (isset($users[$login]) && $users[$login]['password'] == $password) {
        $_SESSION['user'] = $login;
        // Сохраняем дополнительную информацию о пользователе
        $_SESSION['user_email'] = $users[$login]['email'];
        $_SESSION['user_fullname'] = $users[$login]['fullname'];
    }
}

// Подключение файла с функциями преобразования BB-кодов
include('bbcodes.php');

// Проверка, авторизован ли пользователь
$is_authorized = isset($_SESSION['user']);

// Получение номера страницы (из GET параметра)
$page = isset($_GET['page']) ? (int)$_GET['page'] : 0;
$news_id = isset($_GET['news']) ? (int)$_GET['news'] : 0;

// Функция загрузки XML файла
function loadXML($filename) {
    if (file_exists($filename)) {
        return simplexml_load_file($filename);
    }
    return null;
}

// Функция получения данных из XML
function getXMLData($xml, $id) {
    if ($xml && isset($xml->data[$id])) {
        return $xml->data[$id];
    }
    return null;
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="author" content="Иванов Иван Иванович">
    <meta name="description" content="Сайт с сессиями и XML">
    <title>Мой сайт</title>
    <link rel="stylesheet" type="text/css" href="styles/style.css">
</head>
<body>

<div id="main">
    <?php include('header.php'); ?>
    
    <div id="content">
        <?php include('navigation.php'); ?>
        
        <div id="right">
            <?php
            // Если пользователь не авторизован, показываем форму входа
            if (!$is_authorized) {
            ?>
                <h2>Авторизация</h2>
                <p>Для доступа к содержимому сайта необходимо авторизоваться.</p>
                <form method="post">
                    <table border="0">
                        <tr>
                            <td>Логин:</td>
                            <td><input type="text" name="login"></td>
                        </tr>
                        <tr>
                            <td>Пароль:</td>
                            <td><input type="password" name="password"></td>
                        </tr>
                        <tr>
                            <td colspan="2">
                                <input type="submit" value="Войти">
                            </td>
                        </tr>
                    </table>
                </form>
                <p style="color: #888; font-size: 11px;">
                    <strong>Тестовые пользователи:</strong><br>
                    admin / 123<br>
                    user / 123
                </p>
            <?php
            } else {
                // Пользователь авторизован - показываем содержимое
                
                // Загружаем XML файлы
                $main_xml = loadXML('data/main.xml');
                $news_xml = loadXML('data/news.xml');
                $contacts_xml = loadXML('data/contacts.xml');
                
                // Вывод информации о пользователе
                echo '<div style="background: #e0f0ff; padding: 10px; margin-bottom: 15px; border-radius: 5px;">';
                echo '<strong>Добро пожаловать, ' . htmlspecialchars($_SESSION['user_fullname']) . '!</strong><br>';
                echo 'Логин: ' . htmlspecialchars($_SESSION['user']) . '<br>';
                echo 'E-mail: ' . htmlspecialchars($_SESSION['user_email']) . '<br>';
                echo '<a href="index.php?action=exit" style="color: red;">Выйти</a>';
                echo '</div>';
                
                // Обработка просмотра новости
                if ($news_id > 0) {
                    // Показываем полную новость
                    $news_xml = loadXML('data/news.xml');
                    $news = getXMLData($news_xml, $news_id - 1);
                    
                    if ($news) {
                        echo '<h2>' . htmlspecialchars($news->header) . '</h2>';
                        echo '<p style="font-size: 11px; color: #888;">' . $news->date . '</p>';
                        echo '<div>' . parse_bbcode($news->text) . '</div>';
                        echo '<p><a href="index.php">← Назад к новостям</a></p>';
                    } else {
                        echo '<p style="color: red;">Новость не найдена!</p>';
                    }
                }
                // Обработка страниц
                else if ($page > 0) {
                    // Проверяем, какая страница запрошена
                    if ($page == 1) {
                        // Страница "Главная"
                        $data = getXMLData($main_xml, 0);
                        if ($data) {
                            echo '<h2>' . htmlspecialchars($data->header) . '</h2>';
                            echo '<div>' . parse_bbcode($data->text) . '</div>';
                        }
                    } elseif ($page == 2) {
                        // Страница "Новости" - выводим список новостей
                        echo '<h2>Новости</h2>';
                        if ($news_xml && isset($news_xml->data)) {
                            $count = 0;
                            foreach ($news_xml->data as $news) {
                                $count++;
                                echo '<div style="border-bottom: 1px solid #ddd; padding: 10px 0;">';
                                echo '<h3><a href="index.php?news=' . $count . '">' . htmlspecialchars($news->header) . '</a></h3>';
                                echo '<p style="font-size: 11px; color: #888;">' . $news->date . '</p>';
                                echo '<div>' . parse_bbcode($news->shorttext) . '</div>';
                                echo '<p>' . parse_bbcode($news->addurl) . '</p>';
                                echo '</div>';
                            }
                        } else {
                            echo '<p>Новостей пока нет.</p>';
                        }
                    } elseif ($page == 3) {
                        // Страница "Контакты"
                        if ($contacts_xml && isset($contacts_xml->data)) {
                            $contacts = $contacts_xml->data[0];
                            echo '<h2>' . htmlspecialchars($contacts->header) . '</h2>';
                            echo '<div>' . parse_bbcode($contacts->text) . '</div>';
                        } else {
                            echo '<p>Контактная информация временно недоступна.</p>';
                        }
                    } else {
                        echo '<p style="color: red;">Страница не найдена!</p>';
                    }
                } else {
                    // Главная страница (по умолчанию)
                    $data = getXMLData($main_xml, 0);
                    if ($data) {
                        echo '<h2>' . htmlspecialchars($data->header) . '</h2>';
                        echo '<div>' . parse_bbcode($data->text) . '</div>';
                    } else {
                        echo '<p>Содержимое главной страницы недоступно.</p>';
                    }
                }
            }
            ?>
        </div>
        
        <div style="clear: both;"></div>
        <?php include('footer.php'); ?>
    </div>
</div>

</body>
</html>
<?php
  // начинаем работу с сессиями 
  session_start();
?>

<html>
<head>
  <meta http-equiv="content-type" content="text/html; charset=windows-1251">
  <title>Мой первый простой сайт</title>
  <link rel=STYLESHEET type="text/css" href="/styles/style.css"> 
</head>

<body>
  <div id="main">
    <div id="header">
       <?php include "header.php" ?> 
    </div>
    
    <div id="content">
      <div id="left">
        <?php include "navigation.php" ?>
        <?php include "links.php" ?>

        <?php include "users.php" ?>

        <?php
        // Получаем параметр action из GET-запроса
        $action = $_GET['action'];        
        
        // Если параметр action не задан, устанавливаем значение "none"
        if(!isset($action))
          $action = "none";
        
        // Обработка события выход с сайта
        // При переходе по ссылке "Выйти" уничтожаем все переменные сессии
        if($action == "exit") {
          session_unset();
        }
        
        // Авторизация пользователя
        // Проверяем: пользователь не авторизован, и переданы логин и пароль
        if(!isset($_SESSION["autorized"]) && isset($_POST['login']) && isset($_POST['pwd'])) {
          
          // Получаем переданные логин и пароль с формы
          $login = $_POST['login'];
          $pwd = $_POST['pwd'];
          
          // Сверяем полученный пароль с тем, что хранится в массиве $users
          // Теперь $users - это многомерный массив, где ключ - логин, 
          // а значение - массив с данными пользователя
          if(isset($users[$login])) {
             if($pwd == $users[$login]["pwd"]) {
              // Устанавливаем переменные сессии
              $_SESSION["autorized"] = true;
              $_SESSION["user"] = $login;
            }
          }
        }
        
        ?>
        
        <?php include "autorization.php" ?>
      </div>
    
       <?php include "bbcodes.php" ?>
       
      <div id="right">
      <?php
           
        // Получаем данные о том, какая страница/новость запрошены     
        $p = $_GET['page'];    // Номер страницы (1 - новости, 2 - контакты)
        $news = $_GET['news']; // Номер новости для просмотра
        
        // Проверка параметров: если параметры не числовые или не заданы,
        // устанавливаем значение 0 (главная страница)
        if(!is_numeric($p) || !isset($p))
          $p = 0;
        if(!is_numeric($news) || !isset($news))
          $news = 0;
        
        // Загрузка XML файла с данными в зависимости от запрошенной страницы
        switch($p) {
          case 1:  // Запрошен перечень новостей
            $s = simplexml_load_file('data/news.xml');
            break;
          case 2:  // Запрошена страница контактов
            $s = simplexml_load_file('data/contacts.xml');
            break;
          default: // Главная страница или конкретная новость
            if($news == 0)
              // Запрошена главная страница
              $s = simplexml_load_file('data/main.xml');
            else
              // Запрошена конкретная новость
              $s = simplexml_load_file('data/news.xml');
        } 
        
        // Вывод главной страницы (page=0, news=0)
        if(($p == 0) && ($news == 0)) {
          // Проходим по всем элементам данных в XML
          for($i = 0; $i < $s->datacount[0]; $i++) {
            // Выводим заголовок с конвертацией из UTF-8 в windows-1251
            echo "<H2>".iconv("UTF-8", "windows-1251",$s->data[$i]->header)."</H2>";  
            // Выводим текст с обработкой BB-кодов
            echo parse_bb_codes(iconv("UTF-8", "windows-1251",$s->data[$i]->text));
          }
        }
        // Вывод перечня новостей (page=1, news=0) - доступ только авторизованным
        else if(($p == 1) && ($news == 0) && ($_SESSION["autorized"] == true)) {
          // Проходим по всем новостям в XML
          for($i = 0; $i < $s->datacount[0]; $i++) {
            echo "<H2>".iconv("UTF-8", "windows-1251",$s->data[$i]->header)."</H2>";  
            echo "<H4>".iconv("UTF-8", "windows-1251",$s->data[$i]->date)."</H4>";
            // Выводим краткий текст новости
            echo parse_bb_codes(iconv("UTF-8", "windows-1251",$s->data[$i]->shorttext));
            // Выводим ссылку "Подробнее..."
            echo parse_bb_codes(iconv("UTF-8", "windows-1251",$s->data[$i]->addurl));
          }
        }
        // Вывод конкретной новости (page=0, news!=0) - доступ только авторизованным
        else if(($p == 0) && ($news != 0) && ($_SESSION["autorized"] == true)) {
            // Выводим новость с индексом news-1 (так как нумерация с 0)
            echo "<H2>".iconv("UTF-8", "windows-1251",$s->data[$news - 1]->header)."</H2>";  
            echo "<H4>".iconv("UTF-8", "windows-1251",$s->data[$news - 1]->date)."</H4>";
            echo parse_bb_codes(iconv("UTF-8", "windows-1251",$s->data[$news - 1]->text));
        }
        // Вывод страницы контактов (page=2, news=0)
        else if(($p == 2) && ($news == 0)) {
          // Загружаем файл contacts.xml
          $s = simplexml_load_file('data/contacts.xml');
          if($s) {
            // Выводим заголовок и текст страницы контактов
            echo "<H2>".iconv("UTF-8", "windows-1251",$s->data[0]->header)."</H2>";
            echo parse_bb_codes(iconv("UTF-8", "windows-1251",$s->data[0]->text));
          } else {
            echo "<p>Страница контактов временно недоступна</p>";
          }
        }
        // Если ни одно условие не подошло - показываем ошибку доступа
        else {
          include "error.php";
        }
      ?>  
      
      </div>

      <div style="clear: both;"></div>
      <div id="footer">
        <?php include "footer.php" ?>
      </div>
    </div>
  </div>
</body>
</html>

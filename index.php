<?php
  // Начинаем работу с сессиями - должно быть вызвано до любого вывода
  session_start();
?>

<html>
<head>
  <base href="http://lab8/">
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
        // Получаем параметр action из GET-запроса (например, для выхода)
        $action = $_GET['action'];        
        
        if(!isset($action))
          $action = "none";
        
        // Обработка события выхода с сайта - уничтожаем все переменные сессии
        if($action == "exit") {
          session_unset(); // Удаляет все переменные сессии
        }
        
        // Авторизация - проверяем, если пользователь не авторизован и переданы логин/пароль
        if(!isset($_SESSION["autorized"]) && isset($_POST['login']) && isset($_POST['pwd'])) {
          
          // Получаем переданные логин и пароль с формы
          $login = $_POST['login'];
          $pwd = $_POST['pwd'];
          
          // Сверяем полученный пароль с теми, что хранятся в массиве $users
          if(isset($users[$login])) {
             if($pwd == $users[$login]['pwd']) { // Изменено для работы с новым массивом
              // Устанавливаем переменные сессии
              $_SESSION["autorized"] = true;
              $_SESSION["user"] = $login;
              // Сохраняем email пользователя в сессии
              $_SESSION["email"] = $users[$login]['email'];
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
        $p = $_GET['page'];
        $news = $_GET['news'];
        
        // Проверка параметров - приводим к числовым значениям
        if(!is_numeric($p) || !isset($p))
          $p = 0;
        if(!is_numeric($news) || !isset($news))
          $news = 0;
        
        // Загрузка XML файла с данными в зависимости от запроса
        switch($p) {
          // Запрошен перечень новостей (страница 1)
          case 1:
            $s = simplexml_load_file('data/news.xml');
            break;
          
          // Страница контактов (страница 2)
          case 2:
            $s = simplexml_load_file('data/contacts.xml');
            break;
            
	        default:
            if($news == 0)
              // Запрошена главная страница
	            $s = simplexml_load_file('data/main.xml');
            else
              // Запрошена конкретная новость
              $s = simplexml_load_file('data/news.xml');
        } 
        
        // Вывод главной страницы
        if(($p == 0) && ($news == 0))
          for($i = 0; $i < $s->datacount[0]; $i++) {
            echo "<H2>".iconv("UTF-8", "windows-1251",$s->data[$i]->header)."</H2>";  
            echo parse_bb_codes(iconv("UTF-8", "windows-1251",$s->data[$i]->text));
          }
        else if(($p == 1) && ($news == 0) && ($_SESSION["autorized"] == true)) 
          // Вывод перечня новостей (только для авторизованных)
          for($i = 0; $i < $s->datacount[0]; $i++) {
            echo "<H2>".iconv("UTF-8", "windows-1251",$s->data[$i]->header)."</H2>";  
            echo "<H4>".iconv("UTF-8", "windows-1251",$s->data[$i]->date)."</H4>";
            echo parse_bb_codes(iconv("UTF-8", "windows-1251",$s->data[$i]->shorttext));
            echo parse_bb_codes(iconv("UTF-8", "windows-1251",$s->data[$i]->addurl));
          }
        else if(($p == 0) && ($news != 0) && ($_SESSION["autorized"] == true)) {
            // Вывод конкретной новости (только для авторизованных)
            echo "<H2>".iconv("UTF-8", "windows-1251",$s->data[$news - 1]->header)."</H2>";  
            echo "<H4>".iconv("UTF-8", "windows-1251",$s->data[$news - 1]->date)."</H4>";
            echo parse_bb_codes(iconv("UTF-8", "windows-1251",$s->data[$news - 1]->text));
        }
        // Вывод страницы контактов (страница 2)
        else if(($p == 2) && ($_SESSION["autorized"] == true)) {
          for($i = 0; $i < $s->datacount[0]; $i++) {
            echo "<H2>".iconv("UTF-8", "windows-1251",$s->data[$i]->header)."</H2>";  
            echo parse_bb_codes(iconv("UTF-8", "windows-1251",$s->data[$i]->text));
          }
        }
        else
          include "error.php"; // Сообщение об ошибке доступа
          
        // Код отображения страницы контактов написан самостоятельно (см. выше)
      ?>  
      
      </div>

      <div style="clear: both;"></div>
      <div id="footer">
        <?php include "footer.php" ?>
      </div>
    </div>
  </div>
</body>
<html>

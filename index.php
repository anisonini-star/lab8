<?php
  // начинаем работу с сессиями 
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

        <?php  include "users.php" ?>

        <?php
        
        $action = $_GET['action'];        
        
        if(!isset($action))
          $action = "none";
        
        // обработка события выход с сайта
        if($action == "exit") {
          session_unset();
        }
        
        // авторизация
        if(!isset($_SESSION["autorized"]) && isset($_POST['login']) && isset($_POST['pwd'])) {
          
          // получаем переданные логин и пароль с формы
          $login = $_POST['login'];
          $pwd = $_POST['pwd'];
          
          
          // сверим полученный пароль с теми то хранятся в массиве $users
          if(isset($users[$login])) {
             if($pwd == $users[$login]) {
              // установим переменные сесии
              $_SESSION["autorized"] = true;
              $_SESSION["user"] = $login;
            }
          }
        }
        
        ?>
        
        <?php include "autorization.php" ?>
      </div>
    
       <?php  include "bbcodes.php" ?>
       
      <div id="right">
      <?php
           
        // получаем данные о том какая страница/новость запрошены     
        $p = $_GET['page'];
        $news = $_GET['news'];
        
        // проверка параметров 
        if(!is_numeric($p) || !isset($p))
          $p = 0;
        if(!is_numeric($news) || !isset($news))
          $news = 0;
        
        // загрузка xml файла с данными
        switch($p) {
          // запрошен перечень новостей
          case 1:
            $s = simplexml_load_file('data/news.xml');
            break;
            
	        default:
            if($news == 0)
              // запрошена главная страница
	            $s = simplexml_load_file('data/main.xml');
            else
              // запрошена конкретная новость
              $s = simplexml_load_file('data/news.xml');
        } 
        
        // вывод главной страницы
        if(($p == 0) && ($news == 0))
          for($i = 0; $i < $s->datacount[0]; $i++) {
            echo "<H2>".iconv("UTF-8", "windows-1251",$s->data[$i]->header)."</H2>";  
            echo parse_bb_codes(iconv("UTF-8", "windows-1251",$s->data[$i]->text));
          }
        else if(($p == 1) && ($news == 0) && ($_SESSION["autorized"] == true)) 
          // вывод перечня новостей
          for($i = 0; $i < $s->datacount[0]; $i++) {
            echo "<H2>".iconv("UTF-8", "windows-1251",$s->data[$i]->header)."</H2>";  
            echo "<H4>".iconv("UTF-8", "windows-1251",$s->data[$i]->date)."</H4>";
            echo parse_bb_codes(iconv("UTF-8", "windows-1251",$s->data[$i]->shorttext));
            echo parse_bb_codes(iconv("UTF-8", "windows-1251",$s->data[$i]->addurl));
          }
        else if(($p == 0) && ($news != 0) && ($_SESSION["autorized"] == true)) {
            // вывод новости
            echo "<H2>".iconv("UTF-8", "windows-1251",$s->data[$news - 1]->header)."</H2>";  
            echo "<H4>".iconv("UTF-8", "windows-1251",$s->data[$news - 1]->date)."</H4>";
            echo parse_bb_codes(iconv("UTF-8", "windows-1251",$s->data[$news - 1]->text));
        }
        else
          include "error.php";
          
        // код отображения страницы контактов написать самостоятельно
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
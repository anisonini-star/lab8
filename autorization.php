<?php if($_SESSION["autorized"] != true) { ?>
   <h2>Авторизация</h2>
   <form method="post">
   Логин: 
   <div align="right"><input name="login" type="text"></div>  
   Пароль: 
   <div align="right"><input name="pwd" type="password"></div>
   <div align="right"><input name="ok" type="submit" value="Войти"></div>
   </form>
 <?php } 
   else {
?>
<h2>Пользователь:</h2>
<p align = "center">
<?php echo $_SESSION["user"]; ?>
</p>
<!-- Вывод дополнительной информации о пользователе -->
<p align="center">
<?php 
// Получаем данные о пользователе из сессии
$user_login = $_SESSION["user"];
// Для получения полных данных нужен доступ к массиву users
// Включаем файл users.php, если еще не включен
include_once "users.php";
if(isset($users[$user_login])) {
    echo "Email: " . $users[$user_login]["email"] . "<br>";
    echo "Полное имя: " . $users[$user_login]["fullname"];
}
?>
</p>
<div align="center"><a href="index.php?action=exit">Выйти</a></div>
   <?php } ?>

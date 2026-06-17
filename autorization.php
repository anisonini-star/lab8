<?php if($_SESSION["autorized"] != true) { ?>
   <h2>Авторизация</h2>
   <form method="post">
   Пользователь: 
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
<p align = "center">
Email: <?php echo $_SESSION["email"]; ?>
</p>
<div align="center"><a href="index.php?action=exit">Выйти</a></div>
   <?php } ?>

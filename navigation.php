<div id="left">
    <h2>Навигация</h2>
    <ul>
        <li><a href="index.php?page=1">Главная</a></li>
        <li><a href="index.php?page=2">Новости</a></li>
        <li><a href="index.php?page=3">Контакты</a></li>
    </ul>
    <h2>Ссылки</h2>
    <ul>
        <li><a href="http://yandex.ua" target="_blank">Яндекс</a></li>
        <li><a href="http://google.com.ua" target="_blank">Google</a></li>
    </ul>
    <h2>Пользователь</h2>
    <ul>
        <?php if (isset($_SESSION['user'])): ?>
            <li style="list-style-type: none; color: #2979BD;">
                <strong><?php echo htmlspecialchars($_SESSION['user_fullname']); ?></strong><br>
                <a href="index.php?action=exit">Выйти</a>
            </li>
        <?php else: ?>
            <li style="list-style-type: none; color: #2979BD;">Гость</li>
        <?php endif; ?>
    </ul>
</div>
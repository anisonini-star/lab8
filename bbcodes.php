<?php
/**
 * Функция преобразования BB-кодов в HTML
 * 
 * @param string $text Текст с BB-кодами
 * @return string Текст с заменёнными BB-кодами на HTML
 */
function parse_bbcode($text) {
    // Массив соответствий BB-кодов и HTML-кодов
    $bb_codes = array(
        // Абзацы
        '/\[p\](.*?)\[\/p\]/s' => '<p>$1</p>',
        
        // Жирный текст
        '/\[b\](.*?)\[\/b\]/s' => '<strong>$1</strong>',
        
        // Курсив
        '/\[i\](.*?)\[\/i\]/s' => '<em>$1</em>',
        
        // Подчеркнутый текст
        '/\[u\](.*?)\[\/u\]/s' => '<u>$1</u>',
        
        // Заголовки
        '/\[h1\](.*?)\[\/h1\]/s' => '<h1>$1</h1>',
        '/\[h2\](.*?)\[\/h2\]/s' => '<h2>$1</h2>',
        '/\[h3\](.*?)\[\/h3\]/s' => '<h3>$1</h3>',
        
        // Ссылки
        '/\[url\=(.*?)\](.*?)\[\/url\]/s' => '<a href="$1" target="_blank">$2</a>',
        '/\[url\](.*?)\[\/url\]/s' => '<a href="$1" target="_blank">$1</a>',
        
        // Изображения
        '/\[img\](.*?)\[\/img\]/s' => '<img src="$1" alt="Изображение" style="max-width: 100%;">',
        
        // Маркированный список
        '/\[ul\](.*?)\[\/ul\]/s' => '<ul>$1</ul>',
        '/\[li\](.*?)\[\/li\]/s' => '<li>$1</li>',
        
        // Цитата
        '/\[quote\](.*?)\[\/quote\]/s' => '<blockquote>$1</blockquote>',
        
        // Код
        '/\[code\](.*?)\[\/code\]/s' => '<pre><code>$1</code></pre>',
        
        // Новая строка
        '/\[br\]/s' => '<br>',
        
        // Красная строка (5 пробелов)
        '/\[bl\]/s' => '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;',
        
        // Цветной текст
        '/\[color=(.*?)\](.*?)\[\/color\]/s' => '<span style="color:$1">$2</span>',
        
        // Размер шрифта
        '/\[size=(.*?)\](.*?)\[\/size\]/s' => '<span style="font-size:$1px">$2</span>'
    );
    
    // Применяем все замены
    $result = $text;
    foreach ($bb_codes as $pattern => $replacement) {
        $result = preg_replace($pattern, $replacement, $result);
    }
    
    return $result;
}

// Дополнительная функция для обработки строк с переносами
function parse_bbcode_nl($text) {
    $text = parse_bbcode($text);
    // Заменяем \n на <br> (если не обработаны через [br])
    $text = nl2br($text);
    return $text;
}
?>
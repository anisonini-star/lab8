<?php
// Функция обрабатывающая bb коды
// Поддерживает преобразование следующих bb кодов в html теги
//[p] [/p] => <p> </p>
//[b] [/b] => <b> </b>
//[i] [/i] => <em> </em>
//[u] [/u] => <u> </u>
//[img]url[/img] => <img src = url> 
//[url=url]текст[/url] => <a href = url>текст</a> 
//[url]url[/url] => <a href = url> url </a> 
//[br] => <br>
//[bl] => 5 пробелов (красная строка)
function parse_bb_codes($var) {
      $search = array(
                '/\[p\](.*?)\[\/p\]/is',
                '/\[b\](.*?)\[\/b\]/is',
                '/\[i\](.*?)\[\/i\]/is',
                '/\[u\](.*?)\[\/u\]/is',
                '/\[img\](.*?)\[\/img\]/is',
                '/\[url\](.*?)\[\/url\]/is',
                '/\[url\=(.*?)\](.*?)\[\/url\]/is',
                '/\[br\]/is',
                '/\[bl\]/is'  // Добавлен новый bb-код для красной строки
                );

      $replace = array(
                '<p>$1</p>',
                '<strong>$1</strong>',
                '<em>$1</em>',
                '<u>$1</u>',
                '<img src="$1" />',
                '<a href="$1">$1</a>',
                '<a href="$1">$2</a>',
                '<br>',
                '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'  // 5 неразрывных пробелов
                );

      $var = preg_replace ($search, $replace, $var);
      return $var;
    }	
?>

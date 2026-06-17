<?php
function parse_bb_codes($var) {
      $search = array(
                '/\[p\](.*?)\[\/p\]/is',
                '/\[b\](.*?)\[\/b\]/is',
                '/\[i\](.*?)\[\/i\]/is',
                '/\[u\](.*?)\[\/u\]/is',
                '/\[img\](.*?)\[\/img\]/is',
                '/\[url\](.*?)\[\/url\]/is',
                '/\[url\=(.*?)\](.*?)\[\/url\]/is',
                '/\[br\]/',
                '/\[bl\]/'  // новый bb-код для красной строки
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
                '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;' // 5 неразрывных пробелов
                );

      $var = preg_replace ($search, $replace, $var);
      return $var;
    }	
?>

<?php

if(!function_exists('get_called_class')) {
function get_called_class() {
    $bt = debug_backtrace();
    $l = 0;
    do {
        $l++;
        $lines = file($bt[$l]['file']);
        $callerLine = $lines[$bt[$l]['line']-1];
        preg_match('/([a-zA-Z0-9\_]+)::'.$bt[$l]['function'].'/',
                   $callerLine,
                   $matches);

       if ($matches[1] == 'self') {
               $line = $bt[$l]['line']-1;
               while ($line > 0 && strpos($lines[$line], 'class') === false) {
                   $line--;
               }
               preg_match('/class[\s]+(.+?)[\s]+/si', $lines[$line], $matches);
       }
    }
    while ($matches[1] == 'parent'  && $matches[1]);
    return $matches[1];
  }
}


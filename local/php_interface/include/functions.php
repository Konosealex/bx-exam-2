<?php

function dump($var) {
    echo '<pre>';
    var_dump($var);
    echo '</pre>';
}

function wtf($var) {
    Bitrix\Main\Diag\Debug::writeToFile($var);
}
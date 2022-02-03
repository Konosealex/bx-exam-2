<?php if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) {
    die();
}

/** @var array $arParams */
/** @var array $arResult */
/** @global CMain $APPLICATION */

if ($arParams['SET_SPECIALDATE'] == 'Y') {
    $APPLICATION->SetPageProperty('specialdate', $arResult['LAST_NEWS_DATE']);
}
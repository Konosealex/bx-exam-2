<?php if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) {
    die();
}

/** @var array $arParams */
/** @var array $arResult */

if ($arParams['SET_SPECIALDATE'] == 'Y') {
    $lastNews = reset($arResult['ITEMS']);
    $arResult['LAST_NEWS_DATE'] = $lastNews['ACTIVE_FROM'];
    $this->getComponent()->SetResultCacheKeys(['LAST_NEWS_DATE']);
}
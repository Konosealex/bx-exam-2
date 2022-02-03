<?php if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) {
    die();
}

/** @var array $arParams */
/** @var array $arResult */
/** @global CMain $APPLICATION */

if ($arParams['CANONICAL_IBLOCK_ID']) {
    $APPLICATION->SetPageProperty('canonical', $arResult['CANONICAL_VALUE']);
}

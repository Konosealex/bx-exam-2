<?php if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) {
    die();
}

/** @var array $arParams */
/** @var array $arResult */

if ($arParams['CANONICAL_IBLOCK_ID']) {
    $arFilter = [
        "IBLOCK_ID"          => $arParams['CANONICAL_IBLOCK_ID'],
        "PROPERTY_NEWS_LINK" => $arResult['ID'],
        "ACTIVE"             => 'Y'
    ];
    $arSelect = [
        "ID",
        "NAME",
    ];
    $obElement = CIBlockElement::GetList([], $arFilter, false, false, $arSelect);
    $arElement = $obElement->GetNext();
    if ($arElement) {
        $arResult['CANONICAL_VALUE'] = $arElement['NAME'];
        $this->getComponent()->SetResultCacheKeys(['CANONICAL_VALUE']);
    }
}
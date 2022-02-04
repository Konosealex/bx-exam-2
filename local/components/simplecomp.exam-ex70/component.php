<?php

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) {
    die();
}

use Bitrix\Main\Loader,
    Bitrix\Iblock;

/** @var array $arParams */
/** @var array $arResult */
/** @global CMain $APPLICATION */

if (!Loader::includeModule("iblock")) {
    ShowError(GetMessage("SIMPLECOMP_EXAM2_IBLOCK_MODULE_NONE"));
    return;
}

$productionIblockId = (int)$arParams['PRODUCTS_IBLOCK_ID'];
$newsIblockId = (int)$arParams['NEWS_IBLOCK_ID'];
$propertyCode = (string)$arParams['PROPERTY_CODE'];

if (!$productionIblockId || !$newsIblockId || !$propertyCode) {
    ShowError(GetMessage("SIMPLECOMP_EXAM2_IBLOCK_PARAMS_NONE"));
    return;
}

if ($this->StartResultCache()) {
    $newsIds = [];
    $newsList = [];
    $sectionIds = [];
    $sectionList = [];

    $arFilter = [
        "IBLOCK_ID" => $newsIblockId,
        "ACTIVE"    => "Y"
    ];
    $arSelect = [
        "ID",
        "NAME",
        "ACTIVE_FROM"
    ];

    $obNews = CIBlockElement::GetList([], $arFilter, false, false, $arSelect);
    while ($arNews = $obNews->GetNext()) {
        $newsIds[] = $arNews['ID'];
        $newsList[$arNews['ID']] = $arNews;
    }

    $arFilter = [
        "IBLOCK_ID"   => $productionIblockId,
        "ACTIVE"      => "Y",
        $propertyCode => $newsIds,
        'CNT_ACTIVE'
    ];
    $arSelect = [
        "ID",
        "IBLOCK_ID",
        "NAME",
        $propertyCode
    ];

    $obSections = CIBlockSection::GetList([], $arFilter, true, $arSelect);
    while ($arSection = $obSections->GetNext()) {
        $sectionIds[] = $arSection['ID'];
        $sectionList[$arSection['ID']] = $arSection;
    }

    $arFilter = [
        'IBLOCK_ID'  => $productionIblockId,
        'ACTIVE'     => 'Y',
        'SECTION_ID' => $sectionIds
    ];
    $arSelect = [
        "ID",
        "NAME",
        'IBLOCK_SECTION_ID',
        'IBLOCK_ID',
        'PROPERTY_ARTNUMBER',
        'PROPERTY_MATERIAL',
        'PROPERTY_PRICE'
    ];

    $obProduct = CIBlockElement::GetList([], $arFilter, false, false, $arSelect);
    while ($arProduct = $obProduct->GetNext()) {
        foreach ($sectionList[$arProduct['IBLOCK_SECTION_ID']]['UF_NEWS_LINK'] as $newsId) {
            $newsList[$newsId]['PRODUCTS'][] = $arProduct;
        }
    }

    $arResult['PRODUCT_CNT'] = 0;
    foreach ($sectionList as $section) {
        $arResult['PRODUCT_CNT'] += $section['ELEMENT_CNT'];
        foreach ($section['UF_NEWS_LINK'] as $newsId) {
            $newsList[$newsId]['SECTIONS'][] = $section['NAME'];
        }
    }

    $arResult['NEWS'] = $newsList;

    $this->SetResultCacheKeys(['PRODUCT_CNT']);

    $this->includeComponentTemplate();
}

$APPLICATION->SetPageProperty('title', GetMessage('SET_TITLE') . $arResult['PRODUCT_CNT']);
$APPLICATION->SetTitle(GetMessage('H1') . $arResult['PRODUCT_CNT']);
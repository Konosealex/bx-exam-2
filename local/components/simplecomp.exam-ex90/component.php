<?php

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) {
    die();
}

/** @var array $arParams */
/** @var array $arResult */

/** @global CMain $APPLICATION */

use Bitrix\Main\Loader,
    Bitrix\Iblock;

if (!Loader::includeModule("iblock")) {
    ShowError(GetMessage("SIMPLECOMP_EXAM2_IBLOCK_MODULE_NONE"));
    return;
}

$productionIblockId = (int)$arParams['PRODUCTS_IBLOCK_ID'];
$servicesIblockId = (int)$arParams['SERVICES_IBLOCK_ID'];
$classifaerProperty = (string)$arParams['CLASSIFICATOR_PROPERTY_CODE'];
$newsCount = (string)$arParams['NEWS_COUNT'];

if (!$productionIblockId || !$classifaerProperty || !$servicesIblockId) {
    ShowError(GetMessage("SIMPLECOMP_EXAM2_IBLOCK_PARAMS_NONE"));
    return;
}

if ($newsCount <= 0) {
    $newsCount = 20;
}

$arNavParams = [
    "nPageSize"          => $newsCount,
    "bDescPageNumbering" => false,
    "bShowAll"           => false,
];
$arNavigation = CDBResult::GetNavParams($arNavParams);

if ($this->startResultCache(false, [$arNavigation])) {
    $arFilter = [
        "IBLOCK_ID" => $servicesIblockId,
        "ACTIVE"    => "Y",
    ];
    $arSelect = [
        "ID",
        "NAME",
    ];
    $arResult["CLASSIFIER"] = [];
    $arResult["CLASSIFIER_IDS"] = [];
    $obSection = CIBlockSection::GetList([], $arFilter, false, $arSelect);
    while ($arSection = $obSection->GetNext()) {
        $arResult["CLASSIFIER"][$arSection["ID"]] = $arSection;
        $arResult["CLASSIFIER_IDS"][] = $arSection["ID"];
    }

    $arFilter = [
        "IBLOCK_ID"                       => $productionIblockId,
        "ACTIVE"                          => "Y",
        'PROPERTY_' . $classifaerProperty => $arResult["CLASSIFIER_IDS"]
    ];
    $arSelect = [
        "ID",
        'IBLOCK_ID',
        "NAME",
    ];
    $arResult["ELEMENTS"] = [];
    $obProduct = CIBlockElement::GetList([], $arFilter, false, $arNavParams, $arSelect);
    while ($arProduct = $obProduct->GetNextElement()) {
        $arProductList = $arProduct->GetFields();
        $arProductList["PROPERTY"] = $arProduct->GetProperties();

        foreach ($arProductList["PROPERTY"][$classifaerProperty]["VALUE"] as $value) {
            $arResult["CLASSIFIER"][$value]["ELEMENTS_ID"][] = $arProductList["ID"];
        }
        $arResult["ELEMENTS"][$arProductList["ID"]] = $arProductList;
    }
    $arResult["COUNT_ELEM"] = count($arResult["ELEMENTS"]);
    $arResult["NAV_STRING"] = $obProduct->GetPageNavString("Странички");

    $this->SetResultCacheKeys(["COUNT_ELEM"]);


    $this->includeComponentTemplate();
}

$APPLICATION->SetTitle(GetMessage('H1') . $arResult["COUNT_ELEM"]);

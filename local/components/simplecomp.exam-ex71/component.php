<?php

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) {
    die();
}

/** @var array $arParams */
/** @var array $arResult */
/** @global CUser $USER */

/** @global CMain $APPLICATION */

use Bitrix\Main\Loader,
    Bitrix\Iblock;

if (!Loader::includeModule("iblock")) {
    ShowError(GetMessage("SIMPLECOMP_EXAM2_IBLOCK_MODULE_NONE"));
    return;
}

$productionIblockId = (int)$arParams['PRODUCTS_IBLOCK_ID'];
$classificatorIblockId = (int)$arParams['CLASSIFICATOR_IBLOCK_ID'];
$propertyCode = (string)$arParams['PROPERTY_CODE'];
$detailLinkTemplate = (string)$arParams['DETAIL_LINK'];

if (!$productionIblockId || !$classificatorIblockId || !$propertyCode || !$detailLinkTemplate) {
    ShowError(GetMessage("SIMPLECOMP_EXAM2_IBLOCK_PARAMS_NONE"));
    return;
}

$arUserGroups = $USER->GetUserGroupArray();

if ($this->StartResultCache(false, [$arUserGroups])) {
    $arResult["CLASSIFIER"] = [];

    $arFilter = [
        "IBLOCK_ID"         => $classificatorIblockId,
        "CHECK_PERMISSIONS" => "Y",
        "ACTIVE"            => "Y",
    ];
    $arSelect = [
        "ID",
        "IBLOCK_ID",
        "NAME",
    ];

    $obClassificator = CIBlockElement::GetList([], $arFilter, false, false, $arSelect);
    while ($arClassificator = $obClassificator->GetNext()) {
        $arResult["CLASSIFIER_ID"][] = $arClassificator["ID"];
        $arResult["CLASSIFIER"][$arClassificator["ID"]] = $arClassificator;
    }

    $arOrder = [
        "NAME" => "ASC",
        "SORT" => "ASC"
    ];
    $arFilter = [
        "IBLOCK_ID"                 => $productionIblockId,
        "PROPERTY_" . $propertyCode => $arResult["CLASSIFIER_ID"],
        "CHECK_PERMISSIONS"         => "Y",
        "ACTIVE"                    => "Y",
    ];
    $arSelect = [
        "ID",
        "IBLOCK_ID",
        "NAME",
        "PREVIEW_TEXT",
        'DETAIL_PAGE_URL'
    ];

    $arResult["ELEMENTS"] = [];

    $obProduct = CIBlockElement::GetList($arOrder, $arFilter, false, false, $arSelect);
    $obProduct->SetUrlTemplates($detailLinkTemplate);
    while ($arProduct = $obProduct->GetNextElement()) {
        $arProductList = $arProduct->GetFields();
        $arProductList["PROPERTY"] = $arProduct->GetProperties();

        foreach ($arProductList["PROPERTY"]["COMPANY"]["VALUE"] as $product) {
            $arResult["CLASSIFIER"][$product]["ELEMENTS_ID"][] = $arProductList["ID"];
        }

        $arResult["ELEMENTS"][$arProductList["ID"]] = $arProductList;
    }

    $arResult["COUNT_CLASSIFIER"] = count($arResult["CLASSIFIER"]);

    $this->SetResultCacheKeys(["COUNT_CLASSIFIER"]);
}
$this->includeComponentTemplate();

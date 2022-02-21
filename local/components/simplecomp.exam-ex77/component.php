<?php

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) {
    die();
}

/** @var array $arParams */
/** @var array $arResult */

/** @global CMain $APPLICATION */

/** @global CUser $USER */

use Bitrix\Main\Loader,
    Bitrix\Iblock;

if (!Loader::includeModule("iblock")) {
    ShowError(GetMessage("SIMPLECOMP_EXAM2_IBLOCK_MODULE_NONE"));
    return;
}

$productionIblockId = (int)$arParams['PRODUCTS_IBLOCK_ID'];
$classificatorIblockId = (int)$arParams['CLASSIFICATOR_IBLOCK_ID'];
$propertyCode = (string)$arParams['PROPERTY_CODE'];

if (!$propertyCode || !$classificatorIblockId || !$productionIblockId) {
    ShowError(GetMessage("SIMPLECOMP_EXAM2_IBLOCK_PARAMS_NONE"));
    return;
}

if ($USER->IsAuthorized()) {

    $this->AddIncludeAreaIcon(
        [
            'URL' => $APPLICATION->GetCurUri("hello=world"),
            'TITLE' => "Hello world"
        ]
    );
}

if ($this->StartResultCache()) {
    $arFilter = [
        "IBLOCK_ID" => $classificatorIblockId,
        "ACTIVE"    => "Y"
    ];
    $arSelect = [
        "ID",
        "NAME",
    ];

    $arResult["ITEMS"] = [];
    $obSections = CIBlockSection::GetList([], $arFilter, false, $arSelect);
    while ($arSection = $obSections->GetNext()) {
        $arResult["ITEMS"][$arSection['ID']] = $arSection;
    }

    $arFilter = [
        "IBLOCK_ID" => $productionIblockId,
        "ACTIVE"    => "Y"
    ];
    $arSelect = [
        "ID",
        "NAME",
        $propertyCode
    ];

    $arResult["SECTIONS"] = [];
    $obSections = CIBlockSection::GetList([], $arFilter, false, $arSelect);
    while ($arSection = $obSections->GetNext()) {
        if ($arSection[$propertyCode] > 0) {
            $arResult["ITEMS"][$arSection[$propertyCode]]["LINK_SECTIONS"][] = $arSection["ID"];
        }
        $arResult["SECTIONS"][$arSection["ID"]] = $arSection;
    }

    $arFilter = [
        "IBLOCK_ID" => $productionIblockId,
        "ACTIVE"    => "Y"
    ];
    $arSelect = [
        "ID",
        "IBLOCK_SECTION_ID",
        "NAME",
        "PREVIEW_TEXT",
        "PROPERTY_PRICE",
        "PROPERTY_MATERIAL",
        "PROPERTY_ARTNUMBER",
        'DETAIL_PAGE_URL'
    ];

    $arResult["ELEMENTS"] = [];
    $obElements = CIBlockElement::GetList([], $arFilter, false, false, $arSelect);
    while ($arElement = $obElements->GetNext()) {
        if ($arResult["ITEMS"][$arResult["SECTIONS"][$arElement["IBLOCK_SECTION_ID"]][$propertyCode]] > 0) {
            $arResult["ITEMS"][$arResult["SECTIONS"][$arElement["IBLOCK_SECTION_ID"]][$propertyCode]]["LINK_ELEMENTS"][] = $arElement["ID"];
        }

        $arResult["ELEMENTS"][$arElement["ID"]] = $arElement;
    }

    $arResult['SECTIONS_COUNT'] = count($arResult["SECTIONS"]);

    $this->SetResultCacheKeys(['SECTIONS_COUNT']);

    $this->includeComponentTemplate();
}

$APPLICATION->SetTitle(GetMessage('H1') . $arResult['SECTIONS_COUNT']);

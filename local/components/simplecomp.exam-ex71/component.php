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

$request = \Bitrix\Main\Application::getInstance()->getContext()->getRequest();
$bFilter = false;
if ($request->get('F') == 'Y') {
    $bFilter = true;
}

$arUserGroups = $USER->GetUserGroupArray();

if ($this->StartResultCache(false, [$arUserGroups, $bFilter])) {
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

    if ($bFilter) {
        $arFilter[] = [
            "LOGIC" => "OR",
            ["<=PROPERTY_PRICE" => 1700, "=PROPERTY_MATERIAL" => "Дерево, ткань"],
            ["<PROPERTY_RADIUS" => 1500, "=PROPERTY_MATERIAL" => "Металл, пластик"]
        ];
        $this->AbortResultCache();
    }
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

        $arButtons = CIBlock::GetPanelButtons(
            $productionIblockId,
            $arProductList['ID'],
            0,
            ["SECTION_BUTTONS" => false, "SESSID" => false]
        );
        $arItem["EDIT_LINK"] = $arButtons["edit"]["edit_element"]["ACTION_URL"];
        $arItem["DELETE_LINK"] = $arButtons["edit"]["delete_element"]["ACTION_URL"];
        $arResult["ITEMS"][$arProductList["ID"]][] = $arItem;

        $arResult["ELEMENTS"][$arProductList["ID"]] = $arProductList;
    }

    $arResult["COUNT_CLASSIFIER"] = count($arResult["CLASSIFIER"]);

    $filterUrl = $APPLICATION->GetCurPage() . '?F=Y';
    $arResult['FILTER_LINK'] = "<a href='{$filterUrl}'>{$filterUrl}</a>";

    $this->SetResultCacheKeys(["COUNT_CLASSIFIER"]);

    $this->includeComponentTemplate();
}

$APPLICATION->SetTitle(GetMessage('H1') . $arResult['COUNT_CLASSIFIER']);

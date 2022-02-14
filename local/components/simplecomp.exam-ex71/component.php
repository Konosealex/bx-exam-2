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

$newsCount = intval($arParams["NEWS_COUNT"]);
if ($newsCount <= 0) {
    $newsCount = 20;
}

$arNavParams = [
    "nPageSize"          => $newsCount,
    "bDescPageNumbering" => false,
    "bShowAll"           => false,
];
$arNavigation = CDBResult::GetNavParams($arNavParams);

$arUserGroups = $USER->GetUserGroupArray();

if ($this->StartResultCache(false, [$arUserGroups, $bFilter, $arNavigation])) {
    $arResult["CLASSIFIER"] = [];
    $arPrice = [];

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

    $obProduct = CIBlockElement::GetList($arOrder, $arFilter, false, $arNavParams, $arSelect);
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
        $arPrice[] = $arResult["ELEMENTS"][$arProductList["ID"]]['PROPERTY']['PRICE']['VALUE'];
    }
    $arResult['MIN_PRICE'] = min($arPrice);
    $arResult['MAX_PRICE'] = max($arPrice);

    $arResult["COUNT_CLASSIFIER"] = count($arResult["CLASSIFIER"]);

    $filterUrl = $APPLICATION->GetCurPage() . '?F=Y';
    $arResult['FILTER_LINK'] = "<a href='{$filterUrl}'>{$filterUrl}</a>";
    $arResult["NAV_STRING"] = $obProduct->GetPageNavString(GetMessage('SIMPLECOMP_EXAM2_DESC_LIST'));

    $this->SetResultCacheKeys(["COUNT_CLASSIFIER", 'MIN_PRICE', 'MAX_PRICE']);

    $this->includeComponentTemplate();
}

if ($USER->IsAuthorized()) {
    $arButtons = CIBlock::GetPanelButtons($productionIblockId);
    $editAdminLink = $arButtons["submenu"]["element_list"]["ACTION_URL"];

    $this->AddIncludeAreaIcon(
        [
            "URL"            => $editAdminLink,
            "TITLE"          => GetMessage('MAIN_MENU_ADD_NEW'),
            "IN_PARAMS_MENU" => true
        ]
    );
}

$APPLICATION->SetTitle(GetMessage('H1') . $arResult['COUNT_CLASSIFIER']);

$APPLICATION->AddViewContent('min_price', "Минимальная цена:" . $arResult['MIN_PRICE']);
$APPLICATION->AddViewContent('max_price', "Максимальная цена:" . $arResult['MAX_PRICE']);

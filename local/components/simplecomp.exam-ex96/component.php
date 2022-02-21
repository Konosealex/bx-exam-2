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

if (!$USER->IsAuthorized()) {
    ShowError(GetMessage("SIMPLECOMP_EXAM2_AUTH_NONE"));
    return;
}

$productionIblockId = (int)$arParams['PRODUCTS_IBLOCK_ID'];
$propertyCode = (string)$arParams['PROPERTY_CODE'];

if (!$productionIblockId || !$propertyCode) {
    ShowError(GetMessage("SIMPLECOMP_EXAM2_PARAMS_NONE"));
    return;
}

$currentUserId = $USER->GetID();

if ($this->StartResultCache(false, [$currentUserId])) {
    $by = "id";
    $order = "asc";
    $arFilter = [
        "ACTIVE" => "Y"
    ];

    $arResult["USERS"] = [];
    $obUsers = CUser::GetList($by, $order, $arFilter);
    while ($arUser = $obUsers->GetNext()) {
        $arResult["USERS"][$arUser['ID']] = $arUser['LOGIN'];
    }

    $usersProperyCode = 'PROPERTY_' . $propertyCode;

    $arFilter = [
        "IBLOCK_ID"             => $productionIblockId,
        "ACTIVE"                => "Y",
        '!' . $usersProperyCode => false
    ];

    $arSelect = [
        "ID",
        "NAME",
        'PROPERTY_PRICE',
        'PROPERTY_MATERIAL',
        'PROPERTY_ARTNUMBER',
        $usersProperyCode
    ];

    $arResult["ELEMENTS"] = [];
    $arResult["ELEMENTS_ID"] = [];
    $obElements = CIBlockElement::GetList([], $arFilter, false, false, $arSelect);
    while ($arElement = $obElements->GetNext()) {
        if ($arElement[$usersProperyCode . '_VALUE'] == $currentUserId) {
            $arResult["CURRENT_USER_FAVORITE_ID"][] = $arElement["ID"];
        } else {
            $arResult["FAVORITE_ID"][$arElement["ID"]][$arElement[$usersProperyCode . "_VALUE"]] = $arResult['USERS'][$arElement[$usersProperyCode . "_VALUE"]];
            $arResult["FAVORITE_ID_2"][$arElement[$usersProperyCode . "_VALUE"]][] = $arElement["ID"];
            $arResult["FAVORITE_ID_OTHER"][] = $arElement["ID"];
        }
        $arResult["ELEMENTS"][$arElement["ID"]] = $arElement;
        $arResult["ELEMENTS_ID"][] = $arElement["ID"];
    }

    $arResult["CURRENT_USER_FAVORITE_ID"] = array_unique($arResult["CURRENT_USER_FAVORITE_ID"]);
    $arResult["FAVORITE_ID_OTHER"] = array_unique($arResult["FAVORITE_ID_OTHER"]);

    foreach ($arResult["CURRENT_USER_FAVORITE_ID"] as $value) {
        if (in_array($value, $arResult["FAVORITE_ID_OTHER"])) {
            $arResult["FAVORITE_ID_OTHER_2"][] = $value;
        }
    }

    foreach ($arResult["FAVORITE_ID_OTHER_2"] as $value) {
        $arResult["FAVORITE_ID_OTHER_3"] = array_keys($arResult["FAVORITE_ID"][$value]);
    }

    foreach ($arResult["FAVORITE_ID_OTHER_3"] as $value) {
        $arResult["FAVORITE_ID_OTHER_4"] = $arResult["FAVORITE_ID_2"][$value];
    }

    $arResult["FAVORITE_ID_OTHER_4"] = array_unique($arResult["FAVORITE_ID_OTHER_4"]);
    $arResult["FAVORITE_COUNT"] = count($arResult["CURRENT_USER_FAVORITE_ID"]);

    $this->SetResultCacheKeys(['FAVORITE_COUNT']);

    $this->includeComponentTemplate();
}

$APPLICATION->SetTitle(GetMessage('H1') . $arResult["FAVORITE_COUNT"]);
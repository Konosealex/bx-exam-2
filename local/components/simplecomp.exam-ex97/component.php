<?php

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) {
    die();
}

use Bitrix\Main\Loader,
    Bitrix\Iblock;

/** @var array $arParams */
/** @var array $arResult */
/** @global CUser $USER */
/** @global CMain $APPLICATION */

if (!Loader::includeModule("iblock")) {
    ShowError(GetMessage("SIMPLECOMP_EXAM2_IBLOCK_MODULE_NONE"));
    return;
}

$newsIblockId = (int)$arParams['NEWS_IBLOCK_ID'];
$authorProperty = (string)$arParams['PROPERTY_AUTHOR'];
$authorTypeProperty = (string)$arParams['PROPERTY_AUTHOR_TYPE'];

if (!$newsIblockId || !$authorProperty || !$authorTypeProperty) {
    ShowError(GetMessage("SIMPLECOMP_EXAM2_IBLOCK_PARAMS_NONE"));
    return;
}

if (!$USER->IsAuthorized()) {
    ShowError(GetMessage("SIMPLECOMP_EXAM2_AUTH_NONE"));
    return;
}

$currentUserId = $USER->GetID();

if ($this->StartResultCache(false, [$currentUserId])) {
    $arUser = CUser::GetByID($currentUserId)->GetNext();
    $currentUserAuthorType = $arUser[$authorProperty];
    $arResult["USERS"] = [];

    $arOrderUser = ["id"];
    $sortOrder = "asc";
    $arFilter = [
        "ACTIVE"        => "Y",
        '!ID'           => $currentUserId,
        $authorProperty => $currentUserAuthorType
    ];
    $arSelect = [
        'FIELDS' => ['ID', 'LOGIN']
    ];

    $obUsers = CUser::GetList($arOrderUser, $sortOrder, $arFilter, $arSelect); // выбираем пользователей
    while ($arUser = $obUsers->GetNext()) {
        $arResult["USERS"][$arUser['ID']] = [
            'LOGIN' => $arUser['LOGIN']
        ];
    }

    if (!$arResult["USERS"]) {
        $this->AbortResultCache();
        ShowError(GetMessage("SIMPLECOMP_EXAM2_USERS_NONE"));
        return;
    }

    $arAuthorsId = array_keys($arResult["USERS"]);
    $authorTypeProperty = 'PROPERTY_' . $authorTypeProperty;

    $newsList = [];

    $arFilter = [
        "IBLOCK_ID"         => $newsIblockId,
        $authorTypeProperty => $arAuthorsId,
    ];
    $arSelect = [
        "ID",
        "NAME",
        "ACTIVE_FROM",
        $authorTypeProperty
    ];
    $obElement = CIBlockElement::GetList([], $arFilter, false, false, $arSelect);
    while ($arElement = $obElement->GetNext()) {
        if (!$newsList[$arElement["ID"]]) {
            $newsList[$arElement["ID"]] = $arElement;
        }
        $newsList[$arElement["ID"]]["AUTHORS"][] = $arElement[$authorTypeProperty . '_VALUE'];
    }

    foreach ($newsList as $key => $news) {
        foreach ($news["AUTHORS"] as $authorId) {
            $arResult["USERS"][$authorId]["NEWS"][] = $news;
        }
    }

    $arResult["COUNT"] = count($newsList);
    $this->SetResultCacheKeys(["COUNT"]);

    $this->includeComponentTemplate();
}

$APPLICATION->SetTitle(GetMessage('H1') . $arResult["COUNT"]);


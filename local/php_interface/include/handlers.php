<?php

$eventManager = \Bitrix\Main\EventManager::getInstance();
$eventManager->addEventHandler("iblock", "OnBeforeIBlockElementUpdate", "OnBeforeIBlockElementUpdate");
$eventManager->addEventHandler("main", "OnEpilog", "OnEpilog");
$eventManager->addEventHandler("main", "OnBeforeEventAdd", "OnBeforeEventAdd");
$eventManager->addEventHandler("main", "OnBuildGlobalMenu", "OnBuildGlobalMenu");
$eventManager->addEventHandler("main", "OnPageStart", "OnPageStart");

function OnBeforeIBlockElementUpdate(&$arFields)
{
    if ($arFields['IBLOCK_ID'] == PRODUCTION_IBLOCK_ID && $arFields['ACTIVE'] == 'N') {
        $arFilter = [
            "IBLOCK_ID"     => PRODUCTION_IBLOCK_ID,
            "ACTIVE"        => "N",
            'ID'            => $arFields['ID'],
            '>SHOW_COUNTER' => 2
        ];
        $arSelect = [
            "ID",
            "SHOW_COUNTER",
        ];
        $obElement = CIBlockElement::GetList([], $arFilter, false, false, $arSelect);
        $arElement = $obElement->GetNext();
        if ($arElement) {
            $count = ['#COUNT#' => $arElement['SHOW_COUNTER']];
            global $APPLICATION;
            $APPLICATION->ThrowException(GetMessage('ERROR', $count));
            return false;
        }
    }
}

function OnEpilog()
{
    if (ERROR_404 && ERROR_404 == 'Y') {
        global $APPLICATION;
        $currentPage = $APPLICATION->GetCurUri();

        CEventLog::Add(
            [
                "SEVERITY"      => "INFO",
                "AUDIT_TYPE_ID" => "ERROR_404",
                "MODULE_ID"     => "main",
                "DESCRIPTION"   => $currentPage
            ]
        );
    }
}

function OnBeforeEventAdd(&$event, &$lid, &$arFields)
{
    if ($event == EVENT_NAME) {
        wtf($arFields);
        global $USER;
        if ($USER->IsAuthorized()) {
            $currentUserId = $USER->GetID();
            $currentUserLogin = $USER->GetLogin();
            $currentUserName = $USER->GetFirstName();
            $message = "Пользователь авторизован: {$currentUserId} ({$currentUserLogin}) {$currentUserName}, данные из формы: {$arFields['NAME']}";
        } else {
            $message = "Пользователь не авторизован, данные из формы: {$arFields['NAME']}";
        }

        $description = ['#AUTHOR#' => $message];

        CEventLog::Add(
            [
                "SEVERITY"      => "INFO",
                "AUDIT_TYPE_ID" => "FEEDBACK_FORM",
                "MODULE_ID"     => "main",
                "DESCRIPTION"   => GetMessage('FEEDBACK_FORM', $description),
            ]
        );
    }
}

function OnBuildGlobalMenu(&$aGlobalMenu, &$aModuleMenu)
{
    global $USER;
    if (in_array(CONTENT_MANAGER_GROUP_ID, $USER->GetUserGroupArray())) {
        foreach ($aGlobalMenu as $key => $globalMenu) {
            if ($key != 'global_menu_content') {
                unset($aGlobalMenu[$key]);
            }
        }

        foreach ($aModuleMenu as $key => $moduleMenu) {
            if ($moduleMenu['items_id'] != 'menu_iblock_/news') {
                unset($aModuleMenu[$key]);
            }
        }
    }
}

function OnPageStart()
{
    global $APPLICATION;
    $currentDir = $APPLICATION->GetCurDir();
    $currentPage = $APPLICATION->GetCurPage();
    if (($currentDir == '/bitrix/admin') || (!CModule::IncludeModule("iblock"))) {
        return false;
    }

    $arFilter = [
        "NAME"   => $currentPage,
        "ACTIVE" => "Y"
    ];
    $arSelect = [
        "ID",
        "NAME",
        "PROPERTY_TITLE",
        "PROPERTY_DECRIPTION"
    ];
    $obElement = CIBlockElement::GetList([], $arFilter, false, false, $arSelect);
    $arElement = $obElement->GetNext();
    if ($arElement) {
        $APPLICATION->SetPageProperty('title', $arElement['PROPERTY_TITLE_VALUE']);
        $APPLICATION->SetPageProperty('description', $arElement['PROPERTY_DECRIPTION_VALUE']);
    }
}
<?php

$eventManager = \Bitrix\Main\EventManager::getInstance();
$eventManager->addEventHandler("iblock", "OnBeforeIBlockElementUpdate", "OnBeforeIBlockElementUpdate");
$eventManager->addEventHandler("main", "OnEpilog", "OnEpilog");

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
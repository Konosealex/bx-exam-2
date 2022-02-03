<?php

$eventManager = \Bitrix\Main\EventManager::getInstance();
$eventManager->addEventHandler("iblock", "OnBeforeIBlockElementUpdate", "OnBeforeIBlockElementUpdate");

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
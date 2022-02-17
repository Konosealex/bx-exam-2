<?php if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) {
    die();
}

/** @var array $arParams */
/** @var array $arResult */
/** @global CMain $APPLICATION */

if ($arParams['CANONICAL_IBLOCK_ID']) {
    $APPLICATION->SetPageProperty('canonical', $arResult['CANONICAL_VALUE']);
}

global $USER;

$obContext = \Bitrix\Main\Application::getInstance()->getContext();
$obResponse = $obContext->getResponse();
$obRequest = $obContext->getRequest();
$isComplaint = $obRequest->get('complaint') == 'Y';
$isAjax = $obRequest->isAjaxRequest();

$result = '';
if ($isComplaint) {
    $newsId = (int)$arResult['ID'];
    $activeFrom = date('d.m.Y H:i:s', time());
    $name = "{$activeFrom} {$newsId}";

    if ($USER->isAuthorized()) {
        $userId = $USER->GetID();
        $userLogin = $USER->GetLogin();
        $userFullName = $USER->GetFullName();
        $propertysUser = $userId . ', ' . $userLogin . ', ' . $userFullName;
    } else {
        $propertysUser = GetMessage('USER_NOT_AUTH');
    }

    $el = new CIBlockElement();
    $newComplaintId = $el->Add(
        [
            'IBLOCK_ID'       => IB_COMPLAINT,
            'NAME'            => $name,
            'ACTIVE_FROM'     => $activeFrom,
            'PROPERTY_VALUES' => [
                'USER' => $propertysUser,
                'NEWS' => $newsId
            ]
        ]
    );

    $result = $newComplaintId
        ? GetMessage('COMPLAINT_ADDED', ['#ID#' => $newComplaintId])
        : GetMessage('COMPLAINT_ADD_ERROR');

    if ($isAjax) {
        $APPLICATION->RestartBuffer();
        $obResponse->flush($result);
        die;
    }
}

echo "<div id='complaint-result' class='complaint-result'>{$result}</div>";

1. Создаем нужный раздел, страницу
2. Создаем в папке `local` папку `components`, копируем туда из демоданных папку `simplecomp.exam-materials`
3. Создаем описание компонента для визуального редактора

```php
<?php

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) {
    die();
}

$arComponentDescription = [
    "NAME" => GetMessage("SIMPLECOMP_EXAM2_NAME"),
     //Название компонента в визуальном редакторе
    "PATH" => [
        "ID" => "ex2simple",
         //Раздел отображения компонента в визуальном редакторе
        "NAME" => GetMessage("SIMPLECOMP_EXAM2_SECTION_NAME"),
    ],
];
```

4. Создаем параметры компонента, можем подглядывать в стандартный компонент БУСа, например `news.list`

```php
<?php

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) {
    die();
}
$arComponentParameters = [
    "PARAMETERS" => [
        "NEWS_IBLOCK_ID"  => [
            "NAME" => GetMessage("SIMPLECOMP_EXAM2_NEWS_IBLOCK_ID"),
            "TYPE" => "STRING",
        ],
        "AUTHOR_PROPERTY" => [
            "NAME" => GetMessage("SIMPLECOMP_EXAM2_AUTHOR_PROPERTY"),
            "TYPE" => "STRING",
        ],
        "AUTHOR_TYPE"     => [
            "NAME" => GetMessage("SIMPLECOMP_EXAM2_AUTHOR_TYPE"),
            "TYPE" => "STRING",
        ],
        "CACHE_TIME"      => ["DEFAULT" => 36000000],
    ]
];
```

5. Не забываем создавать ланговые файлы для параметров, описания и самого компонента
6. Создаем логику компонента

```php
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
```

7. Создаем шаблон компонента и не забываем про ланговые файлы

```php
<?php if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) {
    die();
}
/** @var array $arResult */
?>

<p><b><?= GetMessage("SIMPLECOMP_EXAM2_NEWS_TITLE") ?></b></p>
<ul>
    <?php foreach ($arResult['USERS'] as $authorId => $author): ?>
        <li>
            [<?= $authorId ?>] - <?= $author['LOGIN'] ?>
            <ul>
                <?php foreach ($author['NEWS'] as $news): ?>
                    <li>
                        <?= $news['ACTIVE_FROM'] ?> - <?= $news['NAME'] ?>
                    </li>
                <?php endforeach; ?>
            </ul>
        </li>
    <?php endforeach; ?>
</ul>
```
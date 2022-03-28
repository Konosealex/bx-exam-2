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
        "PRODUCTS_IBLOCK_ID" => [
            "NAME" => GetMessage("SIMPLECOMP_EXAM2_CAT_IBLOCK_ID"),
            "TYPE" => "STRING",
        ],
        "COMPANY_IBLOCK_ID"  => [
            "NAME" => GetMessage("SIMPLECOMP_EXAM2_COMPANY_IBLOCK_ID"),
            "TYPE" => "STRING",
        ],
        "LINK_TEMPLATE"      => [
            "NAME" => GetMessage("SIMPLECOMP_EXAM2_LINK_TEMPLATE"),
            "TYPE" => "STRING",
        ],
        "PROPERTY_CODE"      => [
            "NAME" => GetMessage("SIMPLECOMP_EXAM2_PROPERTY_CODE"),
            "TYPE" => "STRING",
        ],
        "CACHE_TIME"         => ["DEFAULT" => 36000000]
    ],
];
```

5. Не забываем создавать ланговые файлы для параметров, описания и самого компонента
6. Создаем логику компонента

```php
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

if (!$productionIblockId || !$classificatorIblockId || !$propertyCode) {
    ShowError(GetMessage("SIMPLECOMP_EXAM2_IBLOCK_PARAMS_NONE"));
    return;
}

$arUserGroups = $USER->GetUserGroupArray();

if ($this->StartResultCache(false, [$arUserGroups])) {
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

    $arFilter = [
        "IBLOCK_ID"                 => $productionIblockId,
        "PROPERTY_" . $propertyCode => $arResult["CLASSIFIER_ID"],
        "CHECK_PERMISSIONS"         => "Y",
        "ACTIVE"                    => "Y",
    ];
    $arSelect = [
        "ID",
        "IBLOCK_ID",
        "NAME",
        "PREVIEW_TEXT",
    ];

    $arResult["ELEMENTS"] = [];

    $obProduct = CIBlockElement::GetList([], $arFilter, false, false, $arSelect);
    while ($arProduct = $obProduct->GetNextElement()) {
        $arProductList = $arProduct->GetFields();
        $arProductList["PROPERTY"] = $arProduct->GetProperties();

        foreach ($arProductList["PROPERTY"]["COMPANY"]["VALUE"] as $product) {
            $arResult["CLASSIFIER"][$product]["ELEMENTS_ID"][] = $arProductList["ID"];
        }

        $arResult["ELEMENTS"][$arProductList["ID"]] = $arProductList;
    }

    $arResult["COUNT_CLASSIFIER"] = count($arResult["CLASSIFIER"]);

    $this->SetResultCacheKeys(["COUNT_CLASSIFIER"]);
}
$this->includeComponentTemplate();

$APPLICATION->SetTitle(GetMessage('H1') . $arResult['COUNT_CLASSIFIER']);
```

7. Создаем шаблон компонента и не забываем про ланговые файлы

```php
<?php if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) {
    die();
}
/** @var array $arResult */
?>
<p><b><?= GetMessage("SIMPLECOMP_EXAM2_CAT_TITLE") ?></b></p>
<ul>
    <?php foreach ($arResult["CLASSIFIER"] as $section): ?>
        <li><b><?= $section["NAME"] ?></b></li>
        <ul>
            <?php if (is_array($section["ELEMENTS_ID"]) && (count($section["ELEMENTS_ID"]) > 0)): ?>
                <?php foreach ($section["ELEMENTS_ID"] as $elem): ?>
                    <li><?= $arResult["ELEMENTS"][$elem]["NAME"] ?>
                        - <?= $arResult["ELEMENTS"][$elem]["PROPERTY"]["PRICE"]["VALUE"] ?>
                        - <?= $arResult["ELEMENTS"][$elem]["PROPERTY"]["MATERIAL"]["VALUE"] ?>
                    </li>
                <?php endforeach; ?>
            <?php endif; ?>
        </ul>
    <?php endforeach; ?>
</ul>
```
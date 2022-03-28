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
        "NEWS_IBLOCK_ID"     => [
            "NAME" => GetMessage("SIMPLECOMP_EXAM2_NEWS_IBLOCK_ID"),
            "TYPE" => "STRING",
        ],
        "PROPERTY_CODE"      => [
            "NAME" => GetMessage("SIMPLECOMP_EXAM2_PROPERTY_CODE"),
            "TYPE" => "STRING",
        ],
        //Создаем параметры id инофблока товаров, id инфоблока новостей,
        // название свойства привязки разделов каталога товаров к новостям 
        "CACHE_TIME"         => ["DEFAULT" => 36000000]
        //Устанавливаем кеширование
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

use Bitrix\Main\Loader,
    Bitrix\Iblock;
//До сюда все можно взять из стандартного компонента (например news.list),
//часть вызовов, например проверка подключения пролога
//и подключения классов идут в демоданных

/** @var array $arParams */
/** @var array $arResult */
/** @global CMain $APPLICATION */

if (!Loader::includeModule("iblock")) {
    ShowError(GetMessage("SIMPLECOMP_EXAM2_IBLOCK_MODULE_NONE"));
    return;
}
//Если не подключен модуль инфоблоков - выведем сообщение об ошибке

$productionIblockId = (int)$arParams['PRODUCTS_IBLOCK_ID'];
$newsIblockId = (int)$arParams['NEWS_IBLOCK_ID'];
$propertyCode = (string)$arParams['PROPERTY_CODE'];
//Создаем переменные на основе вводных данных параметров шаблона

if (!$productionIblockId || !$newsIblockId || !$propertyCode) {
    ShowError(GetMessage("SIMPLECOMP_EXAM2_IBLOCK_PARAMS_NONE"));
    return;
}
//Если не указан один из параметров компонента - выведем сообщение об ошибке

if ($this->StartResultCache()) {
//Начало кешируемой области компонента
    $newsIds = [];
    $newsList = [];
    $sectionIds = [];
    $sectionList = [];

    $arFilter = [
        "IBLOCK_ID" => $newsIblockId,
        "ACTIVE"    => "Y"
    ];
    $arSelect = [
        "ID",
        "NAME",
        "ACTIVE_FROM"
    ];

    $obNews = CIBlockElement::GetList([], $arFilter, false, false, $arSelect);
    while ($arNews = $obNews->GetNext()) {
        $newsIds[] = $arNews['ID'];
        $newsList[$arNews['ID']] = $arNews;
    }

    $arFilter = [
        "IBLOCK_ID"   => $productionIblockId,
        "ACTIVE"      => "Y",
        $propertyCode => $newsIds,
        'CNT_ACTIVE'
    ];
    $arSelect = [
        "ID",
        "IBLOCK_ID",
        "NAME",
        $propertyCode
    ];

    $obSections = CIBlockSection::GetList([], $arFilter, true, $arSelect);
    while ($arSection = $obSections->GetNext()) {
        $sectionIds[] = $arSection['ID'];
        $sectionList[$arSection['ID']] = $arSection;
    }

    $arFilter = [
        'IBLOCK_ID'  => $productionIblockId,
        'ACTIVE'     => 'Y',
        'SECTION_ID' => $sectionIds
    ];
    $arSelect = [
        "ID",
        "NAME",
        'IBLOCK_SECTION_ID',
        'IBLOCK_ID',
        'PROPERTY_ARTNUMBER',
        'PROPERTY_MATERIAL',
        'PROPERTY_PRICE'
    ];

    $obProduct = CIBlockElement::GetList([], $arFilter, false, false, $arSelect);
    while ($arProduct = $obProduct->GetNext()) {
        foreach ($sectionList[$arProduct['IBLOCK_SECTION_ID']]['UF_NEWS_LINK'] as $newsId) {
            $newsList[$newsId]['PRODUCTS'][] = $arProduct;
        }
    }

    $arResult['PRODUCT_CNT'] = 0;
    foreach ($sectionList as $section) {
        $arResult['PRODUCT_CNT'] += $section['ELEMENT_CNT'];
        foreach ($section['UF_NEWS_LINK'] as $newsId) {
            $newsList[$newsId]['SECTIONS'][] = $section['NAME'];
        }
    }

    $arResult['NEWS'] = $newsList;

    $this->SetResultCacheKeys(['PRODUCT_CNT']);
    //Кешируем только количество товаров

    $this->includeComponentTemplate();
}

$APPLICATION->SetPageProperty('title', GetMessage('SET_TITLE') . $arResult['PRODUCT_CNT']);
//Устанавливаем title страницы
$APPLICATION->SetTitle(GetMessage('H1') . $arResult['PRODUCT_CNT']);
//Устанавливаем заголовок страницы
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
    <?php foreach ($arResult['NEWS'] as $news): ?>
        <li>
            <b><?= $news['NAME'] ?></b> - <?= $news['ACTIVE_FROM'] ?> (<?= implode(', ', $news['SECTIONS']) ?>)
            <ul>
                <?php foreach ($news['PRODUCTS'] as $product): ?>
                    <li>
                        <?= $product['NAME'] ?> - <?= $product['PROPERTY_PRICE_VALUE'] ?>
                        - <?= $product['PROPERTY_MATERIAL_VALUE'] ?> - <?= $product['PROPERTY_ARTNUMBER_VALUE'] ?>
                    </li>
                <?php endforeach; ?>
            </ul>
        </li>
    <?php endforeach; ?>
</ul>

```
1. Идем в стандартный компонент БУСа `news.list` и из `.parameters.php` забираем параметр `'NEWS_COUNT'` и все языковые
   файлы к нему (можно и руками написать, так просто быстрее), в итоге файл `.parameters.php` в нашем компоненте будет
   иметь следующий вид

```php
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
        "LINK_TEMPLATE"      => [
            "NAME" => GetMessage("SIMPLECOMP_EXAM2_LINK_TEMPLATE"),
            "TYPE" => "STRING",
        ],
        "NEWS_COUNT"         => [
            "PARENT"  => "BASE",
            "NAME"    => GetMessage("SIMPLECOMP_EXAM2_DESC_LIST_CONT"),
            "TYPE"    => "STRING",
            "DEFAULT" => "20",
        ],
        "CACHE_TIME"         => ["DEFAULT" => 36000000]
    ],
];
```

2. Далее все в том же `news.list` уже в самом `component.php` берем строку проверки вводных данных для количества
   страниц и блок с объявлением массивов `$arNavParams` и `$arNavigation` в итоге в свой компонент записываем следующий
   код (вне кешируемой области)

```php
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
```

3. Добавим условие вывода кеша компоненту

```php
$this->StartResultCache(false, [$arNavigation])
```

4. `$arNavParams` передаедаем в `GetList` который выбираюет новости

```php
$obNews = CIBlockElement::GetList([], $arFilter, false, $arNavParams, $arSelect);
```

5. Передаем в `$arResult` собранную верстку пагинации

```php
$arResult["NAV_STRING"] = $obNews->GetPageNavString(GetMessage('SIMPLECOMP_EXAM2_DESC_LIST'));
```

6. Выводим пагинацию в шаблоне компонента

```php
<?= $arResult["NAV_STRING"] ?>
```
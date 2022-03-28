1. Идем в параметры нужного компонента `.parameters.php` и добавляем в массив `$arComponentParameters` строковый
   параметр для ввода шаблона ссылки детальной страницы

```php
 "LINK_TEMPLATE"      => [
            "NAME" => GetMessage("SIMPLECOMP_EXAM2_LINK_TEMPLATE"),
            "TYPE" => "STRING",
        ]
//Не забываем создать ланговый файл для описания параметра
```

2. В самом компоненте обрабатываем вводные параметры

```php
$linkTemplate = (string)$arParams['LINK_TEMPLATE'];
```

3. По условию задания необходимо поменять сортировку выводимых элементов, объявляем массив `$arOrder`

```php
 $arOrder = [
        "NAME" => "ASC",
        "SORT" => "ASC"
    ];
```

и передаем его в `CIBlockElement::GetList`

```php
$obProduct = CIBlockElement::GetList($arOrder, $arFilter, false, false, $arSelect);
```

4. Так же необхоимо к возвращаемым полям элемента добавить `DETAIL_PAGE_URL`

```php
 $arSelect = [
        'ID',
        'NAME',
        'IBLOCK_SECTION_ID',
        'PROPERTY_PRICE',
        'PROPERTY_MATERIAL',
        'PROPERTY_ARTNUMBER',
        'DETAIL_PAGE_URL'
    ];
```

5. Устанавливаем шаблоны путей до детальной страницы для элементов

```php
$obProduct->SetUrlTemplates($linkTemplate);
```

6. Передаем полученый шаблон детальной страницы в массив `$arAllProducts`

```php
'DETAIL_LINK' => $arProduct['DETAIL_PAGE_URL']
```

7. Выводим полученную сссылку в шаблоне компонента, нужно помнить что по умолчанию у элементов не прописаны символьне
   коды, поэтому даже в случае успешного решения будет выводится путь до раздела, чтобы этого избежать вводим нескольким
   элементам символьный код

```php
   (<?= $arProduct['DETAIL_LINK'] ?>)
```
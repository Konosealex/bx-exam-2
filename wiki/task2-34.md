1. В панели администрирования (`Управление структурой -> Настройки для сайтов -> Типы свойств`) создаем
   свойство `specialdate`
2. В header.php шаблона добавляем мета
   тег `<meta property= "specialdate" content="<?php $APPLICATION->ShowProperty('specialdate');?>">`
3. На главной странице сайта изменяем свойство раздела `specialdate`, установить значение 100.
4. В `"/local/templates/furniture_blue/components/bitrix/news/.default/"` создаем файл `.parameters.php`

- его содержимое:

```php
$arTemplateParameters['SET_SPECIALDATE'] = [
    "PARENT" => "ADDITIONAL_SETTINGS", //в какой группе свойств отобразить
    "NAME" => GetMessage('SET_SPECIALDATE'), //создать lang файл и добавить строку $MESS['SET_SPECIALDATE'] = 'Установить свойство страницы specialdate';
    "TYPE" => "CHECKBOX",
    "DEFAULT" => "Y"
]
```

5. В файл `"news.php"`, в параметры `"bitrix:news.list"` добавляем строку:

```php
"SET_SPECIALDATE" => $arParams["SET_SPECIALDATE"]
```

6. В `"/local/templates/furniture_blue/components/bitrix/news/.default/bitrix/news.list/.default"` создаем файлы

- `"result_modifier.php"`:

```php
if ($arParams['SET_SPECIALDATE'] == 'Y') {
    $firtsItem = reset($arResult['ITEMS']); 	//получаем первый элемент $arResult['ITEMS']
    $arResult['FIRST_ITEM_DATE'] = $firtsItem['ACTIVE_FROM']; //записываем нужное значение

    $this->GetComponent()->SetResultCacheKeys(['FIRST_ITEM_DATE']); //получаем доступ к методам компонента и добавляем в кэш новое значение, чтобы оно было доступно в "component_epilog.php"
}
 ```

- `"component_epilog.php"`:

```php
if ($arParams['SET_SPECIALDATE'] == 'Y') {
    $APPLICATION->SetPageProperty('specialdate', $arResult['FIRST_ITEM_DATE']); //устанавливаем новое значение метатега из кеша
}
 ```

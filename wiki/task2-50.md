1. Создаем в `"/local/php_interface/include"` файл `handlers.php`, не забываем его подключить в `init.php`
2. Создаем языковой файл `handlers.php` по адресу `"/local/php_interface/include/lang/ru/"`
3. В файле обработчиков событий `handlers.php` регистрируем событие `OnBeforeIBlockElementUpdate`

```php
$obEventManager = \Bitrix\Main\EventManager::getInstance();
$obEventManager->addEventHandler("iblock", "OnBeforeIBlockElementUpdate", "OnBeforeIBlockElementUpdate");
//делать класс не будем - поэтому последним параметром передаем только исполняемый метод,
//регестрируем обработчик события изменения элемента инфоблока
```

4. описываем логику обработчика

```php
function OnBeforeIBlockElementUpdate($arFields)
{
    if ($arFields['IBLOCK_ID'] == IBLOCK_PRODUCTION && $arFields['ACTIVE'] == 'N') {
        //обрабатываем только события изменений элементов инфоблока "Продукция" и только если производится попытка их деактивировать
        $arFilter = [
            "IBLOCK_ID"     => IBLOCK_PRODUCTION,
            "ACTIVE"        => "Y",
            "ID"            => $arFields['ID'],
            ">SHOW_COUNTER" => 2
        ];
        $arSelect = [
            "ID",
            "SHOW_COUNTER"
        ];
        $obElement = CIBlockElement::GetList([], $arFilter, false, false, $arSelect);
        $arElement = $obElement->GetNext();
        //получаем массив содержащий информацию о количестве просмотров и активности элемента

        if ($arElement) {
            //если количество просмотров элемента больше 2 и элемент активен
            //запрещаем запись изменений и показываем ошибку (пользуемся шаблонизацией языковых файлов)
            global $APPLICATION;
            $count = ['#COUNT#' => $arElement['SHOW_COUNTER']];
            $APPLICATION->throwException(
                Bitrix\Main\Localization\Loc::getMessage('ELEMENT_EDITING_NOT_AVAILABLE', $count)
            );
            return false;
        }
    }
}
```

5. В языковом файле не забываем использовать #COUNT# для корректной автозамены

```php
$MESS['ELEMENT_EDITING_NOT_AVAILABLE'] = 'Товар невозможно деактивировать - у него #COUNT# просмотров.';
```
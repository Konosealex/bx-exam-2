1. Создаем в `"/local/php_interface/include"` файл `handlers.php`, не забываем его подключить в `init.php`
2. Обьявляем константу `IBLOCK_METATEG` в `init.php`
```php
const IBLOCK_METATEG = 6;
```
3. В файле обработчиков событий `handlers.php` регистрируем событие `OnPageStart`

```php
$obEventManager = \Bitrix\Main\EventManager::getInstance();
$obEventManager->addEventHandler("main", "OnPageStart", "OnPageStart");
//делать класс не будем - поэтому последним параметром передаем только исполняемый метод,
//регестрируем обработчик события на начале выполняемой части пролога сайта
```

3. Описываем логику обработчика

```php
function OnPageStart()
{
    global $APPLICATION;
    $currentPage = $APPLICATION->GetCurPage();
    $currentDir = $APPLICATION->GetCurDir();
    //Получаем текущую страницу и текущий раздел

    if (($currentDir == '/bitrix/admin/') || (!\Bitrix\Main\Loader::includeModule('iblock'))) {
        return false;
    }
    //Если текущий раздел административный или модуль инфоблоков не подключен - прекращаем выполнение

    $arFilter = [
        'IBLOCK_ID' => IBLOCK_METATEG,
        'ACTIVE'    => 'Y',
        'NAME'      => $currentPage
    ];
    $arSelect = [
        'ID',
        'NAME',
        'PROPERTY_TITLE',
        'PROPERTY_DESCRIPTION'
    ];

    $arElement = CIBlockElement::GetList([], $arFilter, false, false, $arSelect)->GetNext();
    //В инфоблоке "Метатег" выбираем активные элементы с именем равным текущей странице
    //Если элемент найден - в выборку берем только его свойства

    if ($arElement) {
        $APPLICATION->SetPageProperty('title', $arElement['PROPERTY_TITLE_VALUE']);
        $APPLICATION->SetPageProperty('description', $arElement['PROPERTY_DESCRIPTION_VALUE']);
    }
     //Если элемент найден - устанавливаем свойства страницы из свойств элемента
}
```
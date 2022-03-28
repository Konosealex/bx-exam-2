1. Создаем в `"/local/php_interface/include"` файл `handlers.php`, не забываем его подключить в `init.php`
2. файле обработчиков событий `handlers.php` регистрируем событие `OnEpilog`

```php
$obEventManager = \Bitrix\Main\EventManager::getInstance();
$obEventManager->addEventHandler("main", "OnEpilog", "OnEpilog");
//делать класс не будем - поэтому последним параметром передаем только исполняемый метод,
//регестрируем обработчик события изменения элемента инфоблока
```

3. Описываем логику обработчика

```php
function OnEpilog()
{
    if (ERROR_404 && ERROR_404 == "Y") {
    //проверяем что оказались на несуществующей странице
        global $APPLICATION;
        $currentPage = $APPLICATION->GetCurUri();
        CEventLog::Add(
            [
                "SEVERITY"      => "INFO",
                "AUDIT_TYPE_ID" => "ERROR_404",
                "MODULE_ID"     => "main",
                "DESCRIPTION"   => $currentPage
            ]
        );
         //записываем событие в журнал
    }
}
```
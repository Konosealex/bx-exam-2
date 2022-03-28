1. Создаем нужный раздел, страницу, размещаем компонент формы обратной связи 
2. Создаем в `"/local/php_interface/include"` файл `handlers.php`, не забываем его подключить в `init.php`
3. файле обработчиков событий `handlers.php` регистрируем событие `onBeforeEventAdd`

```php
$obEventManager = \Bitrix\Main\EventManager::getInstance();
$obEventManager->addEventHandler("main", "onBeforeEventAdd", "onBeforeEventAdd");
//делать класс не будем - поэтому последним параметром передаем только исполняемый метод,
//регестрируем обработчик события изменения элемента инфоблока
```

3. Описываем логику обработчика

```php
function onBeforeEventAdd(&$event, &$lid, &$arFields)
{
    if ($event == 'FEEDBACK_FORM') {
     //обрабатываем только события FEEDBACK_FORM
        global $USER;
        if ($USER->isAuthorized()) {
            $arUser = CUser::GetById($USER->GetId())->GetNext();
            //если пользователь авторизован - получаем поля его профиля
            //формируем фразу для записи в журнал в зависимости от того авторизован пользователь или нет
            $arFields['AUTHOR'] = "Пользователь авторизован: {$arUser['ID']} ({$arUser['LOGIN']}) {$arUser['NAME']}, данные из формы: {$arFields['AUTHOR']}";
        } else {
            $arFields['AUTHOR'] = "Пользователь не авторизован, данные из формы: {$arFields['AUTHOR']}";
        }

        $description = ['#AUTHOR#' => $arFields['AUTHOR']];

        CEventLog::Add(
            [
                "SEVERITY"      => "INFO",
                "AUDIT_TYPE_ID" => "FEEDBACK_FORM",
                "MODULE_ID"     => "main",
                "DESCRIPTION"   => GetMessage('FEEDBACK_EVENT_DESCRIPTION', $description)
            ]
        );
         //записываем событие отправки формы в журнал
    }
}
```

4. В языковом файле не забываем использовать #AUTHOR# для корректной автозамены

```php
$MESS['FEEDBACK_EVENT_DESCRIPTION'] = 'Замена данных в отсылаемом письме – #AUTHOR#';
```
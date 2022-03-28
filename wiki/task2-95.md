1. Создаем в `"/local/php_interface/include"` файл `handlers.php`, не забываем его подключить в `init.php`
2. В файле обработчиков событий `handlers.php` регистрируем событие `OnBuildGlobalMenu`

```php
$obEventManager = \Bitrix\Main\EventManager::getInstance();
$obEventManager->addEventHandler("main", "OnBuildGlobalMenu", "OnBuildGlobalMenu");
//делать класс не будем - поэтому последним параметром передаем только исполняемый метод,
//регестрируем обработчик события построения меню в панели администрирования
```

3. Описываем логику обработчика

```php
function OnBuildGlobalMenu(&$aGlobalMenu, &$aModuleMenu)
{
    global $USER;
    if (in_array(CONTEN_GROUP_ID, $USER->GetUserGroupArray())) {
    //Проверяем содержится ли в массиве групп текущего юзера нужная нам группа (CONTEN_GROUP_ID)
        foreach ($aGlobalMenu as $key => $menu) {
            if ($key != 'global_menu_content') {
                unset($aGlobalMenu[$key]);
            }
        }
        //Удаляем все пункты меню кроме пункта "Управление контентом сайта"

        foreach ($aModuleMenu as $key => $menu) {
            if ($menu['items_id'] != 'menu_iblock_/news') {
                unset($aModuleMenu[$key]);
            }
        }
        //Удаляем все вложенные пункты меню кроме пункта "Новости"
    }
}
```
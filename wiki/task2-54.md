1. Создаем в панели администрирования (`Настройки->Настройки продукта->Почтовые и СМС события->Типы событий`) новое
   событие, `NEW_REGISTRATION`, в описании указываем следующие шаблоны

```
#EMAIL_TO# - Email получателя письма
#COUNT_USERS# - Количество пользователей
#COUNT_DAYS# - Количество дней
```

2. Создаем в панели администрирования шаблон письма согласно требованиям задания

3. Создаем в панели администрирования агента, в качестве функции агента укажем `checkUserCount();`, остальные параметры
   указываем согласно заданию

4. Создаем в `"/local/php_interface/include"` файл `agents.php`, не забываем его подключить в `init.php`

5. В файле `agents.php` объявляем константы согласно заданию, и метод `checkUserCount();` который должен возвращать свое
   название строкой, все манипуляции будем производить внутри этого метода

```php
<?php

const ADMIN_GRPUP_ID = 1;
const MAIL_EVENT = 'NEW_REGISTRATION';

function checkUserCount(): string
{
   //тут будет решение задания
   
   return 'checkUserCount();';
}
```

```php
function checkUserCount(): string
{
    $lastUserId = COption::GetOptionInt("main", "lastUserId", "0");
    //id последнего зарегестрированного пользователя будем хранить в опциях главного модуля
    $by = "id";
    $order = "desc";
    //Сортируем по убыванию id
    $obUsers = CUser::GetList($by, $order, ['>ID' => $lastUserId], ['FIELDS' => ['ID']]);
    $usersCount = $obUsers->SelectedRowsCount();
    //Запрашиваем количество пользователей у которых id больше чем последний записанный в опциях главного модуля
    if ($usersCount) {
        $arUser = $obUsers->GetNext();
        $newlastUserId = $arUser['ID'];
        //Если пользователь с большим id нашелся - получим его id
        $timeUserCheck = COption::GetOptionInt("main", "timeUserCheck", "0");
        //Получаем время последнего запуска проверки
        if ($timeUserCheck) {
            $days = round((time() - $timeUserCheck) / 86400);
            if (!$days) {
                $days = 1;
            }
        } else {
            $days = 1;
        }
        //Переводим время в дни

        $arFields = [
            'COUNT_USERS' => $usersCount,
            'COUNT_DAYS'  => $days
        ];
         //Переводаем в почтовое событие количество пользователей и время с последней проверки

        $by = "id";
        $order = "desc";
        $obUsers = CUser::GetList($by, $order, ['GROUPS_ID' => [ADMIN_GROUP_ID]], ['FIELDS' => ['ID', 'EMAIL']]);
        while ($arUser = $obUsers->GetNext()) {
            $arFields['EMAIL'] = $arUser['EMAIL'];
            //Выбираем e-mail админа, чтобы отправить письмо

            CEvent::SendImmediate
            (
                MAIL_EVENT,
                SITE_ID,
                $arFields,
                'N',
            );
            //Отправляем письмо
        }

        COption::SetOptionInt("main", "lastUserId", $newlastUserId);
        //Запишем id последнего юзера в главный модуль
    }
    COption::SetOptionInt("main", "timeUserCheck", time());
    //Записываем в главный модуль время последней проверки

    return 'checkUserCount();';
}
```
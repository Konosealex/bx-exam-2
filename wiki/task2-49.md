1. Идем в компонент и делаем проверку есть ли нужный гет параметр

```php
use Bitrix\Main\Application;

$request = Application::getInstance()->getContext()->getRequest();
$bFilter = false;
if ($request->get('F')) {
    $bFilter = true;
}
//сама проверка $request->get('F') в нашем случае аналог
//$_GET['F']) просто показывем знание D7
```

2. Добавляем условие наличия гет параметра `$bFilter` к условиям кеширования компонента

```php
if ($this->StartResultCache(false, [$USER->GetUserGroupArray(), $bFilter])) {
```

3. Если гет параметр `$bFilter` есть - дополняем `$arFilter` логикой из задания и удаляем кеш

```php
if ($bFilter) {
     $arFilter[] = [
         'LOGIC' => 'OR',
         [
             '<= PROPERTY_PRICE' => 1700,
             'PROPERTY_MATERIAL' => 'Дерево, ткань'
         ],
         [
             '< PROPERTY_PRICE'  => 1500,
             'PROPERTY_MATERIAL' => 'Металл, пластик'
         ],
     ];
     $this->AbortResultCache();
 }
```

4. Передадим в шаблон компонента ссылку на фильтр

```php
$filterUrl = $APPLICATION->GetCurPage() . '?F=Y';
$arResult['FILTER_LINK'] = '<a href=' . $filterUrl . '>' . $filterUrl . '</a>';
```

5. Выведем в шаблоне компонента ссылку на фильтр, не забываем про ланговые файлы

```php
<?= GetMessage("SIMPLECOMP_EXAM2_CAT_FILTER") . $arResult['FILTER_LINK'] ?>
```
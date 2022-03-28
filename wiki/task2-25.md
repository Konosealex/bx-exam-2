1. Создаем нужный раздел, страницу
2. Создаем в папке `local` папку `components`, копируем туда из демоданных папку `complexcomp.exam-materials`
3. В `.description.php` необходимо изменить название ID раздела в котором находится компонент, в нашем случае
   это `GetMessage("SIMPLECOMP_EXAM2_COMPONENT_SECTION_NAME")`
4. Идем в параметры компонента `.parameters.php`, раскоментируем объявление параметров перемененных `PARAM1` и `PARAM2`,
   а так же находим параметры для ЧПУ режима (`SEF_MODE`) и раскоментрируем шаблон ссылки для `exampage` и заполним
   данными из задания

```php
"exampage" => [
    "NAME"      => GetMessage("EXAM_PAGE"),
    "DEFAULT"   => "exam/new/#PARAM1#/?PARAM2=#PARAM2#",
    "VARIABLES" => ["PARAM1", "PARAM2"],
]
```

5. Идем в сам файл компонента `component.php` и в массив путей по умолчанию (`$arDefaultUrlTemplates404`) добавляем наш
   шаблон `exampage`

```php
$arDefaultUrlTemplates404 = [
    "sections_top" => "",
    "section"      => "#SECTION_ID#/",
    "detail"       => "#SECTION_ID#/#ELEMENT_ID#/",
    "exampage"     => "exam/new/#PARAM1#/?PARAM2=#PARAM2#"
];
```

Добавляем в список перемененных принимаемых компонентом (`$arComponentVariables`) наши переменные

```php
$arComponentVariables = [
    "SECTION_ID",
    "SECTION_CODE",
    "ELEMENT_ID",
    "ELEMENT_CODE",
    "PARAM1",
    "PARAM2",
];
```

Дополняем возможный код шаблона (`$componentPage`) нашим `exampage`

```php
 if (isset($arVariables["ELEMENT_ID"]) && intval($arVariables["ELEMENT_ID"]) > 0) {
        $componentPage = "detail";
    } elseif (isset($arVariables["ELEMENT_CODE"]) && strlen($arVariables["ELEMENT_CODE"]) > 0) {
        $componentPage = "detail";
    } elseif (isset($arVariables["SECTION_ID"]) && intval($arVariables["SECTION_ID"]) > 0) {
        $componentPage = "section";
    } elseif (isset($arVariables["SECTION_CODE"]) && strlen($arVariables["SECTION_CODE"]) > 0) {
        $componentPage = "section";
    } elseif (isset($arVariables["PARAM1"]) && strlen($arVariables["PARAM1"]) > 0) {
        $componentPage = "exampage";
    } else {
        $componentPage = "sections_top";
    }
```

Дополняем `$arResult["URL_TEMPLATES"]` нашим шаблоном пути

```php
$arResult = [
     "FOLDER"        => "",
     "URL_TEMPLATES" => [
         "section" => htmlspecialcharsbx($APPLICATION->GetCurPage())."?".$arVariableAliases["SECTION_ID"]."=#SECTION_ID#",
         "detail"  => htmlspecialcharsbx($APPLICATION->GetCurPage())."?".$arVariableAliases["SECTION_ID"]."=#SECTION_ID#"."&".$arVariableAliases["ELEMENT_ID"]."=#ELEMENT_ID#",
         "exampage" => htmlspecialcharsbx($APPLICATION->GetCurPage())."?".$arVariableAliases["PARAM1"]."=#PARAM1#"."&".$arVariableAliases["PARAM2"]."=#PARAM2#",
     ],
     "VARIABLES"     => $arVariables,
     "ALIASES"       => $arVariableAliases
 ];
```

6. Идем в шаблоны компонента, нас интересует `sections_top.php`, необходимо задать ссылку на страницу `exampage`

```php
$url = $arResult['FOLDER'] . $arResult['URL_TEMPLATES']['exampage'];
$url = str_replace('#PARAM1#', '123', $url);
$url = str_replace('#PARAM2#', '456', $url);
```

7. В шаблонах компонента открываем `exampage.php` и из `$arResult` выводим значения наших переменных

```php
<p>PARAM1 = <?= $arResult['VARIABLES']['PARAM1']?></p>
<p>PARAM2 = <?= $arResult['VARIABLES']['PARAM2']?></p>
```
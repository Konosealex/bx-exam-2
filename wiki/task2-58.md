1. Идем в стандартный компонент `news.list` и копируем следующий блок

```php
$arButtons = CIBlock::GetPanelButtons(
    $arItem["IBLOCK_ID"],
    $arItem["ID"],
    0,
    array("SECTION_BUTTONS"=>false, "SESSID"=>false)
);
$arItem["EDIT_LINK"] = $arButtons["edit"]["edit_element"]["ACTION_URL"];
$arItem["DELETE_LINK"] = $arButtons["edit"]["delete_element"]["ACTION_URL"];
```

2. В нашем компоненте вставляем данный код в цикл где у нас формируется массив элементов и заменяем передаваемые
   параметры `$arItem["IBLOCK_ID"]` и `$arItem["ID"]` на свои

```php
    $arButtons = CIBlock::GetPanelButtons(
            $productionIblockId,
            $arProductList['ID'],
            0,
            ["SECTION_BUTTONS" => false, "SESSID" => false]
        );
        $arItem["EDIT_LINK"] = $arButtons["edit"]["edit_element"]["ACTION_URL"];
        $arItem["DELETE_LINK"] = $arButtons["edit"]["delete_element"]["ACTION_URL"];
        $arResult["ITEMS"][$arProductList["ID"]][] = $arItem;
```

3. Идем в шаблон стандартного компонента `news.list` и копируем в свой комонент ланговый файл, а так же саму визуальную
   часть эрмитажа и формирование id для элементов списка

```php
 $this->AddEditAction(
    $arItem['ID'],
    $arItem['EDIT_LINK'],
    CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_EDIT")
);
$this->AddDeleteAction(
    $arItem['ID'],
    $arItem['DELETE_LINK'],
    CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_DELETE"),
    ["CONFIRM" => GetMessage('CT_BNL_ELEMENT_DELETE_CONFIRM')]
);

<p id="<?=$this->GetEditAreaId($arItem['ID']);?>">
```

4. Вставляем скопированный код вывода эрмитажа в цикле перебора продуктов в своем шаблоне компонента, и присваем
   элементам id

```php
 <?php foreach ($arResult["CLASSIFIER"] as $sectionKey => $section): ?>
 //сюда вставялем код эрмитажа скопированный в шаге 4 и находим тег элемента и присваем ему id
 //в случае этого задания он выглядел так:
 <li id="<?= $this->GetEditAreaId($ermitageId); ?>">
 <?php endforeach; ?>

```

5. Так как один и тот же продукт на странице у нас может выводиться многократно - необходимо `$arItem['ID']` сделать
   более уникальным, для этого объявляем новую переменную `$ermitageId`, и заменяем `$arItem['ID']` в эрмитаже
   на `$ermitageId`, а так же заменяем на `$arProduct['EDIT_LINK']` и `$arItem["IBLOCK_ID"]`

```php
$ermitageId = $sectionKey . '_' . $elem
//Где $sectionKey - id раздела, а $elem - id элемента
$this->AddEditAction(
   $ermitageId,
   $arResult["ITEMS"][$elem][0]["EDIT_LINK"],
   CIBlock::GetArrayByID($arResult["ELEMENTS"][$elem]["IBLOCK_ID"], "ELEMENT_EDIT")
);
$this->AddDeleteAction(
   $ermitageId,
   $arResult["ITEMS"][$elem][0]["EDIT_LINK"],
   CIBlock::GetArrayByID($arResult["ELEMENTS"][$elem]["IBLOCK_ID"], "ELEMENT_DELETE"),
   ["CONFIRM" => GetMessage("NEWS_DELETE_CONFIRM")]
);

<li id="<?= $this->GetEditAreaId($ermitageId); ?>">
```
1. Собираем все цены товаров в одном массиве

```php
$arPrice[] = $arResult["ELEMENTS"][$arProductList["ID"]]['PROPERTY']['PRICE']['VALUE'];
```

2. При помощи стандартных методов php получаем значение минимальной и максимальной цены в нашем массиве и передаем их
   в `$arResult`

```php
$arResult['MIN_PRICE'] = min($arPrice);
$arResult['MAX_PRICE'] = max($arPrice);
```

3. Кешируем значения полученные на предыдущем шаге

```php
$this->SetResultCacheKeys(["COUNT_CLASSIFIER", 'MIN_PRICE', 'MAX_PRICE']);
```

4. Вне кешируемой области создаем метку по которой будем выводить контент в шаблоне сайта

```php
$APPLICATION->AddViewContent('min_price', "Минимальная цена:" . $arResult['MIN_PRICE']);
$APPLICATION->AddViewContent('max_price', "Максимальная цена:" . $arResult['MAX_PRICE']);
```

5. В `header.php` выводим значения вычесленные в компоненте

```php
<div style="color:red; margin: 34px 15px 35px 15px">
   <div>
      <?php $APPLICATION->ShowViewContent('min_price'); ?>
   </div>
   <div>
      <?php $APPLICATION->ShowViewContent('max_price'); ?>
   </div>
</div>
```
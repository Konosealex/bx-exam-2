<?php if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) {
    die();
}
/** @var array $arResult */
?>
<p><b><?= GetMessage("SIMPLECOMP_EXAM2_CAT_TITLE") ?></b></p>
<ul>
    <?php foreach ($arResult['NEWS'] as $news): ?>
        <li>
            <b><?= $news['NAME'] ?></b> - <?= $news['ACTIVE_FROM'] ?> (<?= implode(', ', $news['SECTIONS']) ?>)
            <ul>
                <?php foreach ($news['PRODUCTS'] as $product): ?>
                    <li>
                        <?= $product['NAME'] ?> - <?= $product['PROPERTY_PRICE_VALUE'] ?>
                        - <?= $product['PROPERTY_MATERIAL_VALUE'] ?> - <?= $product['PROPERTY_ARTNUMBER_VALUE'] ?>
                    </li>
                <?php endforeach; ?>
            </ul>
        </li>
    <?php endforeach; ?>
</ul>

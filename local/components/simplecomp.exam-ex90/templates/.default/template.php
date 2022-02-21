<?php if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) {
    die();
}
/** @var array $arResult */
?>
---<p><b>Каталог:</b></p>
<ul>
    <?php foreach ($arResult["CLASSIFIER"] as $section): ?>
        <li><b><?= $section["NAME"] ?></b></li>
        <ul>
            <?php if (is_array($section["ELEMENTS_ID"]) && (count($section["ELEMENTS_ID"]) > 0)): ?>
                <?php foreach ($section["ELEMENTS_ID"] as $element): ?>
                    <li><?= $arResult["ELEMENTS"][$element]["NAME"] ?>
                        - <?= $arResult["ELEMENTS"][$element]["PROPERTY"]["PRICE"]["VALUE"] ?>
                        - <?= $arResult["ELEMENTS"][$element]["PROPERTY"]["MATERIAL"]["VALUE"] ?>
                        - <?= $arResult["ELEMENTS"][$element]["PROPERTY"]["ARTNUMBER"]["VALUE"] ?>
                    </li>
                <?php endforeach; ?>
            <?php endif; ?>
        </ul>
    <?php endforeach; ?>
</ul>

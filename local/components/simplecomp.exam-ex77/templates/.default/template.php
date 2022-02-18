<?php if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) {
    die();
}

/** @var array $arResult */

?>
---
<p><b><?= GetMessage("SIMPLECOMP_EXAM2_CAT_TITLE") ?></b></p>
<ul>
    <?php foreach ($arResult["ITEMS"] as $section): ?>
        <li><b><?= $section["NAME"] ?></b>
            <?php $arNameSections = [];
            foreach ($section["LINK_SECTIONS"] as $idSection)
                $arNameSections[] = $arResult["SECTIONS"][$idSection]["NAME"]
            ?>
            (<?= implode(", ", $arNameSections); ?>)
        </li>
        <ul>
            <?php foreach ($section["LINK_ELEMENTS"] as $idElement): ?>
            <li><?= $arResult["ELEMENTS"][$idElement]["NAME"] ?>
                - <?= $arResult["ELEMENTS"][$idElement]["PROPERTY_PRICE_VALUE"] ?>
                - <?= $arResult["ELEMENTS"][$idElement]["PROPERTY_MATERIAL_VALUE"] ?>
                - <?= $arResult["ELEMENTS"][$idElement]["PROPERTY_ARTNUMBER_VALUE"] ?>
                <?php endforeach; ?>
            </li>
        </ul>
    <?php endforeach; ?>
</ul>

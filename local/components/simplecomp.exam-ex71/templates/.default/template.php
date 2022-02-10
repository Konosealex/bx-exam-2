<?php if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) {
    die();
}
/** @var array $arResult */
?>
<p><b><?= GetMessage("SIMPLECOMP_EXAM2_FILTER") ?> <?= $arResult['FILTER_LINK'] ?></b></p>
<p><b><?= GetMessage("SIMPLECOMP_EXAM2_CAT_TITLE") ?></b></p>
<ul>
    <?php foreach ($arResult["CLASSIFIER"] as $section): ?>
        <li><b><?= $section["NAME"] ?></b></li>
        <ul>
            <?php if (is_array($section["ELEMENTS_ID"]) && (count($section["ELEMENTS_ID"]) > 0)): ?>
                <?php foreach ($section["ELEMENTS_ID"] as $elem): ?>
                    <li><?= $arResult["ELEMENTS"][$elem]["NAME"] ?>
                        - <?= $arResult["ELEMENTS"][$elem]["PROPERTY"]["PRICE"]["VALUE"] ?>
                        - <?= $arResult["ELEMENTS"][$elem]["PROPERTY"]["MATERIAL"]["VALUE"] ?>
                        - <a href="<?= $arResult["ELEMENTS"][$elem]["DETAIL_PAGE_URL"] ?>"><?= $arResult["ELEMENTS"][$elem]["DETAIL_PAGE_URL"] ?></a>
                    </li>
                <?php endforeach; ?>
            <?php endif; ?>
        </ul>
    <?php endforeach; ?>
</ul>
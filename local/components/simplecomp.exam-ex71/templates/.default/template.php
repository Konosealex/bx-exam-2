<?php if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) {
    die();
}
/** @var array $arResult */
?>
<p>Метка времени <?= time(); ?></p>
<p><b><?= GetMessage("SIMPLECOMP_EXAM2_FILTER") ?> <?= $arResult['FILTER_LINK'] ?></b></p>
<p><b><?= GetMessage("SIMPLECOMP_EXAM2_CAT_TITLE") ?></b></p>
<ul>
    <?php foreach ($arResult["CLASSIFIER"] as $sectionKey => $section): ?>
        <li><b><?= $section["NAME"] ?></b></li>
        <ul>
            <?php if (is_array($section["ELEMENTS_ID"]) && (count($section["ELEMENTS_ID"]) > 0)): ?>
                <?php foreach ($section["ELEMENTS_ID"] as $elem): ?>
                    <?php $ermitageId = $sectionKey . '_' . $elem ?>
                    <?php
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
                    ?>
                    <li id="<?= $this->GetEditAreaId($ermitageId); ?>"><?= $arResult["ELEMENTS"][$elem]["NAME"] ?>
                        - <?= $arResult["ELEMENTS"][$elem]["PROPERTY"]["PRICE"]["VALUE"] ?>
                        - <?= $arResult["ELEMENTS"][$elem]["PROPERTY"]["MATERIAL"]["VALUE"] ?>
                        -
                        <a href="<?= $arResult["ELEMENTS"][$elem]["DETAIL_PAGE_URL"] ?>"><?= $arResult["ELEMENTS"][$elem]["DETAIL_PAGE_URL"] ?></a>
                    </li>
                <?php endforeach; ?>
            <?php endif; ?>
        </ul>
    <?php endforeach; ?>
</ul>
<?= $arResult["NAV_STRING"] ?>
<?php if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) {
    die();
}

/**
 * @var array $arResult
 */

?>
    <p><b><?= GetMessage("FAVORITE_ELEMENTS") ?></b></p>
<?php foreach ($arResult["CURRENT_USER_FAVORITE_ID"] as $currentLike): ?>
    <ul>
        <li>
            <?= $arResult["ELEMENTS"][$currentLike]["NAME"] ?>
            <a href="<?= $arResult["ELEMENTS"][$currentLike]["DETAIL_PAGE_URL"] ?>"></a>
            - <?= $arResult["ELEMENTS"][$currentLike]["PROPERTY_PRICE_VALUE"] ?>
            - <?= $arResult["ELEMENTS"][$currentLike]["PROPERTY_MATERIAL_VALUE"] ?>
            - <?= $arResult["ELEMENTS"][$currentLike]["PROPERTY_ARTNUMBER_VALUE"] ?>
        </li>
    </ul>
<?php endforeach; ?>
    <p><b><?= GetMessage("FAVORITE_YOURS") ?></b></p>
<?php foreach ($arResult["FAVORITE_ID_OTHER_4"] as $elementId): ?>
    <?php if (!in_array($elementId, $arResult["FAVORITE_ID_OTHER_2"])): ?>
        <ul>
            <li>
                <?= $arResult["ELEMENTS"][$elementId]["NAME"] ?>
                - <?= $arResult["ELEMENTS"][$elementId]["PROPERTY_PRICE_VALUE"] ?>
                - <?= $arResult["ELEMENTS"][$elementId]["PROPERTY_MATERIAL_VALUE"] ?>
                - <?= $arResult["ELEMENTS"][$elementId]["PROPERTY_ARTNUMBER_VALUE"] ?>
                <br>
                <?= GetMessage("FAVORITE_USERS") ?><?= implode(", ", $arResult["FAVORITE_ID"][$elementId]); ?>
            </li>
        </ul>
    <?php endif; ?>
<?php endforeach; ?>
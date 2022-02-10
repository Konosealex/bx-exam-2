<?php if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) {
    die();
}
/** @var array $arResult */
?>

<p><b><?= GetMessage("SIMPLECOMP_EXAM2_NEWS_TITLE") ?></b></p>
<ul>
    <?php foreach ($arResult['USERS'] as $authorId => $author): ?>
        <li>
            [<?= $authorId ?>] - <?= $author['LOGIN'] ?>
            <ul>
                <?php foreach ($author['NEWS'] as $news): ?>
                    <li>
                        <?= $news['ACTIVE_FROM'] ?> - <?= $news['NAME'] ?>
                    </li>
                <?php endforeach; ?>
            </ul>
        </li>
    <?php endforeach; ?>
</ul>


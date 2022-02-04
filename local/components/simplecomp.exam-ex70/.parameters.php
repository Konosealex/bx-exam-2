<?php

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) {
    die();
}
$arComponentParameters = [
    "PARAMETERS" => [
        "PRODUCTS_IBLOCK_ID" => [
            "NAME" => GetMessage("SIMPLECOMP_EXAM2_CAT_IBLOCK_ID"),
            "TYPE" => "STRING",
        ],
        "NEWS_IBLOCK_ID"     => [
            "NAME" => GetMessage("SIMPLECOMP_EXAM2_NEWS_IBLOCK_ID"),
            "TYPE" => "STRING",
        ],
        "PROPERTY_CODE"      => [
            "NAME" => GetMessage("SIMPLECOMP_EXAM2_PROPERTY_CODE"),
            "TYPE" => "STRING",
        ],
        "CACHE_TIME"         => ["DEFAULT" => 36000000],
    ],
];
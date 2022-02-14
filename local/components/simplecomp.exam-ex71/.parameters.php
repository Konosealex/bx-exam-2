<?php

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) {
    die();
}
$arComponentParameters = [
    "PARAMETERS" => [
        "PRODUCTS_IBLOCK_ID"      => [
            "NAME" => GetMessage("SIMPLECOMP_EXAM2_CAT_IBLOCK_ID"),
            "TYPE" => "STRING",
        ],
        "CLASSIFICATOR_IBLOCK_ID" => [
            "NAME" => GetMessage("SIMPLECOMP_EXAM2_CLASSIFICATOR_IBLOCK_ID"),
            "TYPE" => "STRING",
        ],
        "DETAIL_LINK"             => [
            "NAME" => GetMessage("SIMPLECOMP_EXAM2_DETAIL_LINK"),
            "TYPE" => "STRING",
        ],
        "PROPERTY_CODE"           => [
            "NAME" => GetMessage("SIMPLECOMP_EXAM2_PROPERTY_CODE"),
            "TYPE" => "STRING",
        ],
        "NEWS_COUNT"              => [
            "NAME"    => GetMessage("T_IBLOCK_DESC_LIST_CONT"),
            "TYPE"    => "STRING",
            "DEFAULT" => "20",
        ],
        "CACHE_TIME"              => ["DEFAULT" => 36000000],
    ],
];
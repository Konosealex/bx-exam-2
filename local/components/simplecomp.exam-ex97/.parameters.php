<?php

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) {
    die();
}
$arComponentParameters = [
    "PARAMETERS" => [
        "NEWS_IBLOCK_ID"   => [
            "NAME" => GetMessage("SIMPLECOMP_EXAM2_NEWS_IBLOCK_ID"),
            "TYPE" => "STRING",
        ],
        "PROPERTY_AUTHOR"      => [
            "NAME" => GetMessage("SIMPLECOMP_EXAM2_PROPERTY_AUTHOR"),
            "TYPE" => "STRING",
        ],
        "PROPERTY_AUTHOR_TYPE" => [
            "NAME" => GetMessage("SIMPLECOMP_EXAM2_PROPERTY_AUTHOR_TYPE"),
            "TYPE" => "STRING",
        ],
        "CACHE_TIME"           => ["DEFAULT" => 36000000],
    ],
];
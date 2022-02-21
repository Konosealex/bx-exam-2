<?php

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) {
    die();
}
$arComponentParameters = [
    "PARAMETERS" => [
        "PRODUCTS_IBLOCK_ID"          => [
            "NAME" => GetMessage("SIMPLECOMP_EXAM2_CAT_IBLOCK_ID"),
            "TYPE" => "STRING",
        ],
        "SERVICES_IBLOCK_ID"          => [
            "NAME" => GetMessage("SIMPLECOMP_EXAM2_SERVICES_IBLOCK_ID"),
            "TYPE" => "STRING",
        ],
        "CLASSIFICATOR_PROPERTY_CODE" => [
            "NAME" => GetMessage("SIMPLECOMP_EXAM2_CLASSIFICATOR_PROPERTY_CODE"),
            "TYPE" => "STRING",
        ],
        "CACHE_TIME"                  => ["DEFAULT" => 36000000],
    ],
];
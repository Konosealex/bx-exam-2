<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Простой компонент ex77");
?><?$APPLICATION->IncludeComponent(
	"simplecomp.exam-ex77",
	"",
	Array(
		"CACHE_TIME" => "36000000",
		"CACHE_TYPE" => "A",
		"CLASSIFICATOR_IBLOCK_ID" => "9",
		"PRODUCTS_IBLOCK_ID" => "2",
		"PROPERTY_CODE" => "UF_NEW_CLASSIFIER"
	)
);?><?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>
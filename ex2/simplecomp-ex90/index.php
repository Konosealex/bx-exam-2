<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Простой компонент ex90");
?><?$APPLICATION->IncludeComponent(
	"simplecomp.exam-ex90", 
	".default", 
	array(
		"CACHE_TIME" => "36000000",
		"CACHE_TYPE" => "A",
		"CLASSIFICATOR_PROPERTY_CODE" => "CLASSIFICATOR",
		"PRODUCTS_IBLOCK_ID" => "2",
		"SERVICES_IBLOCK_ID" => "3",
		"COMPONENT_TEMPLATE" => ".default",
		"NEWS_COUNT" => "2"
	),
	false
);?><?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>
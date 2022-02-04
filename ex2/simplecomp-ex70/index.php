<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Простой компонент ex70");
?><?$APPLICATION->IncludeComponent(
	"simplecomp.exam-ex70", 
	".default", 
	array(
		"COMPONENT_TEMPLATE" => ".default",
		"CACHE_TYPE" => "A",
		"CACHE_TIME" => "36000000",
		"PRODUCTS_IBLOCK_ID" => "2",
		"NEWS_IBLOCK_ID" => "1",
		"PROPERTY_CODE" => "UF_NEWS_LINK"
	),
	false
);?><?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>
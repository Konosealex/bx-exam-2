<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Простой компонент ex97");
?><?$APPLICATION->IncludeComponent(
	"simplecomp.exam-ex97", 
	".default", 
	array(
		"PRODUCTS_IBLOCK_ID" => "2",
		"COMPONENT_TEMPLATE" => ".default",
		"CACHE_TYPE" => "A",
		"CACHE_TIME" => "36000000",
		"NEWS_IBLOCK_ID" => "1",
		"PROPERTY_AUTHOR" => "UF_AUTHOR_TYPE",
		"PROPERTY_AUTHOR_TYPE" => "AUTHOR"
	),
	false
);?><?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>
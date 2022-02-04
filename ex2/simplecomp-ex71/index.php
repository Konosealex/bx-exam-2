<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Простой компонент ex71");
?><?$APPLICATION->IncludeComponent(
	"simplecomp.exam-ex71", 
	".default", 
	array(
		"COMPONENT_TEMPLATE" => ".default",
		"CACHE_TYPE" => "A",
		"CACHE_TIME" => "36000000",
		"PRODUCTS_IBLOCK_ID" => "2",
		"CLASSIFICATOR_IBLOCK_ID" => "7",
		"DETAIL_LINK" => "/products/#SECTION_ID#/#ELEMENT_ID#/",
		"PROPERTY_CODE" => "COMPANY"
	),
	false
);?><?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>
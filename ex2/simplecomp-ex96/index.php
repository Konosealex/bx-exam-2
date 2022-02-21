<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Простой компонент ex96");
?><?$APPLICATION->IncludeComponent(
	"simplecomp.exam-ex96",
	"",
	Array(
		"CACHE_TIME" => "36000000",
		"CACHE_TYPE" => "A",
		"PRODUCTS_IBLOCK_ID" => "2",
		"PROPERTY_CODE" => "IN_FAVORITE"
	)
);?><?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>
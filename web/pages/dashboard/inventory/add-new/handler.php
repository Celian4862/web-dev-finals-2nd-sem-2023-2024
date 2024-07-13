<?php

use Utilities\Helper;

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    Helper::redirect("/dashboard/employees/add-new");
}

$db = Helper::getDatabase();

$physicalStocks = intval($_POST["physicalStocks"]);

$product = Helper::removeEmptyValues(array_diff_key($_POST, ["physicalStocks" => ""]));

$product["buyingPrice"] = floatval($product["buyingPrice"]);
$product["sellingPrice"] = floatval($product["sellingPrice"]);
$product["desiredStocks"] = intval($product["desiredStocks"]);

$productEncode = json_encode($product);

$stocksEncode = [];

for ($i = 0; $i < $physicalStocks; $i++) {
    $stocksEncode[] = <<<SQL
    LET \$physicalStock = (CREATE ONLY physicalProduct);
    RELATE (\$product.id)->stock->(\$physicalStock.id);
    SQL;
}

$sqlStocksEncode = implode("\n", $stocksEncode);

$db->query(implode("\n", [
    "LET \$product = (CREATE ONLY product CONTENT $productEncode);",
    $sqlStocksEncode
]));

Helper::redirect("/dashboard/inventory");

<?php

use Utilities\Helper;

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    Helper::redirect("/dashboard/inventory");
}

$db = Helper::getDatabase();

if (isset($_POST["close"])) {
    unset($_SESSION["inputs"], $_SESSION["edit"]);

    Helper::redirect(
        Helper::getURLPathQuery(
            "/dashboard/inventory",
            array_diff_key(Helper::getURLQuery(), ["info" => ""])
        )
    );
}

if (isset($_POST["edit"])) {
    $_SESSION["edit"][$_POST["edit"]] =  !($_SESSION["edit"][$_POST["edit"]] ?? false);
    $_SESSION["inputs"] = array_merge($_SESSION["inputs"] ?? [], array_diff_key(($_POST), ["edit" => ""]));
} elseif (isset($_POST["updateProduct"])) {
    $productID = $_POST["updateProduct"];

    $data = array_diff_key(Helper::removeEmptyValues($_POST), ["updateProduct" => ""]);

    if (!empty($data)) {
        if (isset($data["physicalStocks"])) {
            $physicalStocks = intval($data["physicalStocks"]);

            $currentPhysicalStocks = $db->query("array::len((SELECT id FROM {$productID}->stock));");

            $stocksEncode = [];

            for ($i = $currentPhysicalStocks; $i < $physicalStocks; $i++) {
                $stocksEncode[] = <<<SQL
                LET \$physicalProduct = (CREATE ONLY physicalProduct);
                RELATE {$productID}->stock->(\$physicalProduct.id);
                SQL;
            }

            $sqlStocksEncode = implode("\n", $stocksEncode);
            $data = array_diff_key($data, ["physicalStocks" => ""]);
        }

        $data["buyingPrice"] = floatval($data["buyingPrice"] ?? "");
        $data["sellingPrice"] = floatval($data["sellingPrice"] ?? "");
        $data["desiredStocks"] = intval($data["desiredStocks"] ?? "");

        $dataEncode = json_encode($data);

        $db->query(implode("\n", [
            ($sqlStocksEncode ?? ""),
            "UPDATE $productID MERGE $dataEncode"
        ]));
    }

    unset($_SESSION["inputs"], $_SESSION["edit"]);
} elseif (isset($_POST["deleteProduct"])) {
    $db->query("UPDATE {$_POST["deleteProduct"]} SET time.deletedAt = time::now();");

    unset($_SESSION["inputs"], $_SESSION["edit"]);

    Helper::redirect(
        Helper::getURLPathQuery(
            "/dashboard/inventory",
            array_diff_key(Helper::getURLQuery(), ["info" => ""])
        )
    );
}

Helper::redirect(Helper::getURLPathQuery("/dashboard/inventory"));

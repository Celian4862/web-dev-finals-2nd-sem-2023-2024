<?php

// TODO: This needs to be refactored, it's a mess

use Utilities\Helper;

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    Helper::redirect("/dashboard/receptions/add-new");
}

$db = Helper::getDatabase();

$sqlDelivery = <<<SQL
LET \$delivery = (CREATE ONLY delivery CONTENT {
    description: "{$_POST["deliveryDescription"]}",
    dateShipped: <datetime> "{$_POST["dateShipped"]}",
});
SQL;

$sqlDeliveryStatusLine = <<<SQL
RELATE (\$delivery.id)->deliveryStatusLine->{$_POST["status"]}
SET
    description = "{$_POST["statusDescription"]}",
    eventDatetime = time::now();
SQL;

$sqlReception = <<<SQL
LET \$reception = (CREATE ONLY reception CONTENT {
    distributor: {$_POST["distributor"]},
    delivery: \$delivery.id,
});
SQL;

$receptionLineEncode = [];
$physicalProductEncode = [];

foreach ($_POST["products"] as $productId => $quantity) {
    $quantity = intval($quantity);

    if ($quantity > 0) {
        $receptionLineEncode[] = <<<SQL
        RELATE (\$reception.id)->productLine->{$productId} SET quantity = $quantity;
        SQL;

        for ($i = 0; $i < $quantity; $i++) {
            $physicalProductEncode[] = <<<SQL
            LET \$physicalProduct = (CREATE ONLY physicalProduct);
            RELATE {$productId}->stock->(\$physicalProduct.id);
            SQL;
        }
    }
}

$db->query(implode("\n", [
    $sqlDelivery,
    $sqlDeliveryStatusLine,
    $sqlReception,
    implode("\n", $receptionLineEncode),
    implode("\n", $physicalProductEncode),
]));

Helper::redirect("/dashboard/receptions");

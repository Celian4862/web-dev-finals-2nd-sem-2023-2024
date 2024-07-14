<?php

use Utilities\Helper;

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    Helper::redirect("/dashboard/orders/add-new");
}

Helper::debug($_POST);

$db = Helper::getDatabase();

$sqlAddress = <<<SQL
LET \$address = (CREATE ONLY address CONTENT {
    country: "{$_POST["address"]["country"]}",
    city: "{$_POST["address"]["city"]}",
    street: "{$_POST["address"]["street"]}",
    zipCode: "{$_POST["address"]["zipCode"]}",
});
SQL;

$sqlDelivery = <<<SQL
LET \$delivery = (CREATE ONLY delivery SET address = \$address.id);
SQL;

$sqlDeliveryStatusLine = <<<SQL
RELATE (\$delivery.id)->deliveryStatusLine->deliveryStatus:0;
SQL;

$sqlOrder = <<<SQL
LET \$order = (CREATE ONLY order CONTENT {
    client: {$_POST["client"]},
    employee: {$_POST["employee"]},
    status: {$_POST["orderStatus"]},
    description: "{$_POST["description"]}",
    dateOrdered: <datetime> "{$_POST["dateOrdered"]}",
    delivery: \$delivery.id,
});
SQL;

$orderLineEncode = [];
$physicalProductEncode = [];

foreach ($_POST["products"] as $productId => $quantity) {
    $quantity = intval($quantity);

    if ($quantity > 0) {
        $orderLineEncode[] = <<<SQL
        RELATE (\$order.id)->productLine->{$productId}
        SET
            quantity = $quantity,
            amount = $quantity * out.sellingPrice;
        SQL;

        $physicalProductEncode[] = <<<SQL
        FOR \$stock IN (
            SELECT VALUE id FROM ($productId)->stock
            WHERE
                time.deletedAt IS NONE AND
                status = stockStatus:0
            LIMIT $quantity
        ) {
            UPDATE \$stock SET status = stockStatus:1;
        };
        SQL;
    }
}

$db->query(implode("\n", [
    $sqlAddress,
    $sqlDelivery,
    $sqlDeliveryStatusLine,
    $sqlOrder,
    implode("\n", $orderLineEncode),
    implode("\n", $physicalProductEncode),
]));

Helper::redirect("/dashboard/orders");

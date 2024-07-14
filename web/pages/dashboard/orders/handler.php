<?php

use Utilities\Helper;

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    Helper::redirect("/dashboard/orders");
}

$db = Helper::getDatabase();

if (isset($_POST["close"])) {
    unset($_SESSION["inputs"], $_SESSION["edit"]);

    Helper::redirect(
        Helper::getURLPathQuery(
            "/dashboard/orders",
            array_diff_key(Helper::getURLQuery(), ["info" => ""])
        )
    );
}

if (isset($_POST["edit"])) {
    $_SESSION["edit"][$_POST["edit"]] =  !($_SESSION["edit"][$_POST["edit"]] ?? false);
    $_SESSION["inputs"] = array_merge($_SESSION["inputs"] ?? [], array_diff_key(($_POST), ["edit" => ""]));
} elseif (isset($_POST["updateOrder"])) {
    $orderID = $_POST["updateOrder"];

    $data = array_diff_key(Helper::removeEmptyValues($_POST), ["updateOrder" => ""]);

    if (isset($_SESSION["edit"]["information"])) {
        $sqlOrder = <<<SQL
        UPDATE ONLY {$orderID} MERGE {
            client: {$data["client"]},
            employee: {$data["employee"]},
            dateOrdered: <datetime> "{$data["dateOrdered"]}",
            description: "{$data["description"]}",
            status: {$data["status"]}
        };
        SQL;
    }

    if (isset($_SESSION["edit"]["address"])) {
        $sqlAddress = <<<SQL
        UPDATE ONLY {$orderID}.delivery.address MERGE {
            country: "{$data["address"]["country"]}",
            city: "{$data["address"]["city"]}",
            street: "{$data["address"]["street"]}",
            zipCode: "{$data["address"]["zipCode"]}",
        };
        SQL;
    }

    if (isset($sqlOrder) || isset($sqlAddress)) {
        $db->query(implode("\n", [
            ($sqlOrder ?? ""),
            ($sqlAddress ?? "")
        ]));
    }

    unset($_SESSION["inputs"], $_SESSION["edit"]);
} elseif ($_POST["deleteOrder"]) {
    $orderID = $_POST["deleteOrder"];

    $db->query(<<<SQL
    UPDATE $orderID SET time.deletedAt = time::now();
    SQL);

    unset($_SESSION["inputs"], $_SESSION["edit"]);

    Helper::redirect(
        Helper::getURLPathQuery(
            "/dashboard/orders",
            array_diff_key(Helper::getURLQuery(), ["info" => ""])
        )
    );
}

Helper::redirect(Helper::getURLPathQuery("/dashboard/orders"));

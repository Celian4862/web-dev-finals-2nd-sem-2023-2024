<?php

use Utilities\Helper;

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    Helper::redirect("/dashboard/receptions");
}

$db = Helper::getDatabase();

if (isset($_POST["close"])) {
    unset($_SESSION["inputs"], $_SESSION["edit"]);

    Helper::redirect(
        Helper::getURLPathQuery(
            "/dashboard/receptions",
            array_diff_key(Helper::getURLQuery(), [
                "info" => "",
                "addStatus" => 0,
            ])
        )
    );
}

if (isset($_POST["edit"])) {
    $_SESSION["edit"][$_POST["edit"]] =  !($_SESSION["edit"][$_POST["edit"]] ?? false);
    $_SESSION["inputs"] = array_merge($_SESSION["inputs"] ?? [], array_diff_key(($_POST), ["edit" => ""]));
} elseif (isset($_POST["updateReception"])) {
    $receptionID = $_POST["updateReception"];

    $reception = array_diff_key($_POST, ["updateReception" => ""]);

    if (isset($reception["addStatus"])) {
        $addStatus = $reception["addStatus"];

        $reception = array_diff_key($reception, ["addStatus" => ""]);

        $sqlAddStatus = <<<SQL
        RELATE ({$receptionID}.delivery)->deliveryStatusLine->{$addStatus["status"]}
        SET
            description = "{$addStatus["description"]}",
            eventDatetime = <datetime> "{$addStatus["eventDatetime"]}Z"
        SQL;
    }

    if (isset($reception["distributor"])) {
        $sqlReception = <<<SQL
        UPDATE $receptionID SET distributor = {$reception["distributor"]};
        UPDATE {$receptionID}.delivery MERGE {
            description: "{$reception["description"]}",
            dateShipped: <datetime> "{$reception["dateShipped"]}",
        };
        SQL;
    }

    $db->query(implode("\n", [
        ($sqlAddStatus ?? ""),
        ($sqlReception ?? "")
    ]));

    unset($_SESSION["inputs"], $_SESSION["edit"]);
} elseif (isset($_POST["deleteDeliveryStatusLine"])) {
    $db->query("UPDATE {$_POST["deleteDeliveryStatusLine"]} SET time.deletedAt = time::now();");
} elseif (isset($_POST["deleteReception"])) {
    $db->query("UPDATE {$_POST["deleteReception"]} SET time.deletedAt = time::now();");

    unset($_SESSION["inputs"], $_SESSION["edit"]);

    Helper::redirect(
        Helper::getURLPathQuery(
            "/dashboard/receptions",
            array_diff_key(Helper::getURLQuery(), ["info" => ""])
        )
    );
}

Helper::redirect(Helper::getURLPathQuery("/dashboard/receptions"));

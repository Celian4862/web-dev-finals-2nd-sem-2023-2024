<?php

use Utilities\Helper;

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    Helper::redirect("/dashboard/distributors");
}

$db = Helper::getDatabase();

if (isset($_POST["close"])) {
    session_start();

    unset($_SESSION["edit"]);
    unset($_SESSION["inputs"]);

    Helper::redirect(
        Helper::getURLPathQuery(
            "/dashboard/distributors",
            array_diff_key(Helper::getURLQuery(), ["info" => ""])
        )
    );
}

if (isset($_POST["edit"])) {
    session_start();

    $_SESSION["edit"][$_POST["edit"]] =  !($_SESSION["edit"][$_POST["edit"]] ?? false);
    $_SESSION["inputs"] = array_merge($_SESSION["inputs"] ?? [], array_diff_key(($_POST), ["edit" => ""]));
} elseif (isset($_POST["updateDistributor"])) {
    $distributorID = $_POST["updateDistributor"];

    $data = array_diff_key(Helper::removeEmptyValues($_POST), ["updateDistributor" => ""]);

    $sqlPerson = <<<SQL
    LET \$person = (SELECT person FROM ONLY $distributorID)["person"];
    SQL;

    if (isset($data["address"])) {
        $addressEncode = json_encode($data["address"]);

        $data = array_diff_key($data, ["address" => ""]);

        $sqlUpdateAddress = <<<SQL
        IF \$person.address = NONE THEN
            UPDATE \$person SET address = (CREATE ONLY address CONTENT $addressEncode)["id"]
        ELSE 
            UPDATE \$person.address MERGE $addressEncode
        END;
        SQL;
    }

    if (!empty($data)) {
        $personEncode = json_encode(Helper::removeEmptyValues($data));

        $sqlUpdatePerson = "UPDATE \$person MERGE $personEncode;";
    }

    if (isset($sqlUpdateAddress) || isset($sqlUpdatePerson)) {
        $db->query(implode("\n", [
            $sqlPerson,
            ($sqlUpdateAddress ?? ""),
            ($sqlUpdatePerson ?? "")
        ]));
    }
} elseif (isset($_POST["deleteDistributor"])) {
    $distributorID = $_POST["deleteDistributor"];

    $db->query("UPDATE $distributorID SET time.deletedAt = time::now()");
}

Helper::redirect(Helper::getURLPathQuery("/dashboard/distributors"));

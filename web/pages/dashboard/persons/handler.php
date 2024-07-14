<?php

use Utilities\Helper;

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    Helper::redirect("/dashboard/persons");
}

$db = Helper::getDatabase();

if (isset($_POST["close"])) {
    unset($_SESSION["inputs"], $_SESSION["edit"]);

    Helper::redirect(
        Helper::getURLPathQuery(
            "/dashboard/persons",
            array_diff_key(Helper::getURLQuery(), ["info" => ""])
        )
    );
}

if (isset($_POST["edit"])) {
    $_SESSION["edit"][$_POST["edit"]] =  !($_SESSION["edit"][$_POST["edit"]] ?? false);
    $_SESSION["inputs"] = array_merge($_SESSION["inputs"] ?? [], array_diff_key(($_POST), ["edit" => ""]));
} elseif (isset($_POST["updatePerson"])) {
    $personID = $_POST["updatePerson"];

    $data = array_diff_key(Helper::removeEmptyValues($_POST), ["updatePerson" => ""]);

    if (isset($data["address"])) {
        $addressEncode = json_encode($data["address"]);

        $data = array_diff_key($data, ["address" => ""]);

        $sqlUpdateAddress = <<<SQL
        IF {$personID}.address = NONE THEN
            UPDATE {$personID} SET address = (CREATE ONLY address CONTENT $addressEncode).id
        ELSE 
            UPDATE {$personID}.address MERGE $addressEncode
        END;
        SQL;
    }

    if (!empty($data)) {
        $personEncode = json_encode(Helper::removeEmptyValues($data));

        $sqlUpdatePerson = "UPDATE {$personID} MERGE $personEncode;";
    }

    if (isset($sqlUpdateAddress) || isset($sqlUpdatePerson)) {
        $db->query(implode("\n", [
            ($sqlUpdateAddress ?? ""),
            ($sqlUpdatePerson ?? "")
        ]));
    }

    unset($_SESSION["inputs"], $_SESSION["edit"]);
} elseif (isset($_POST["setClient"])) {
    $personID = $_POST["setClient"];

    $db->query(<<<SQL
    IF (SELECT id FROM ONLY client WHERE person = $personID LIMIT 1) IS NONE THEN
        CREATE ONLY client SET person = $personID
    ELSE
        UPDATE client SET time.deletedAt = NONE WHERE person = $personID
    END;
    SQL);
} elseif (isset($_POST["setDistributor"])) {
    $personID = $_POST["setDistributor"];

    $db->query(<<<SQL
    IF (SELECT person FROM ONLY distributor WHERE person = $personID LIMIT 1) IS NONE THEN
        CREATE ONLY distributor SET person = $personID
    ELSE
        UPDATE distributor SET time.deletedAt = NONE WHERE person = $personID
    END;
    SQL);
} elseif (isset($_POST["deleteClient"])) {
    $personID = $_POST["deleteClient"];

    $db->query(<<<SQL
    UPDATE client
    SET time.deletedAt = time::now()
    WHERE person = $personID;
    SQL);
} elseif (isset($_POST["deleteDistributor"])) {
    $personID = $_POST["deleteDistributor"];

    $db->query(<<<SQL
    UPDATE distributor
    SET time.deletedAt = time::now()
    WHERE person = $personID;
    SQL);
} elseif (isset($_POST["deletePerson"])) {
    $personID = $_POST["deletePerson"];

    $db->query("UPDATE $personID SET time.deletedAt = time::now()");

    unset($_SESSION["inputs"], $_SESSION["edit"]);

    Helper::redirect(
        Helper::getURLPathQuery(
            "/dashboard/persons",
            array_diff_key(Helper::getURLQuery(), ["info" => ""])
        )
    );
}

Helper::redirect(Helper::getURLPathQuery("/dashboard/persons"));

<?php

// TODO: Refactor this, because it's a mess

use Utilities\Helper;

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    Helper::redirect("/dashboard/distributors/add-new");
}

$db = Helper::getDatabase();

if (isset($_POST["address"])) {
    $addressEncode = json_encode($_POST["address"]);

    $sqlAddress = "LET \$address = (CREATE ONLY address CONTENT $addressEncode);";
}

if (isset($_POST["contact"])) {
    $_POST["contact"] = Helper::removeEmptyValues($_POST["contact"]);
}

$distributor = Helper::removeEmptyValues(array_diff_key($_POST, ["address" => ""]));

$distributorEncode = json_encode($distributor);

if (isset($sqlAddress)) {
    $db->query(<<<SQL
    LET \$person = CREATE ONLY person CONTENT $distributorEncode;
    $sqlAddress

    UPDATE \$person SET address = \$address.id;

    CREATE ONLY distributor CONTENT {
        person: \$person.id,
    };
    SQL);
} else {
    $db->query(<<<SQL
    LET \$person = CREATE ONLY person CONTENT $distributorEncode;

    CREATE ONLY distributor CONTENT {
        person: \$person.id,
    };
    SQL);
}

Helper::redirect("/dashboard/distributors");

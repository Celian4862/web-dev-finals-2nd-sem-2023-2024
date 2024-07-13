<?php

// TODO: Refactor this, because it's a mess

use Utilities\Helper;

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    Helper::redirect("/dashboard/person/add-new");
}

$db = Helper::getDatabase();

if (isset($_POST["address"])) {
    $addressEncode = json_encode($_POST["address"]);

    $sqlAddress = "LET \$address = (CREATE ONLY address CONTENT $addressEncode);";
}

if (isset($_POST["contact"])) {
    $_POST["contact"] = Helper::removeEmptyValues($_POST["contact"]);
}

$person = Helper::removeEmptyValues(array_diff_key($_POST, ["address" => ""]));

$personEncode = json_encode($person);

if (isset($sqlAddress)) {
    $sqlPerson = <<<SQL
    LET \$person = CREATE ONLY person CONTENT $personEncode;
    $sqlAddress

    UPDATE \$person SET address = \$address.id;
    SQL;
} else {
    $sqlPerson = "LET \$person = CREATE ONLY person CONTENT $personEncode;";
}

if (isset($_POST["isClient"])) {
    $sqlCleint = "CREATE ONLY client SET person = \$person.id;";
}

if (isset($_POST["isDistributor"])) {
    $sqlDistributor = "CREATE ONLY distributor SET person = \$person.id";
}

$db->query(implode("\n", [
    $sqlPerson,
    ($sqlCleint ?? ""),
    ($sqlDistributor ?? "")
]));

Helper::redirect("/dashboard/persons");

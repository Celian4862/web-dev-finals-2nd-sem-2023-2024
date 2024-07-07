<?php

use Utilities\Helper;

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    Helper::redirect("/dashboard/employees/add-new");
}

$address = $_POST["address"];
$employee = array_diff_key($_POST, ["address" => ""]);

$employee["password"] = password_hash($employee["password"], PASSWORD_DEFAULT);

$employee["details"]["fullName"] = Helper::removeEmptyValues($employee["details"]["fullName"]);
$employee["details"]["contact"] = Helper::removeEmptyValues($employee["details"]["contact"]);
$employee["details"] = Helper::removeEmptyValues($employee["details"]);

$addressEncode = json_encode($address);
$employeeEncode = json_encode($employee);

Helper::getDatabase()->query(<<<SQL
    LET \$addressID = (CREATE ONLY address CONTENT $addressEncode)["id"];
    LET \$employeeID = (CREATE ONLY employee CONTENT $employeeEncode)["id"];

    RELATE \$employeeID->addressLine->\$addressID;
SQL);

Helper::redirect("/dashboard/employees");

<?php

use Utilities\Helper;

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    Helper::redirect("/dashboard/employees");
}

$db = Helper::getDatabase();

if (isset($_POST["close"])) {
    unset($_SESSION["inputs"], $_SESSION["edit"]);

    Helper::redirect(
        Helper::getURLPathQuery(
            "/dashboard/employees",
            array_diff_key(Helper::getURLQuery(), [
                "info" => "",
                "addAddress" => 0,
            ])
        )
    );
}

if (isset($_POST["edit"])) {
    $_SESSION["edit"][$_POST["edit"]] =  !($_SESSION["edit"][$_POST["edit"]] ?? false);
    $_SESSION["inputs"] = array_merge($_SESSION["inputs"] ?? [], array_diff_key(($_POST), ["edit" => ""]));
} elseif (isset($_POST["updateEmployee"])) {
    $employeeID = $_POST["updateEmployee"];

    $data = array_diff_key(Helper::removeEmptyValues($_POST), ["updateEmployee" => ""]);

    if (isset($data["details"])) {
        $data["details"] = Helper::removeEmptyValues($data["details"]);

        if (isset($data["details"]["fullName"])) {
            $data["details"]["fullName"] = Helper::removeEmptyValues($data["details"]["fullName"]);
        }
    }

    if (isset($data["addressLine"])) {
        $updateAddress = [];

        foreach ($data["addressLine"] as $addressID => $address) {
            $addressEncode = json_encode(Helper::removeEmptyValues($address));

            $updateAddress[] = "UPDATE $addressID MERGE $addressEncode;";
        }

        $data = array_diff_key($data, ["addressLine" => []]);

        $sqlUpdateAddress = implode("\n", $updateAddress);
    }

    if (isset($data["addAddress"])) {
        $addressEncode = json_encode($data["addAddress"]);

        $data = array_diff_key($data, ["addAddress" => []]);

        $sqlAddAddress = <<<SQL
        LET \$address = (CREATE ONLY address CONTENT $addressEncode);

        RELATE {$employeeID}->addressLine->(\$address.id);
        SQL;
    }

    if (!empty($data)) {
        if (password_needs_rehash($data["password"], PASSWORD_DEFAULT)) {
            $data["password"] = password_hash($data["password"], PASSWORD_DEFAULT);
        } else {
            unset($data["password"]);
        }

        $dataEncode = json_encode($data);

        $sqlUpdateEmployee = "UPDATE $employeeID MERGE $dataEncode;";
    }

    if (isset($sqlUpdateEmployee) || isset($sqlUpdateAddress) || isset($sqlAddAddress)) {
        $db->query(implode("\n", Helper::removeEmptyValues([
            ($sqlUpdateEmployee ?? ""),
            ($sqlUpdateAddress ?? ""),
            ($sqlAddAddress ?? "")
        ])));
    }

    unset($_SESSION["inputs"], $_SESSION["edit"]);
} elseif (isset($_POST["setPrimaryAddress"])) {
    [$employeeID, $addressLine] = json_decode($_POST["setPrimaryAddress"]);

    $db->query(<<<SQL
    UPDATE {$employeeID}->addressLine SET primary = false;
    UPDATE $addressLine SET primary = true;
    SQL);
} elseif (isset($_POST["deleteAddressLine"])) {
    $db->query(<<<SQL
    UPDATE (SELECT out FROM ONLY {$_POST["deleteAddressLine"]}).out SET time.deletedAt = time::now();
    UPDATE {$_POST["deleteAddressLine"]} SET time.deletedAt = time::now();
    SQL);
} elseif (isset($_POST["deleteEmployee"])) {
    $db->query("UPDATE {$_POST["deleteEmployee"]} SET time.deletedAt = time::now();");

    unset($_SESSION["inputs"], $_SESSION["edit"]);

    Helper::redirect(
        Helper::getURLPathQuery(
            "/dashboard/employees",
            array_diff_key(Helper::getURLQuery(), ["info" => ""])
        )
    );
}

Helper::redirect(Helper::getURLPathQuery("/dashboard/employees"));

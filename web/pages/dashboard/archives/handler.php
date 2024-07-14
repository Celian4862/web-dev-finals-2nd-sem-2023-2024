<?php

use Utilities\Helper;

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    Helper::redirect("/dashboard/archives");
}

Helper::getDatabase()->query(<<<SQL
UPDATE {$_POST["restore"]} SET time.deletedAt = NONE;
SQL);

Helper::redirect(Helper::getURLPathQuery("/dashboard/archives"));

<?php

use Utilities\Helper;

if ($_SERVER["REQUEST_METHOD"] === "DELETE" || isset($_GET["_method"]) && $_GET["_method"] === "DELETE") {
    session_start();
    unset($_SESSION["employee"]);
    Helper::redirect("/login");
} else {
    Helper::redirect("/404");
}

<?php

use function Utilities\redirect;

if ($_SERVER["REQUEST_METHOD"] === "DELETE" || isset($_GET["_method"]) && $_GET["_method"] === "DELETE") {
    session_start();
    unset($_SESSION["employee"]);
    redirect("/login");
} else {
    redirect("/404");
}

<?php

use Utilities\Helper;

if ($_SERVER["REQUEST_METHOD"] === "DELETE" || isset($_GET["_method"]) && $_GET["_method"] === "DELETE") {
    session_unset();
    unset($_COOKIE["token"]);
    setcookie("token", "", -1, "/");
    Helper::redirect("/login");
} else {
    Helper::redirect("/404");
}

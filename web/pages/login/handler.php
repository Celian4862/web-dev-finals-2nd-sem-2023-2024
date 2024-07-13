<?php

use Utilities\Helper;

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    Helper::redirect("/login");
}

use Utilities\Employee;

switch ($jwtToken = Employee::login($_POST["email"], $_POST["password"])) {
    case "not admin":
        $_SESSION["error"]["notAdmin"] = true;
        $_SESSION["previous"]["email"] = $_POST["email"];
        Helper::redirect("/login");
        break;
    case "email":
        $_SESSION["error"]["email"] = true;
        $_SESSION["previous"]["email"] = $_POST["email"];
        Helper::redirect("/login");
        break;
    case "password":
        $_SESSION["error"]["password"] = true;
        $_SESSION["previous"]["email"] = $_POST["email"];
        Helper::redirect("/login");
        break;
    default:
        if (isset($_POST["remember"]) && $_POST["remember"] === "on") {
            setcookie("token", $jwtToken, time() + (86400 * 30), "/");
        } else {
            $_SESSION["token"] = $jwtToken;
        }

        Helper::redirect("/dashboard");
}

<?php

use Utilities\Helper;

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    Helper::redirect("/login");
}

session_start();

use Utilities\Employee;

switch ($result = Employee::login($_POST["email"], $_POST["password"])) {
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
        $_SESSION["employee"] = $result;
        Helper::redirect("/dashboard");
}

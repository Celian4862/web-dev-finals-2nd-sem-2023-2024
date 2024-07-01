<?php

use function Utilities\redirect;

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    redirect("/login");
}

session_start();

use Utilities\Employee;

switch ($result = Employee::login($_POST["email"], $_POST["password"])) {
    case "email":
        $_SESSION["error"]["email"] = true;
        $_SESSION["previous"]["email"] = $_POST["email"];
        redirect("/login");
        break;
    case "password":
        $_SESSION["error"]["password"] = true;
        $_SESSION["previous"]["email"] = $_POST["email"];
        redirect("/login");
        break;
    default:
        $_SESSION["employee"] = $result;
        redirect("/dashboard");
}

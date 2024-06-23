<?php
require_once WEB_PATH . "/utilities/redirect.php";

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
	redirect("/login");
}

require_once WEB_PATH . "/utilities/employee.php";

session_start();

switch ($result = Employee::login(filter_input(INPUT_POST, "email", FILTER_VALIDATE_EMAIL), $_POST["password"])) {
	case "email":
		$_SESSION["error"] = ["email" => true];
		$_SESSION["previous"] = ["email" => $_POST["email"]];
		redirect("/login");
	case "password":
		$_SESSION["error"] = ["password" => true];
		$_SESSION["previous"] = ["email" => $_POST["email"]];
		redirect("/login");
	default:
		$_SESSION["employee"] = $result;
		redirect("/dashboard");
}

<?php

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST["email"];
    $pwd = $_POST["pwd"];

    try {
        require_once 'dbh.inc.php';
        require_once 'login_model.inc.php';
        require_once 'login_control.inc.php';

        $errors = [];

        if(is_input_empty($email, $pwd)) {
            $errors["empty_input"] = "Fill in all fields!";
        }

        $result = get_user($pdo, $email);

        if(is_email_wrong($result)) {
            $errors["login_incorrect"] = "Incorrect email or password!";
        }
        if(is_password_wrong($pwd, $result['password'])) {
            $errors["login_incorrect"] = "Incorrect email or password!";
        }

        header("Location: ../../dashboard/index.html?login=success");
        $pdo = null;
        $stmt = null;

        die();

    } catch (PDOException $e) {
        die("Query failed: " . $e->getMessage());
    }
}
else {
    header("Location: ../index.php");
    die();
}
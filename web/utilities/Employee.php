<?php

namespace Utilities;

use Utilities\Auth;
use Utilities\Helper;

class Employee
{
    /** Creates a new employee object if the credentails are correct. */
    public static function login(string $email, string $password): string
    {
        $employee = Helper::getDatabase()->query("SELECT * FROM ONLY employee WHERE email = '$email' LIMIT 1");

        if (!$employee) {
            return "email";
        }

        // Check if the email is the admin email.
        if (strcmp($email, "admin@admin.admin")) {
            return "not admin";
        }

        if (password_verify($password, $employee["password"]) === false) {
            return "password";
        }

        $_SESSION["employeeID"] = $employee["id"];

        return Auth::generate($email, $password);
    }
}

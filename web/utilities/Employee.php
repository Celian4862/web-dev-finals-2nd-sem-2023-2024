<?php

namespace Utilities;

use Utilities\Helper;

class Employee
{
    /** Creates a new employee object if the credentails are correct. */
    public static function login(string $email, string $password): string
    {
        $employee = Helper::getDatabase()->query("SELECT * FROM ONLY employee WHERE email = '$email' LIMIT 1");

        if ($employee == null) {
            return "email";
        }

        if (password_verify($password, $employee["password"]) === false) {
            return "password";
        }

        return "JWT";
    }
}

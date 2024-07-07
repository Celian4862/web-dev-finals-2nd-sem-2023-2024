<?php

namespace Utilities;

use Utilities\Helper;

class Employee
{
    public readonly string $id;
    public readonly string $email;
    public readonly array $details;
    public readonly array $time;

    public function __construct(array $employee)
    {
        $this->id = $employee["id"];
        $this->email = $employee["email"];
        $this->details = $employee["details"];
        $this->time = $employee["time"];
    }

    /** Creates a new employee object if the credentails are correct. */
    public static function login(string $email, string $password): self|string
    {
        $employee = Helper::getDatabase()->query("SELECT * FROM ONLY employee WHERE email = '$email' LIMIT 1");

        if ($employee == null) {
            return "email";
        }

        if (password_verify($password, $employee["password"]) === false) {
            return "password";
        }

        return new Employee($employee);
    }
}

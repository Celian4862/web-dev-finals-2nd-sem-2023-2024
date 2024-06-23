<?php

namespace Utilities;

class Employee
{
    public readonly string $id;
    public readonly string $email;
    public readonly string $password;
    public readonly array $details;
    public readonly array $time;

    public function __construct(array $employee)
    {
        $this->id = $employee["id"];
        $this->email = $employee["email"];
        $this->password = $employee["password"];
        $this->details = $employee["details"];
        $this->time = $employee["time"];
    }

    /** Creates a new employee object if the credentails are correct. */
    public static function login(string $email, string $password): self|string
    {
        require_once "../utilities/db.php";

        $employee = $db->query("SELECT * FROM ONLY employee WHERE email = '$email' LIMIT 1");

        if ($employee == null) {
            return "email";
        }

        if ($employee["password"] !== $password) {
            return "password";
        }

        $db->disconnect();
        return new Employee($employee);
    }
}

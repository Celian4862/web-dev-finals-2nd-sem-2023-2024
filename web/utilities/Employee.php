<?php

namespace Utilities;

use function Utilities\getDatabase;

class Employee
{
    public readonly string $id;
    public readonly string $email;
    public readonly array $time;

    private array $details;

    public function __construct(array $employee)
    {
        $this->id = $employee["id"];
        $this->email = $employee["email"];
        $this->details = $employee["details"];
        $this->time = $employee["time"];
    }

    /** Returns the full name of the employee. */
    public function getFullName(): string
    {
        $fullName = $this->details["fullName"];

        return $fullName["firstName"] . ' ' . $fullName["middleName"][0] . '. ' . $fullName["lastName"];
    }

    public function getContact(): string
    {
        $contact = $this->details["contact"];

        return $contact["phoneNumber"] . ' | ' . $contact["email"];
    }

    /** Creates a new employee object if the credentails are correct. */
    public static function login(string $email, string $password): self|string
    {
        $db = getDatabase();

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

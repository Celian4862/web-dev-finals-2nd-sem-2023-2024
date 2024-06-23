<?php
require_once "../services/db.php";

class Employee
{
	readonly public string $id;
	readonly public string $email;
	readonly public string $password;
	readonly public array $details;
	readonly public array $time;

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
		global $db;

		$employee = $db->query("SELECT * FROM ONLY employee WHERE email = '$email' LIMIT 1");

		if ($employee == NULL) {
			return "email";
		}

		if ($employee["password"] !== $password) {
			return "password";
		}

		return new Employee($employee);
	}
}

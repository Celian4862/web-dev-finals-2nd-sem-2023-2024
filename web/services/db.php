<?php

class Surreal
{
	private CurlHandle $engine;
	private array $target;

	public function __construct()
	{
		$this->engine = curl_init();
		$this->target = ["Accept: application/json"];
	}

	/** Connect to the remote Surreal database. */
	public function connect(string $host, ?array $target = null): void
	{
		curl_setopt($this->engine, CURLOPT_URL, $host . "/sql");
		curl_setopt($this->engine, CURLOPT_TIMEOUT, 2);
		curl_setopt($this->engine, CURLOPT_RETURNTRANSFER, true);

		if ($target) {
			$this->use($target);
		}
	}

	/** Signin with a root, namespace, database or scoped user. */
	public function signin(array $data): void
	{
		curl_setopt($this->engine, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
		curl_setopt($this->engine, CURLOPT_USERPWD, $data["user"] . ":" . $data["pass"]);
	}

	/** Use the given namespace and database for the following queries in the current open connection. */
	public function use(array $target): void
	{
		if (isset($target["namespace"])) {
			$this->target[] = "Surreal-NS: " . $target["namespace"];
		}

		if (isset($target["database"])) {
			$this->target[] = "Surreal-DB: " . $target["database"];
		}
	}

	/** Query a raw SurrealQL query. */
	public function query(string $query): array|null
	{
		curl_setopt($this->engine, CURLOPT_HTTPHEADER, $this->target);
		curl_setopt($this->engine, CURLOPT_POSTFIELDS, $query);

		$response = curl_exec($this->engine);
		$http_code = curl_getinfo($this->engine, CURLINFO_HTTP_CODE);

		if (!$http_code) {
			throw new Exception("timeout");
		}

		$response = json_decode($response, true);

		if (isset($response["code"]) && $response["code"] != 200) {
			throw new Exception($response["description"]);
		}

		return end($response)["result"];
	}

	/** Close the connection. */
	public function disconnect(): void
	{
		curl_close($this->engine);
	}

	public function __destruct()
	{
		$this->disconnect();
	}
}

$db = new Surreal();

$db->connect("http://localhost:8000", [
	"namespace" => "n2n",
	"database" => "n2n",
]);

$db->signin([
	"user" => "admin",
	"pass" => "admin",
]);
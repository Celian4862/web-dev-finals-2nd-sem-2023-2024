<?php

namespace Utilities;

use Utilities\Surreal;

/** Get the surreal database connection. */
function getDatabase(): Surreal
{
    $db = new Surreal();

    $db->connect(
        "http://localhost:8000",
        [
            "namespace" => "n2n",
            "database" => "n2n",
        ]
    );

    $db->signin(
        [
            "user" => "admin",
            "pass" => "admin",
        ]
    );

    return $db;
}

/** Redirects the user to the specified path. */
function redirect(string $path): void
{
    header("Location: $path");
    exit;
}

/** Print the data in a readable format on the console. */
function debug(array $data): void
{
    include __DIR__ . "/views/debug.php";
    unset($data);
}

/** Get the path of the current URL. */
function getURLPath(): string
{
    return parse_url($_SERVER["REQUEST_URI"], PHP_URL_PATH);
}

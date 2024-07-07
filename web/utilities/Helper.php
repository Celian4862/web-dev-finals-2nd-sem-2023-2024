<?php

namespace Utilities;

class Helper
{
    /** Get the surreal database connection. */
    public static function getDatabase(): Surreal
    {
        $db = new Surreal();

        $db->connect(
            "http://localhost:8000",
            [
                "namespace" => "dev",
                "database" => "dev",
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
    public static function redirect(string $path): void
    {
        header("Location: $path");
        exit;
    }

    /** Print the data in a readable format on the console. */
    public static function debug(mixed $data): void
    {
        include __DIR__ . "/views/debug.php";

        unset($data);
    }

    /** Toggle array key */
    public static function arrayToggle(array $array, string $key, bool $remove = true): array
    {
        return match ($remove) {
            true => match ($array[$key] ?? null) {
                "1" => array_diff_key($array, [$key => 1]),
                default => array_merge($array, [$key => 1]),
            },
            false => array_merge($array, [$key => ($array[$key] ?? -1) * -1]),
        };
    }

    /** Toggle the value of the query parameter. */
    public static function queryToggle(array $query, string $name, bool $remove = true): string
    {
        return http_build_query(self::arrayToggle($query, $name, $remove));
    }

    /** Get the path of the current URL. */
    public static function getURLPath(): string
    {
        return parse_url($_SERVER["REQUEST_URI"], PHP_URL_PATH);
    }

    /** Get the query of the current URL. */
    public static function getURLQuery(): array
    {
        parse_str(parse_url($_SERVER["REQUEST_URI"], PHP_URL_QUERY) ?? "", $query);

        return $query;
    }

    public static function getURLPathQuery(?string $path = "", ?array $query = null): string
    {
        $query = http_build_query($query ?? self::getURLQuery());

        return (($path ? $path : self::getURLPath())) . ($query ? "?" . $query : "");
    }

    /** Check if the query parameter is set. */
    public static function inputDisabled(array $array, string $name): string
    {
        return (($array[$name] ?? false) === true) ? "" : "disabled";
    }

    /** Check if the query parameter is set. */
    public static function editButtonClass(array $array, string $name): string
    {
        return (($array[$name] ?? false) === true) ? "button-warning" : "button-primary";
    }

    public static function editButtonSymbol(array $array, string $name): string
    {
        return (($array[$name] ?? false) === true) ? "close" : "edit";
    }

    /** Remove empty values from the array. */
    public static function removeEmptyValues(array $data): array
    {
        return array_filter($data, fn ($value) => match (gettype($value)) {
            "array" => !empty($value),
            "string" => trim($value) !== "",
            default => true,
        });
    }
}

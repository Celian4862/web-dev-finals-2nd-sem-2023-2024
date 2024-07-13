<?php

namespace Components;

use Utilities\Helper;

class DashboardTable
{
    /** Render a table in the dashboard. */
    public static function render(
        array $elements,
        array $headers,
        callable $elementData,
        callable $allowSort = null,
        callable $allowSearch = null,
        callable $headerStyle = null,
        callable $rowStyle = null,
        string $class = "px-4 shadow-md"
    ): void {
        include __DIR__ . "/views/dashboard-table.php";

        unset($elements);
        unset($headers);
        unset($elementData);
        unset($allowSort);
        unset($allowSearch);
        unset($elementData);
        unset($headerStyle);
        unset($rowStyle);
        unset($class);
    }

    public static function getColumnQuery(array $query, array $headers, string $column): array
    {
        $columnQuery = $query;

        foreach ($headers as $otherColumn) {
            if ($column === $otherColumn) {
                continue;
            }

            $columnQuery = array_diff_key($columnQuery, [str_replace(' ', '_', $otherColumn) => 1]);
        }

        return $columnQuery;
    }

    public static function getSearchMethod(array $query, array $headers): string|null
    {
        foreach ($headers as $header => $column) {
            $header = str_replace(' ', '_', $header);

            if (isset($query["search"][$header]) && $query["search"][$header]) {
                return <<<SQL
                WHERE
                    string::lowercase(string::concat($column))
                    CONTAINS
                    string::lowercase('{$query["search"][$header]}')
                SQL;
            }
        }

        return null;
    }

    public static function getSortMethod(array $query, array $headers): string
    {
        foreach ($headers as $header => $column) {
            $header = str_replace(' ', '_', $header);

            if (isset($query[$header])) {
                return implode(" ", ["ORDER BY $column", match ($query[$header]) {
                    "1" => "ASC",
                    default => "DESC"
                }]);
            }
        }

        return "ORDER BY createdAt DESC";
    }

    public static function getActiveSort(array $headers): string|null
    {
        foreach ($headers as $column) {
            $name = str_replace(' ', '_', $column);

            if (isset($_GET[$name])) {
                return $name;
            }
        }

        return null;
    }
}

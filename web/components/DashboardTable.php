<?php

namespace Components;

class DashboardTable
{
    /** Render a table in the dashboard. */
    public static function render(
        array $elements,
        array $headers,
        callable $elementData,
        callable $headerStyle = null,
        callable $rowStyle = null,
    ): void {
        include __DIR__ . "/views/dashboard-table.php";

        unset($elements);
        unset($headers);
        unset($elementData);
        unset($headerStyle);
        unset($rowStyle);
    }
}

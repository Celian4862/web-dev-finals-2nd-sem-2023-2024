<?php

use Components\Sidebar;
use Components\DashboardTable;
use Utilities\Helper;

$db = Helper::getDatabase();
$query = Helper::getURLQuery();

$headers = [
    "Record ID" => "id",
    "Datetime Deleted" => "deletedAtFormatted",
];

$sortMethod = DashboardTable::getSortMethod($query, $headers);
$searchMethod = DashboardTable::getSearchMethod($query, $headers);

$archives = $db->query(<<<SQL
SELECT * FROM (
    SELECT
        id,
        time::format(
            time.deletedAt,
            "%h %e, %Y - %X"
        ) AS deletedAtFormatted,
        time.deletedAt AS createdAt
    FROM order, employee, person, product, reception
    WHERE time.deletedAt IS NOT NONE
    $sortMethod
) $searchMethod;
SQL);

Helper::debug($archives);
?>

<div class="flex">
    <?php Sidebar::render(); ?>
    <div class="dashboard-container">
        <div class="dashboard-header">
            <div class="flex items-center gap-2">
                <span class="material-symbols-rounded text-4xl">archive</span>
                <h1 class="text-3xl font-semibold">Archives</h1>
            </div>
        </div>
        <div class="dashboard-content">
            <?php
            DashboardTable::render(
                $archives,
                ["Record ID", "Datetime Deleted", ""],
                function ($archive) {
                    return [
                        <<<HTML
                        <span class="text-highlight font-bold capitalize">{$archive["id"]}</span>
                        HTML,
                        $archive["deletedAtFormatted"],
                        <<<HTML
                        <form action="/dashboard/archives/handler" method="POST" class="flex justify-end" >
                            <button type="submit" name="restore" value="{$archive['id']}" class="button-primary p-1 rounded-full leading-[0]">
                                <span class="material-symbols-rounded">settings_backup_restore</span>
                            </button>
                        </form>
                        HTML,
                    ];
                },
                allowSort: fn ($column) => match ($column) {
                    "" => false,
                    default => true,
                },
                allowSearch: fn ($column) => match ($column) {
                    "" => false,
                    default => true,
                },
            );
            ?>
        </div>
    </div>
</div>
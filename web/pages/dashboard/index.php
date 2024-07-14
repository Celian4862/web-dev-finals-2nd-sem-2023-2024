<?php

use Components\Sidebar;
use Components\DashboardTable;
use Utilities\Helper;

$db = Helper::getDatabase();

$query = Helper::getURLQuery();

$results = $db->query(<<<SQL
RETURN {
    employeesCount: array::len(SELECT id FROM employee WHERE time.deletedAt IS NONE),
    personsCount: array::len(SELECT id FROM person WHERE time.deletedAt IS NONE),
    inventoryCount: array::len(SELECT id FROM product WHERE time.deletedAt IS NONE),
    ordersCount: array::len(SELECT id FROM order WHERE time.deletedAt IS NONE),
    receptionsCount: array::len(SELECT id FROM reception WHERE time.deletedAt IS NONE),

    receptions: (
        SELECT * FROM (
            SELECT
                id,
                distributor.person as distributor.id,
                distributor.person.name AS distributor.name,
                delivery.dateShipped AS dateShipped,
                time.createdAt AS createdAt,
                (
                    SELECT
                        out as id,
                        out.name AS name,
                        time.createdAt
                    FROM ONLY delivery->deliveryStatusLine
                    WHERE time.deletedAt IS NONE
                    ORDER BY time.createdAt DESC
                    LIMIT 1
                ) AS status
            FROM reception
            WHERE time.deletedAt IS NONE
            LIMIT 10
        ) WHERE status.id = deliveryStatus:2
    ),

    products: (
        SELECT
            id,
            label,
            description,
            desiredStocks,
            array::len((
                SELECT
                    id,
                    status.name AS status
                FROM ->stock
                WHERE
                    time.deletedAt IS NONE AND
                    status = stockStatus:0
            )) AS physicalStocks,
            time.createdAt AS createdAt
        FROM product
        WHERE time.deletedAt IS NONE
        LIMIT 10
    ),
}
SQL);

$cards = [
    [
        "title" => "Employees",
        "icon" => "person",
        "link" => "/employees",
        "description" => $results["employeesCount"],
    ],
    [
        "title" => "Persons",
        "icon" => "group",
        "link" => "/persons",
        "description" => $results["personsCount"],
    ],
    [
        "title" => "Inventory",
        "icon" => "inventory_2",
        "link" => "/inventory",
        "description" => $results["inventoryCount"],
    ],
    [
        "title" => "Orders",
        "icon" => "list_alt",
        "link" => "/orders",
        "description" => $results["ordersCount"],
    ],
    [
        "title" => "Receptions",
        "icon" => "orders",
        "link" => "/receptions",
        "description" => $results["receptionsCount"],
    ]
]
?>

<div class="flex">
    <?php Sidebar::render(); ?>

    <div class="dashboard-container">
        <div class="dashboard-header">
            <div class="flex items-center gap-2">
                <span class="material-symbols-rounded text-4xl">space_dashboard</span>
                <h1 class="text-3xl font-semibold">Dashboard</h1>
            </div>
        </div>
        <div class="dashboard-content flex flex-col gap-4">
            <div class="flex justify-between gap-8">
                <?php foreach ($cards as $card) : ?>
                    <a href="/dashboard<?= $card["link"]; ?>" class="relative flex flex-col w-full p-4 rounded-lg bg-[#0086B2] ring-[#0086B280] text-white shadow-lg hover:bg-[#037CA3] hover:ring focus:bg-[#037CA3] focus:ring transition-colors">
                        <span class="material-symbols-rounded absolute right-0 bottom-0 mr-2 text-6xl text-[#0C6787]"><?= $card["icon"]; ?></span>
                        <span class="text-2xl font-bold z-10"><?= $card["description"]; ?></span>
                        <span class="mb-6 z-10"><?= $card["title"]; ?></span>
                    </a>
                <?php endforeach; ?>
            </div>
            <div>
                <div class="flex items-center gap-2">
                    <span class="material-symbols-rounded text-3xl">table_rows_narrow</span>
                    <h2 class="text-2xl">Recent Arrivals</h2>
                </div>
            </div>
            <?php
            DashboardTable::render(
                $results["receptions"],
                ["Tracking ID", "Distributor", "Date Shipped", "Status"],
                function ($reception) use ($query) {
                    $id = $reception["id"];
                    $infoQuery = Helper::getURLPathQuery("dashboard/receptions", array_merge($query, ["info" => $id]));
                    $distributorQuery = Helper::getURLPathQuery("/dashboard/persons", array_merge($query, ["info" => $reception["distributor"]["id"]]));
                    $status = match ($reception["status"]["name"] ?? "") {
                        "" => "",
                        "Delivered" => <<<HTML
                        <span class="px-2 py-1 rounded-full bg-green-500 text-white font-semibold">{$reception["status"]["name"]}</span>
                        HTML,
                        default => <<<HTML
                        <span class="px-2 py-1 rounded-full bg-gray-400 text-white font-semibold">{$reception["status"]["name"]}</span>
                        HTML
                    };

                    return [
                        <<<HTML
                        <a href="{$infoQuery}" class="dashboard-table-id">$id</a>
                        HTML,
                        <<<HTML
                        <a href="{$distributorQuery}" class="dashboard-table-id">{$reception["distributor"]["name"]}</a>
                        HTML,
                        date_create($reception["dateShipped"])->format("M, d, Y"),
                        $status,
                    ];
                },
                allowSort: fn () => false,
                allowSearch: fn () => false,
                headerStyle: fn ($column) => match ($column) {
                    "Date Shipped", "Status" => "align-items: center",
                    default => ""
                },
                rowStyle: fn ($column) => match ($column) {
                    "Date Shipped", "Status" => "text-align: center;",
                    default => "",
                }
            );
            ?>
            <div>
                <div class="flex items-center gap-2">
                    <span class="material-symbols-rounded text-3xl">table_rows_narrow</span>
                    <h2 class="text-2xl">Recently Added Products</h2>
                </div>
            </div>
            <?php
            DashboardTable::render(
                $results["products"],
                ["Product ID", "Label", "Description", "Desired Stocks", "Physical Stocks"],
                function ($product) {
                    $id = $product["id"];
                    $infoQuery = Helper::getURLPathQuery("/dashboard/inventory", query: ["info" => $id]);

                    return [
                        <<<HTML
                        <a href="{$infoQuery}" class="dashboard-table-id">$id</a>
                        HTML,
                        $product["label"],
                        $product["description"],
                        $product["desiredStocks"],
                        $product["physicalStocks"]
                    ];
                },
                allowSort: fn () => false,
                allowSearch: fn () => false,
                headerStyle: fn ($column) => match ($column) {
                    "Physical Stocks", "Desired Stocks" => "align-items: center;",
                    default => ""
                },
                rowStyle: fn ($column) => match ($column) {
                    "Physical Stocks", "Desired Stocks" => "text-align: center;",
                    default => ""
                }
            );
            ?>
        </div>
    </div>
</div>
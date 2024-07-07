<?php

session_start();

use Components\Sidebar;
use Components\DashboardTable;
use Utilities\Helper;

$db = Helper::getDatabase();

$products = $db->query(<<<SQL
    SELECT
        id,
        label,
        description,
        count(->stocks.in) AS stocks,
        time.createdAt as createdAt
    FROM product
    ORDER BY createdAt DESC
    LIMIT 10;
SQL);

$cards = [
    [
        "title" => "Employees",
        "icon" => "person",
        "link" => "/employees",
        "description" => $db->query("RETURN { count: array::len(SELECT id FROM employee where time.deletedAt = none) }")["count"],
    ],
    [
        "title" => "Client",
        "icon" => "group",
        "link" => "/client",
        "description" => $db->query("RETURN { count: array::len(SELECT id FROM client) }")["count"],
    ],
    [
        "title" => "Distributor",
        "icon" => "warehouse",
        "link" => "/distributors",
        "description" => $db->query("RETURN { count: array::len(SELECT id FROM distributor) }")["count"],
    ],
    [
        "title" => "Inventory",
        "icon" => "inventory_2",
        "link" => "/inventory",
        "description" => $db->query("RETURN { count: array::len(SELECT id FROM physicalProduct) }")["count"],
    ],
    [
        "title" => "Shipments",
        "icon" => "local_shipping",
        "link" => "/shipments",
        "description" => $db->query("RETURN { count: array::len(SELECT id FROM shipment) }")["count"],
    ],
    [
        "title" => "Receptions",
        "icon" => "orders",
        "link" => "/receptions",
        "description" => $db->query("RETURN { count: array::len(SELECT id FROM reception) }")["count"],
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
        <div class="dashboard-content flex flex-col gap-8">
            <div class="flex justify-between gap-8">
                <?php foreach ($cards as $card) : ?>
                    <a href="/dashboard<?= $card["link"]; ?>" class="relative flex flex-col w-full p-4 rounded-lg bg-[#0086B2] ring-[#0086B280] text-white shadow-xl hover:bg-[#037CA3] hover:ring focus:bg-[#037CA3] focus:ring transition-colors">
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
                $products,
                ["Product ID", "Label", "Description", "Stock"],
                elementData: function ($product) {
                    return [
                        $product["id"],
                        $product["label"],
                        $product["description"],
                        $product["stocks"]
                    ];
                },
                search: false,
                headerStyle: fn ($column) => match ($column) {
                    "Stock" => "text-align: center;",
                    default => ""
                },
                rowStyle: fn ($column) => match ($column) {
                    "Stock" => "text-align: center;",
                    default => ""
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
                $products,
                ["Product ID", "Label", "Description", "Stock"],
                elementData: function ($product) {
                    return [
                        $product["id"],
                        $product["label"],
                        $product["description"],
                        $product["stocks"]
                    ];
                },
                search: false,
                headerStyle: fn ($column) => match ($column) {
                    "Stock" => "text-align: center;",
                    default => ""
                },
                rowStyle: fn ($column) => match ($column) {
                    "Stock" => "text-align: center;",
                    default => ""
                }
            );
            ?>
        </div>
    </div>
</div>
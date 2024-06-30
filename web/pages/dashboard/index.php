<?php

session_start();

use Components\Sidebar;
use function Utilities\getDatabase;

$db = getDatabase();

$cards = [
    [
        "title" => "Employees",
        "icon" => "person",
        "link" => "/employees",
        "description" => $db->query("RETURN { count: array::len(SELECT id FROM employee) }")["count"],
    ],
    [
        "title" => "Inventory",
        "icon" => "inventory_2",
        "link" => "/inventory",
        "description" => $db->query("RETURN { count: array::len(SELECT id FROM product) }")["count"],
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
                <i class="material-symbols-rounded text-4xl">space_dashboard</i>
                <h1 class="text-3xl font-semibold">Dashboard</h1>
            </div>
        </div>
        <div class="dashboard-content">
            <div class="flex justify-between gap-8 mb-8">
                <?php foreach ($cards as $card) : ?>
                    <a href="/dashboard<?= $card["link"]; ?>" class="relative flex flex-col w-full p-4 rounded-lg outline-none bg-[#0086B2] ring-[#0086B280] text-white shadow-xl hover:bg-[#037CA3] hover:ring focus:bg-[#037CA3] focus:ring transition-colors">
                        <i class="material-symbols-rounded absolute right-0 bottom-0 mr-2 text-6xl text-[#0C6787]"><?= $card["icon"]; ?></i>
                        <span class="text-2xl font-bold z-10"><?= $card["description"]; ?></span>
                        <span class="mb-6 z-10"><?= $card["title"]; ?></span>
                    </a>
                <?php endforeach; ?>
            </div>
            <div>
                <div class="flex items-center gap-2">
                    <i class="material-symbols-rounded text-3xl">table_rows_narrow</i>
                    <h2 class="text-2xl">Recent Arrivals</h2>
                </div>
            </div>
            <div>
                <div class="flex items-center gap-2">
                    <i class="material-symbols-rounded text-3xl">table_rows_narrow</i>
                    <h2 class="text-2xl">Recently Added Products</h2>
                </div>
            </div>
        </div>
    </div>
</div>
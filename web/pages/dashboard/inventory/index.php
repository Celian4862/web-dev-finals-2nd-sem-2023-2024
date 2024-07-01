<?php

session_start();

use Components\Sidebar;
use Components\DashboardTable;

use function Utilities\getDatabase;

$db = getDatabase();

$products = $db->query("SELECT id, label, description, array::len(->individualProduct.out) as stock FROM product");
?>

<div class="flex">
    <?php Sidebar::render(); ?>
    <div class="dashboard-container">
        <div class="dashboard-header">
            <div class="flex items-center gap-2">
                <i class="material-symbols-rounded text-4xl">inventory_2</i>
                <h1 class="text-3xl font-semibold">Inventory</h1>
            </div>
        </div>
        <div class="dashboard-content">
            <?php
            DashboardTable::render(
                $products,
                ["Product ID", "Label", "Description", "Stock"],
                function ($product) {
                    return [
                        $product["id"],
                        $product["label"],
                        $product["description"],
                        $product["stock"]
                    ];
                },
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
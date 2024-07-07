<?php

session_start();

use Components\Sidebar;
use Components\DashboardTable;
use Utilities\Helper;

$db = Helper::getDatabase();

$products = $db->query("SELECT id, label, description, count(->stocks.in) AS stocks FROM product;");
?>

<div class="flex">
    <?php Sidebar::render(); ?>
    <div class="dashboard-container">
        <div class="dashboard-header">
            <div class="flex items-center gap-2">
                <span class="material-symbols-rounded text-4xl">inventory_2</span>
                <h1 class="text-3xl font-semibold">Inventory</h1>
            </div>
            <a href="/dashboard/inventory/add-new" class="button-primary">Add Product</a>
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
                        $product["stocks"]
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
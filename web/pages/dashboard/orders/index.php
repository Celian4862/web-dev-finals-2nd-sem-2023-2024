<?php

use Components\Sidebar;
use Components\DashboardTable;
use Utilities\Helper;

$db = Helper::getDatabase();
$query = Helper::getURLQuery();

$headers = [
    "Order ID" => "id",
    "Client" => "client.name",
    "Handled by" => "employee.name",
    "Address" => "address",
    "Status" => "status"
];

$sortMethod = DashboardTable::getSortMethod($query, $headers);
$searchMethod = DashboardTable::getSearchMethod($query, $headers);

$orders = $db->query(<<<SQL
SELECT * FROM (
    SELECT
        id,
        client AS client.id,
        client.name AS client.name,
        employee AS employee.id,
        string::join(
            " ",
            employee.details.fullName.first,
            employee.details.fullName.last
        ) AS employee.name,
        string::join(
            ", ",
            delivery.address.street,
            delivery.address.city,
            delivery.address.province,
            delivery.address.zipCode
        ) AS address
    FROM order
) $searchMethod;
SQL);
?>

<div class="flex">
    <?php Sidebar::render(); ?>
    <div class="dashboard-container">
        <div class="dashboard-header">
            <div class="flex items-center gap-2">
                <span class="material-symbols-rounded text-4xl">list_alt</span>
                <h1 class="text-3xl font-semibold">Orders</h1>
                <span class="text-3xl text-gray-400 font-semibold">(<?= count($orders) ?>)</span>
            </div>
            <a href="/dashboard/orders/add-new" class="button-primary group-button">
                <span>Add Orders</span>
                <span class="material-symbols-rounded">add</span>
            </a>
        </div>
        <div class="dashboard-content">
            <?php
            DashboardTable::render(
                $orders,
                ["Order ID", "Client", "Handled by", "Address", "Status"],
                function ($product) {
                    $id = $product["id"];
                    $infoQuery = Helper::getURLPathQuery(query: ["info" => $id]);
                    $clientQuery = Helper::getURLPathQuery("/dashboard/clients", ["info" => $product["client"]["id"]]);
                    $employeeQuery = Helper::getURLPathQuery("/dashboard/employees", ["info" => $product["employee"]["id"]]);

                    return [
                        <<<HTML
                        <a href="{$infoQuery}" class="dashboard-table-id">$id</a>
                        HTML,
                        <<<HTML
                        <a href="{$clientQuery}" class="dashboard-table-link">
                            {$product["client"]["name"]}
                        </a>
                        HTML,
                        <<<HTML
                        <a href="{$employeeQuery}" class="dashboard-table-link">
                            {$product["employee"]["name"]}
                        </a>
                        HTML,
                    ];
                },
            );
            ?>
        </div>
    </div>
</div>
<?php

session_start();

use Components\Sidebar;
use Components\DashboardTable;

use function Utilities\getDatabase;

$db = getDatabase();

$shipments = $db->query(<<<EOD
SELECT
    string::join(
        " ",
        <-order[0].in.fullName.firstName,
        <-order[0].in.fullName.lastName
    ) as client,
    string::join(
        ", ",
        address.country,
        address.city,
        address.street,
        address.zipCode
    ) as address,
    id,
    dateShipped,
    status.name as status
FROM shipment;
EOD);
?>

<div class="flex">
    <?php Sidebar::render(); ?>
    <div class="dashboard-container">
        <div class="dashboard-header">
            <div class="flex items-center gap-2">
                <i class="material-symbols-rounded text-4xl">local_shipping</i>
                <h1 class="text-3xl font-semibold">Shipments</h1>
            </div>
        </div>
        <div class="dashboard-content">
            <?php
            DashboardTable::render(
                $shipments,
                ["Shipment ID", "Client", "Address", "Status"],
                function ($shipment) {
                    return [
                        $shipment["id"],
                        $shipment["client"],
                        $shipment["address"],
                        $shipment["status"]
                    ];
                },
                headerStyle: fn ($column) => match ($column) {
                    "Status" => "text-align: center;",
                    default => ""
                },
                rowStyle: fn ($column) => match ($column) {
                    "Status" => "text-align: center;",
                    default => ""
                }
            );
            ?>
        </div>
    </div>
</div>
<?php

use Utilities\Helper;

$path = Helper::getURLPath();

$navigations = [
    [
        "title" => "Dashboard",
        "icon" => "space_dashboard",
        "link" => "/dashboard",
        "active" => $path === "/dashboard" || $path === "/dashboard\/",
    ],
    [
        "title" => "Employees",
        "icon" => "person",
        "link" => "/dashboard/employees",
        "active" => preg_match("/dashboard\/employees/i", $path) > 0,
    ],
    [
        "title" => "Clients",
        "icon" => "group",
        "link" => "/dashboard/clients",
        "active" => preg_match("/dashboard\/clients/i", $path) > 0,
    ],
    [
        "title" => "Distributor",
        "icon" => "warehouse",
        "link" => "/dashboard/distributors",
        "active" => preg_match("/dashboard\/distributors/i", $path) > 0,
    ],
    [
        "title" => "Inventory",
        "icon" => "inventory_2",
        "link" => "/dashboard/inventory",
        "active" => preg_match("/dashboard\/inventory/i", $path) > 0,
    ],
    [
        "title" => "Shipments",
        "icon" => "local_shipping",
        "link" => "/dashboard/shipments",
        "active" => preg_match("/dashboard\/shipments/i", $path) > 0,
    ],
    [
        "title" => "Receptions",
        "icon" => "orders",
        "link" => "/dashboard/receptions",
        "active" => preg_match("/dashboard\/receptions/i", $path) > 0,
    ],
];
?>

<div class="flex flex-col justify-between w-full max-w-[300px] h-dvh p-4 bg-primary text-white">
    <div>
        <div class="flex justify-center items-center w-fit mx-auto mb-8 p-6 aspect-square rounded-full bg-white shadow-xl">
            <span class="h-fit select-none text-8xl text-highlight font-logo">N2N</span>
        </div>
        <ul class="mb-auto text-md font-bold">
            <?php foreach ($navigations as $navigation) : ?>
                <li class="mb-2">
                    <a href="<?= $navigation["link"]; ?>" data-active="<?= var_export($navigation["active"]); ?>" class="sidebar-button">
                        <span class="material-symbols-rounded text-3xl"><?= $navigation["icon"]; ?></span>
                        <span><?= $navigation["title"]; ?></span>
                    </a>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>
    <form action="/logout" method="DELETE" class="font-bold">
        <button type="submit" name="_method" value="DELETE" class="sidebar-button float-end">
            <span>Logout</span>
            <span class="material-symbols-rounded text-3xl">logout</span>
        </button>
    </form>
</div>
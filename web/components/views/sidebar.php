<?php

use function Utilities\getURLPath;

$path = getURLPath();
?>

<div class="flex flex-col justify-between w-full max-w-[300px] h-dvh p-4 bg-primary text-white">
    <div>
        <div class="flex justify-center items-center w-fit mx-auto mb-4 p-6 aspect-square rounded-full bg-white shadow-xl">
            <span class="h-fit text-8xl text-logo font-logo">N2N</span>
        </div>
        <ul class="mb-auto text-md font-bold">
            <li class="mb-2">
                <a href="/dashboard" data-active="<?= var_export($path === "/dashboard"); ?>" class="sidebar-button">
                    <i class="material-symbols-rounded text-3xl">space_dashboard</i>
                    <span>Dashboard</span>
                </a>
            </li>
            <li class="mb-2">
                <a href="/dashboard/users" data-active="<?= var_export($path === "/dashboard/users"); ?>" class="sidebar-button">
                    <i class="material-symbols-rounded text-3xl">person</i>
                    <span>Users</span>
                </a>
            </li>
            <li class="mb-2">
                <a href="/dashboard/inventory" data-active="<?= var_export($path === "/dashboard/inventory"); ?>" class="sidebar-button">
                    <i class="material-symbols-rounded text-3xl">inventory_2</i>
                    <span>Inventory</span>
                </a>
            </li>
            <li class="mb-2">
                <a href="/dashboard/shipments" data-active="<?= var_export($path === "/dashboard/shipments"); ?>" class="sidebar-button">
                    <i class="material-symbols-rounded text-3xl">local_shipping</i>
                    <span>Shipments</span>
                </a>
            </li>
            <li class="mb-2">
                <a href="/dashboard/receptions" data-active="<?= var_export($path === "/dashboard/receptions"); ?>" class="sidebar-button">
                    <i class="material-symbols-rounded text-3xl">orders</i>
                    <span>Receptions</span>
                </a>
            </li>
        </ul>
    </div>
    <form action="/logout" method="DELETE" class="font-bold">
        <button type="submit" name="_method" value="DELETE" class="sidebar-button float-end">
            <span>Logout</span>
            <i class="material-symbols-rounded text-3xl">logout</i>
        </button>
    </form>
</div>
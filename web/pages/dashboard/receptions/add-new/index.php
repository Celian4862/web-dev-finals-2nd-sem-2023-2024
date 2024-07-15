<?php

use Components\Sidebar;
use Utilities\Helper;

$db = Helper::getDatabase();

$results = $db->query(<<<SQL
RETURN {
    distributors: (
        SELECT 
            id,
            person.name AS name
        FROM distributor
        WHERE
            time.deletedAt IS NONE AND
            person.time.deletedAt IS NONE
        ORDER BY name ASC
    ),

    deliveries: (SELECT id, name FROM deliveryStatus),

    products: (
        SELECT
            id,
            label
        FROM product
        WHERE time.deletedAt IS NONE
        ORDER BY label ASC
    ),
}
SQL);
?>

<div class="flex">
    <?php Sidebar::render(); ?>
    <div class="dashboard-container">
        <div class="dashboard-header">
            <div class="flex items-center gap-2">
                <div class="flex items-center gap-2">
                    <span class="material-symbols-rounded text-4xl">person</span>
                    <h1 class="text-3xl font-semibold">Add New Reception</h1>
                </div>
            </div>
            <a href="/dashboard/receptions" class="button-primary group-button">
                <span>Back</span>
                <span class="material-symbols-rounded">arrow_back</span>
            </a>
        </div>

        <div class="dashboard-content">
            <form method="POST" action="/dashboard/receptions/add-new/handler" class="bg-white p-12 rounded shadow-md flex flex-col gap-2">
                <h2 class="text-xl font-bold mt-4">Reception Information</h2>
                <hr class="border-gray-300">
                <div class="flex flex-col gap-4">
                    <div class="input-box">
                        <label for="distributor">Distributor</label>
                        <select id="distributor" name="distributor" class="h-full" required>
                            <option value="" disabled selected>Select Distributor</option>
                            <?php foreach ($results["distributors"] as $distributor) : ?>
                                <option value="<?= $distributor["id"]; ?>">
                                    <?= $distributor["name"]; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="input-box">
                        <label for="deliveryDescription">Delivery Description</label>
                        <textarea id="deliveryDescription" name="deliveryDescription" class="min-h-20 max-h-[20rem] h-20"></textarea>
                    </div>
                    <div class="group-input-box">
                        <div class="input-box">
                            <label for="dateShipped">Date Shipped</label>
                            <input type="date" value="<?php echo date('Y-m-d'); ?>" id="dateShipped" name="dateShipped" required />
                        </div>
                        <div class="input-box">
                            <label for="status">Status</label>
                            <select id="status" name="status" class="h-full" required>
                                <option value="" disabled selected>Select Status</option>
                                <?php foreach ($results["deliveries"] as $delivery) : ?>
                                    <option value="<?= $delivery["id"]; ?>">
                                        <?= $delivery["name"]; ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="input-box">
                    <label for="statusDescription">Status Description</label>
                    <textarea id="statusDescription" name="statusDescription" class="min-h-20 max-h-[20rem] h-20"></textarea>
                </div>
                <div class="w-full">
                    <table class="w-full">
                        <thead>
                            <tr>
                                <th class="p-4 text-left">Product ID</th>
                                <th class="p-4 text-left">Label</th>
                                <th clas="p-4">Quantity</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($results["products"] as $product) : ?>
                                <tr class="odd:bg-gray-100 border-y border-gray-400">
                                    <td class="p-2">
                                        <a href="<?= Helper::getURLPathQuery("/dashboard/inventory", ["info" => $product["id"]]) ?>" class="table-id"><?= $product["id"]; ?></a>
                                    </td>
                                    <td class="w-full p-2">
                                        <label for="product-<?= $product["id"]; ?>" class="block w-full">
                                            <?= $product["label"]; ?>
                                        </label>
                                    </td>
                                    <td class="p-2 border-l border-dashed border-gray-300">
                                        <input type="number" min="0" id="product-<?= $product["id"]; ?>" name="products[<?= $product["id"]; ?>]" class="input-box-sm" />
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <div class="flex justify-end m">
                    <button type="submit" class="button-success mt-3">Add Reception</button>
                </div>
            </form>
        </div>
    </div>
</div>
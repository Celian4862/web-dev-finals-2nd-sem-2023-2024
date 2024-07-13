<?php

use Components\Sidebar;
use Utilities\Helper;

$db = Helper::getDatabase();
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
                            <?php
                            $distributors = $db->query(<<<SQL
                            SELECT 
                                id,
                                person.name AS name
                            FROM distributor
                            WHERE
                                time.deletedAt IS NONE AND
                                person.time.deletedAt IS NONE
                            ORDER BY name ASC
                            SQL);
                            ?>
                            <option value="" disabled selected>Select Distributor</option>
                            <?php foreach ($distributors as $distributor) : ?>
                                <option value="<?php echo htmlspecialchars($distributor["id"]); ?>">
                                    <?php echo htmlspecialchars($distributor["name"]); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="input-box">
                        <label for="deliveryDescription">Delivery Description</label>
                        <textarea id="deliveryDescription" name="deliveryDescription" class="min-h-20 max-h-[20rem] h-20" required></textarea>
                    </div>
                    <div class="group-input-box">
                        <div class="input-box">
                            <label for="dateShipped">Date Shipped</label>
                            <input type="date" id="dateShipped" name="dateShipped" required />
                        </div>
                        <div class="input-box">
                            <label for="status">Status</label>
                            <select id="status" name="status" class="h-full" required>
                                <option value="" disabled selected>Select Status</option>
                                <?php $deliveries = $db->query("SELECT id, name FROM deliveryStatus"); ?>
                                <?php foreach ($deliveries as $delivery) : ?>
                                    <option value="<?php echo htmlspecialchars($delivery["id"]); ?>">
                                        <?php echo htmlspecialchars($delivery["name"]); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="input-box">
                        <label for="statusDescription">Status Description</label>
                        <textarea id="statusDescription" name="statusDescription" class="min-h-20 max-h-[20rem] h-20" required></textarea>
                    </div>
                    <div class="mx-auto w-full">
                        <table class="w-full">
                            <thead>
                                <tr>
                                    <th class="p-4 text-left">Product</th>
                                    <th clas="p-4">Quantity</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $products = $db->query(<<<SQL
                                SELECT
                                    id,
                                    label
                                FROM product
                                WHERE time.deletedAt IS NONE
                                ORDER BY label ASC
                                SQL);
                                ?>
                                <?php foreach ($products as $product) : ?>
                                    <tr class="odd:bg-gray-100 border-y border-gray-400">
                                        <td class="w-full p-2">
                                            <label for="product-<?php echo htmlspecialchars($product["id"]); ?>" class="block w-full">
                                                <?php echo htmlspecialchars($product["label"]); ?>
                                            </label>
                                        </td>
                                        <td class="p-2 border-l border-dashed border-gray-300">
                                            <input type="number" min="0" id="product-<?php echo htmlspecialchars($product["id"]); ?>" name="products[<?php echo htmlspecialchars($product["id"]); ?>]" class="input-box-sm" />
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="flex justify-end m">
                    <button type="submit" class="button-success mt-3">Add Reception</button>
                </div>
            </form>
        </div>
    </div>
</div>
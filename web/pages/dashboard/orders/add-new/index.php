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
                    <h1 class="text-3xl font-semibold">Add New Product</h1>
                </div>
            </div>
            <a href="/dashboard/inventory" class="button-primary group-button">
                <span>Back</span>
                <span class="material-symbols-rounded">arrow_back</span>
            </a>
        </div>
        <div class="dashboard-content">
            <form action="/dashboard/inventory/add-new/handler" method="POST" class="bg-white p-4 rounded shadow-md">
                <h2 class="text-xl font-bold mt-4">Order Information</h2>
                <hr class="my-4 border-gray-300">
                <div class="flex flex-col gap-4">
                    <div class="input-box">
                        <label for="label">Client</label>
                        <?php
                        $clients = $db->query(<<<SQL
                        SELECT
                            person.id AS id,
                            person.name AS name
                        FROM client
                        WHERE
                            time.deletedAt IS NONE AND
                            person.time.deletedAt IS NONE
                        SQL);
                        ?>
                        <select name="client" id="client" required>
                            <option value="">Select Client</option>
                            <?php foreach ($clients as $client) : ?>
                                <option value="<?= $client['id'] ?>"><?= $client['name'] ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="group-input-box">
                        <div class="input-box">
                            <label for="buyingPrice">Buying Price</label>
                            <input type="number" value="0" min="0" id="buyingPrice" name="buyingPrice"></input>
                        </div>
                        <div class="input-box">
                            <label for="sellingPrice">Selling Price</label>
                            <input type="number" value="0" min="0" id="sellingPrice" name="sellingPrice"></input>
                        </div>
                    </div>
                    <div class="group-input-box">
                        <div class="input-box">
                            <label for="desiredStocks">Desired Stocks</label>
                            <input type="number" min="0" value="0" id="desiredStocks" name="desiredStocks"></input>
                        </div>
                        <div class="input-box">
                            <label for="physicalStocks">Physical Stocks</label>
                            <input type="number" min="0" value="0" id="physicalStocks" name="physicalStocks"></input>
                        </div>
                    </div>
                    <div class="input-box">
                        <label for="description">Description</label>
                        <textarea name="description" id="description" class=""></textarea>
                    </div>
                </div>
                <div class="flex justify-end">
                    <button type="submit" class="button-success mt-4">Add Product</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php

use Components\Sidebar;
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
                <h2 class="text-xl font-bold mt-4">Product Information</h2>
                <hr class="my-4 border-gray-300">
                <div class="flex flex-col gap-4">
                    <div class="input-box">
                        <label for="label">Product Name</label>
                        <input type="text" id="label" name="label"></input>
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
</div>
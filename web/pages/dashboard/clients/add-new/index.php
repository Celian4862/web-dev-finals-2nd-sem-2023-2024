<?php

session_start();

use Components\Sidebar;
use Utilities\Helper;

$query = Helper::getURLQuery();
?>

<div class="flex">
    <?php Sidebar::render(); ?>
    <div class="dashboard-container">
        <div class="dashboard-header">
            <div class="flex items-center gap-2 justify-between">
                <div class="flex items-center gap-2">
                    <span class="material-symbols-rounded text-4xl">person</span>
                    <h1 class="text-3xl font-semibold">Add New Client</h1>
                </div>
            </div>
            <a href="/dashboard/clients" class="flex justify-end button-primary">Back to list</a>
        </div>

        <div class="dashboard-content">
            <form action="/dashboard/clients/add-new/handler" method="POST" class="bg-white p-12 rounded shadow-md flex flex-col gap-2">
                <h2 class="text-xl font-bold mt-4">Information</h2>
                <hr class="border-gray-300">
                <div class="input-box">
                    <label for="name">Client Name</label>
                    <input type="text" id="name" name="name" required autofocus></input>
                </div>
                <div class="group-input-box">
                    <div class="input-box">
                        <label for="email">Contact Email</label>
                        <input type="email" id="email" name="contact[email]" placeholder="Optional"></input>
                    </div>
                    <div class="input-box">
                        <label for="phone">Contact Number</label>
                        <input type="text" id="phone" name="contact[phone]" placeholder="Optional"></input>
                    </div>
                </div>
                <?php if (isset($query["addAddress"])) : ?>
                    <div class="flex justify-between items-center mt-8">
                        <h2 class="text-xl font-bold">Address</h2>
                        <a href="<?= Helper::getURLPathQuery(query: Helper::arrayToggle($query, "addAddress")); ?>" class="button-danger group-button">
                            <span>Cancel</span>
                            <span class="material-symbols-rounded">close</span>
                        </a>
                    </div>
                    <hr class="border-gray-300">
                    <div class="group-input-box">
                        <div class="input-box">
                            <label for="address[country]">Country</label>
                            <input type="text" id="address[country]" name="address[country]" required></input>
                        </div>
                        <div class="input-box">
                            <label for="address[city]">City</label>
                            <input type="text" id="address[city]" name="address[city]" required></input>
                        </div>
                    </div>
                    <div class="group-input-box">
                        <div class="input-box">
                            <label for="address[street]">Street</label>
                            <input type="text" id="address[street]" name="address[street]" required></input>
                        </div>
                        <div class="input-box w-24 flex-grow-0">
                            <label for="address[zipCode]">Zip Code</label>
                            <input type="text" id="address[zipCode]" name="address[zipCode]" required></input>
                        </div>
                    </div>
                <?php else : ?>
                    <a href="<?= Helper::getURLPathQuery(query: Helper::arrayToggle($query, "addAddress")); ?>" class="flex justify-center items-center mt-8 rounded-2xl border-2 border-dashed h-12 font-bold text-gray-400 border-gray-400 ring-gray-200 hover:bg-gray-100 hover:ring focus:bg-gray-100 focus:ring transition-colors">Add Address</a>
                <?php endif; ?>
                <div class="flex justify-end m">
                    <button type="submit" class="button-success mt-3">Add Client</button>
                </div>
                </from>
        </div>
    </div>
</div>
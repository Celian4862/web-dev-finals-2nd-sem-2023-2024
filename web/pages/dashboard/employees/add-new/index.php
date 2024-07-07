<?php

session_start();

use Components\Sidebar;
?>

<div class="flex">
    <?php Sidebar::render(); ?>
    <div class="dashboard-container">
        <div class="dashboard-header">
            <div class="flex items-center gap-2 justify-between">
                <div class="flex items-center gap-2">
                    <span class="material-symbols-rounded text-4xl">person</span>
                    <h1 class="text-3xl font-semibold">Add New Employee</h1>
                </div>
            </div>
            <a href="/dashboard/employees" class="button-primary ">
                <span>Back</span>
                <span class="material-symbols-rounded">arrow_back</span>
            </a>
        </div>

        <div class="dashboard-content">
            <form action="/dashboard/employees/add-new/handler" method="POST" class="bg-white p-12 rounded shadow-md">
                <h2 class="text-xl font-bold mt-4">Employee Information</h2>
                <hr class="my-4 border-gray-300">
                <div class="flex flex-col lg:flex-row">
                    <div class="w-1/2 mr-4">
                        <div class="input-box mb-2">
                            <label for="firstName">First Name:</label>
                            <input type="text" id="firstName" name="details[fullName][first]" required />
                        </div>
                        <div class="input-box mb-2">
                            <label for="lastName">Last Name:</label>
                            <input type="text" id="lastName" name="details[fullName][last]" required />
                        </div>
                        <div class="input-box mb-2">
                            <label for="contactPhone">Contact Number:</label>
                            <input type="text" id="contactPhone" name="details[contact][phone]" placeholder="Optional" />
                        </div>
                    </div>
                    <div class="w-1/2">
                        <div class="input-box mb-2">
                            <label for="middleName">Middle Name:</label>
                            <input type="text" id="middleName" name="details[fullName][middle]" placeholder="Optional" />
                        </div>
                        <div class="input-box mb-2">
                            <label for="suffix">Suffix:</label>
                            <input type="text" id="suffix" name="details[fullName][suffix]" placeholder="Optional" />
                        </div>
                        <div class="input-box mb-2">
                            <label for="contactEmail">Contact Email:</label>
                            <input type="email" id="contactEmail" name="details[contact][email]" placeholder="Optional" />
                        </div>
                    </div>
                </div>
                <h2 class="text-xl font-bold mt-8">Address</h2>
                <hr class="my-4 border-gray-300">
                <div class="flex flex-col lg:flex-row">
                    <div class="w-1/2 mr-4">
                        <div class="input-box mb-2">
                            <label for="country">Country:</label>
                            <input type="text" id="country" name="address[country]" required />
                        </div>
                        <div class="input-box mb-2">
                            <label for="street">Street:</label>
                            <input type="text" id="street" name="address[street]" required />
                        </div>
                    </div>
                    <div class="w-1/2">
                        <div class="input-box mb-2">
                            <label for="city">City:</label>
                            <input type="text" id="city" name="address[city]" required />
                        </div>
                        <div class="input-box mb-2">
                            <label for="zipCode">Zip Code:</label>
                            <input type="text" id="zipCode" name="address[zipCode]" required />
                        </div>
                    </div>
                </div>
                <h2 class="text-xl font-bold mt-8">Login Credentials</h2>
                <hr class="my-4 border-gray-300">
                <div class="flex flex-col lg:flex-row">
                    <div class="w-1/2 mr-4">
                        <div class="input-box mb-2">
                            <label for="email">Email:</label>
                            <input type="email" id="email" name="email" required />
                        </div>
                    </div>
                    <div class="w-1/2">
                        <div class="input-box mb-2">
                            <label for="password">Password:</label>
                            <input type="password" id="password" name="password" required />
                        </div>
                    </div>
                </div>
                <div class="flex justify-end">
                    <button type="submit" class="button-success mt-3">Submit</button>
                </div>
            </form>
        </div>
    </div>
</div>
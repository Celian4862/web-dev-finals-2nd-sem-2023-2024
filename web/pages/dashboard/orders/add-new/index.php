<?php

use Components\Sidebar;
use Utilities\Helper;

$db = Helper::getDatabase();

$results = $db->query(<<<SQL
RETURN {
    clients: (
        SELECT
            id,
            person.name AS name
        FROM client
        WHERE
            time.deletedAt IS NONE AND
            person.time.deletedAt IS NONE
    ),

    employees: (
        SELECT
            id,
            string::join(
                " ",
                details.fullName.first,
                details.fullName.last
            ) AS name
        FROM employee
        WHERE time.deletedAt IS NONE
        ORDER BY name ASC
    ),

    orderStatus: (SELECT id, name FROM orderStatus),

    products: (
        SELECT * FROM (
            SELECT
                id,
                label,
                sellingPrice AS price,
                array::len((
                    SELECT
                        id,
                        status.name AS status
                    FROM ->stock
                    WHERE
                        time.deletedAt IS NONE AND
                        status = stockStatus:0
                )) AS stocks
            FROM product
            WHERE time.deletedAt IS NONE
            ORDER BY label ASC
        ) WHERE stocks > 0
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
                    <h1 class="text-3xl font-semibold">Add New Order</h1>
                </div>
            </div>
            <a href="/dashboard/orders" class="button-primary group-button">
                <span>Back</span>
                <span class="material-symbols-rounded">arrow_back</span>
            </a>
        </div>
        <div class="dashboard-content">
            <form action="/dashboard/orders/add-new/handler" method="POST" class="bg-white p-4 rounded shadow-md">
                <h2 class="text-xl font-bold mt-4">Order Information</h2>
                <hr class="my-4 border-gray-300">
                <div class="flex flex-col gap-4">
                    <div class="group-input-box">
                        <div class="input-box">
                            <label for="client">Client</label>
                            <select name="client" id="client" required>
                                <option value="" disabled selected>Select Client</option>
                                <?php foreach ($results["clients"] as $client) : ?>
                                    <option value="<?= $client["id"] ?>"><?= $client["name"] ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="input-box">
                            <label for="employee">Handled by</label>
                            <select name="employee" id="employee" required>
                                <?php foreach ($results["employees"] as $employee) : ?>
                                    <?php if ($_SESSION["employee"] === $employee["id"]) : ?>
                                        <option value="<?= $employee["id"] ?>" selected><?= $employee["name"] ?></option>
                                    <?php else : ?>
                                        <option value="<?= $employee["id"] ?>"><?= $employee["name"] ?></option>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="group-input-box">
                        <div class="input-box">
                            <label for="dateOrdered">Date Delivery</label>
                            <input type="date" value="<?php echo date('Y-m-d'); ?>" id="dateOrdered" name="dateOrdered" required />
                        </div>
                        <div class="input-box">
                            <label for="orderStatus">Order Status</label>
                            <select id="orderStatus" name="orderStatus" required>
                                <option value="" disabled selected>Select Order Status</option>
                                <?php foreach ($results["orderStatus"] as $status) : ?>
                                    <option value="<?= $status["id"]; ?>">
                                        <?= $status["name"]; ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="input-box">
                        <label for="description">Order Description</label>
                        <textarea name="description" id="description" class="min-h-20 max-h-[20rem] h-20"></textarea>
                    </div>
                </div>
                <h2 class="text-xl font-bold mt-4">Address</h2>
                <hr class="my-4 border-gray-300">
                <div class="flex flex-col gap-4">
                    <div class="group-input-box">
                        <div class="input-box">
                            <label for="country">Country</label>
                            <input type="text" id="country" name="address[country]" required />
                        </div>
                        <div class="input-box">
                            <label for="city">City</label>
                            <input type="text" id="city" name="address[city]" required />
                        </div>
                    </div>
                    <div class="group-input-box">
                        <div class="input-box">
                            <label for="street">Street</label>
                            <input type="text" id="street" name="address[street]" required />
                        </div>
                        <div class="input-box">
                            <label for="zipCode">Zip Code</label>
                            <input type="text" id="zipCode" name="address[zipCode]" required />
                        </div>
                    </div>
                </div>
                <div class="flex flex-col gap-4">
                    <h2 class="text-xl font-bold mt-4">
                        <span>Products</span>
                        <span class="text-gray-500 font-bold">(<?= count($results["products"]); ?>)</span>
                    </h2>
                    <hr class="my-4 border-gray-300">
                    <div class="w-full">
                        <table class="w-full">
                            <thead>
                                <tr>
                                    <th class="p-4 text-left">Product ID</th>
                                    <th class="p-4 text-left">Label</th>
                                    <th class="p-4 text-center">Price</th>
                                    <th class="p-4 text-center">Stocks</th>
                                    <th clas="p-4">Quantity</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($results["products"] as $product) : ?>
                                    <tr class="odd:bg-gray-100 border-y border-gray-400">
                                        <td class="p-2">
                                            <a href="<?= Helper::getURLPathQuery("/dashboard/inventory", ["info" => $product["id"]]) ?>" class="table-id"><?= $product["id"]; ?></a>
                                        </td>
                                        <td class="p-2 w-full">
                                            <label for="product-<?= $product["id"]; ?>" class="block w-full">
                                                <?= $product["label"]; ?>
                                            </label>
                                        </td>
                                        <td class="p-2 text-center text-nowrap">
                                            <span class="font-semibold">&#8369</span>
                                            <span><?= number_format((float)$product["price"], 2, '.', ','); ?></span>
                                        </td>
                                        <td class="p-2 text-center"><?= $product["stocks"] ?></td>
                                        <td class="p-2 border-l border-dashed border-gray-300 w-min">
                                            <input type="number" min="0" max="<?= $product["stocks"]; ?>" id="product-<?= $product["id"]; ?>" name="products[<?= $product["id"]; ?>]" class="input-box-sm w-40" />
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="flex justify-end">
                    <button type="submit" class="button-success mt-4">Add Order</button>
                </div>
            </form>
        </div>
    </div>
</div>
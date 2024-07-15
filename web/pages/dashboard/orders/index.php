<?php

use Components\Sidebar;
use Components\DashboardTable;
use Utilities\Helper;

$db = Helper::getDatabase();
$query = Helper::getURLQuery();

$headers = [
    "Order ID" => "id",
    "Client" => "client.name",
    "Handled by" => "employee.name",
    "Address" => "address",
    "Amount" => "amount",
    "Status" => "status",
];

$sortMethod = DashboardTable::getSortMethod($query, $headers);
$searchMethod = DashboardTable::getSearchMethod($query, $headers);

$sqlInfo = "";

if (isset($query["info"])) {
    $id = $query["info"];

    $sqlInfo = <<<SQL
    order: (
        SELECT
            id,
            client AS client.id,
            client.person.name AS client.name,
            employee AS employee.id,
            description,
            dateOrdered,
            status,
            string::join(
                " ",
                employee.details.fullName.first,
                employee.details.fullName.last
            ) AS employee.name,
            delivery.address AS address,
            (
                SELECT
                    out as id,
                    out.label AS label,
                    amount,
                    quantity
                FROM ->productLine
                WHERE amount IS NOT NONE
            ) AS products
        FROM ONLY $id
        WHERE time.deletedAt IS NONE
        FETCH status, address
    ),

    selections: {
        clients: (
            SELECT id, person.name AS name
            FROM client
            WHERE
                time.deletedAt IS NONE AND
                person.time.deletedAt IS NONE
            ORDER BY name
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
            ORDER BY name
        ),
        orderStatus: (SELECT id, name FROM orderStatus),
    },
    SQL;
}

$results = $db->query(<<<SQL
RETURN {
    orders: (
        SELECT * FROM (
            SELECT
                id,
                client.person AS client.id,
                client.person.name AS client.name,
                employee AS employee.id,
                string::join(
                    " ",
                    employee.details.fullName.first,
                    employee.details.fullName.last
                ) AS employee.name,
                string::join(
                    ", ",
                    delivery.address.country,
                    delivery.address.city,
                    delivery.address.street,
                    delivery.address.zipCode
                ) AS address,
                math::sum((
                    SELECT VALUE amount
                    FROM ->productLine
                    WHERE amount IS NOT NONE
                )) AS amount,
                status.name AS status,
                time.createdAt as createdAt
            FROM order
            WHERE time.deletedAt IS NONE
            $sortMethod
        ) $searchMethod
    ),

    $sqlInfo
}
SQL);
?>

<div class="flex">
    <?php Sidebar::render(); ?>
    <div class="dashboard-container">
        <div class="dashboard-header">
            <div class="flex items-center gap-2">
                <span class="material-symbols-rounded text-4xl">list_alt</span>
                <h1 class="text-3xl font-semibold">Orders</h1>
                <span class="text-3xl text-gray-400 font-semibold">(<?= count($results["orders"]) ?>)</span>
            </div>
            <a href="/dashboard/orders/add-new" class="button-primary group-button">
                <span>Add Order</span>
                <span class="material-symbols-rounded">add</span>
            </a>
        </div>
        <div class="dashboard-content">
            <?php
            DashboardTable::render(
                $results["orders"],
                ["Order ID", "Client", "Handled by", "Address", "Amount", "Status"],
                function ($order) {
                    $id = $order["id"];
                    $infoQuery = Helper::getURLPathQuery(query: ["info" => $id]);
                    $clientQuery = Helper::getURLPathQuery("/dashboard/persons", ["info" => $order["client"]["id"]]);
                    $employeeQuery = Helper::getURLPathQuery("/dashboard/employees", ["info" => $order["employee"]["id"]]);

                    $amount = number_format((float)$order["amount"], 2, '.', ',');

                    $status = match ($order["status"] ?? "") {
                        "Pending" => <<<HTML
                        <span class="px-2 py-1 rounded-full bg-gray-400 text-white font-semibold">{$order["status"]}</span>
                        HTML,
                        default => <<<HTML
                        <span class="px-2 py-1 rounded-full bg-green-500 text-white font-semibold">{$order["status"]}</span>
                        HTML
                    };

                    return [
                        <<<HTML
                        <a href="{$infoQuery}" class="dashboard-table-id">$id</a>
                        HTML,
                        <<<HTML
                        <a href="{$clientQuery}" class="dashboard-table-id">
                            {$order["client"]["name"]}
                        </a>
                        HTML,
                        <<<HTML
                        <a href="{$employeeQuery}" class="dashboard-table-id">
                            {$order["employee"]["name"]}
                        </a>
                        HTML,
                        $order["address"],
                        <<<HTML
                        <div>
                            <span class="font-semibold">&#8369</span>
                            <span>{$amount}</span>
                        </div>
                        HTML,
                        $status,
                    ];
                },
                allowSearch: fn () => true,
            );
            ?>
        </div>
    </div>
</div>

<?php if (isset($query["info"])) : ?>
    <?php
    $id = $query["info"];

    $edit = $_SESSION["edit"] ?? [];
    $inputs = $_SESSION["inputs"] ?? [];

    $order = $results["order"];
    $selections = $results["selections"];
    ?>

    <?php if ($order) : ?>
        <div class="dashboard-info">
            <form class="dashboard-info-container" action="/dashboard/orders/handler?<?= http_build_query($query); ?>" method="POST">
                <div class="dashboard-info-header">
                    <h1><?= $id; ?></h1>
                    <button type="button" onclick="ForceSubmitForm(this.form, this)" name="close" value="true" class="button-danger p-1 leading-[0] rounded-full">
                        <span class="material-symbols-rounded">close</span>
                    </button>
                </div>
                <div class="dashboard-info-content">
                    <div class="section">
                        <div class="header">
                            <h2>Information</h2>
                            <button type="button" onclick="ForceSubmitForm(this.form, this)" name="edit" value="information" class="<?= Helper::editButtonClass($edit, "information"); ?> group-button text-sm">
                                <span>Edit</span>
                                <span class="material-symbols-rounded"><?= Helper::editButtonSymbol($edit, "information"); ?></span>
                            </button>
                        </div>
                        <div class="content">
                            <div class="group-input-box">
                                <div class="input-box">
                                    <label for="client">Client</label>
                                    <select name="client" id="client" class="input-box" <?= Helper::inputDisabled($edit, "information"); ?> required>
                                        <?php foreach ($selections["clients"] as $client) : ?>
                                            <option value="<?= $client["id"]; ?>" <?= (($inputs["client"] ?? $order["client"]["id"]) === $client["id"]) ? "selected" : ""; ?>>
                                                <?= $client["name"]; ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="input-box">
                                    <label for="employee">Handled by</label>
                                    <select name="employee" id="employee" class="input-box" <?= Helper::inputDisabled($edit, "information"); ?> required>
                                        <?php foreach ($selections["employees"] as $employee) : ?>
                                            <option value="<?= $employee["id"]; ?>" <?= (($inputs["employee"] ?? $order["employee"]["id"]) === $employee["id"]) ? "selected" : ""; ?>>
                                                <?= $employee["name"]; ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="group-input-box">
                            <div class="input-box">
                                <label for="dateOrdered">Date Ordered</label>
                                <input type="date" name="dateOrdered" id="dateOrdered" class="input-box" value="<?= $inputs["dateOrdered"] ?? date_create($order["dateOrdered"])->format("Y-m-d"); ?>" <?= Helper::inputDisabled($edit, "information"); ?> required>
                            </div>
                            <div class="input-box">
                                <label for="status">Order Status</label>
                                <select name="status" id="status" class="input-box" <?= Helper::inputDisabled($edit, "information"); ?> required>
                                    <?php foreach ($selections["orderStatus"] as $status) : ?>
                                        <option value="<?= $status["id"]; ?>" <?= (($inputs["status"] ?? $order["status"]["id"]) === $status["id"]) ? "selected" : ""; ?>>
                                            <?= $status["name"]; ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="input-box">
                            <label for="description">Description</label>
                            <textarea name="description" id="description" <?= Helper::inputDisabled($edit, "information"); ?> class="min-h-20 max-h-[20rem] h-20"><?= $order["description"] ?? $order["description"]; ?></textarea>
                        </div>
                    </div>
                    <div class="section">
                        <div class="header">
                            <h2>Address</h2>
                            <button type="button" onclick="ForceSubmitForm(this.form, this)" name="edit" value="address" class="<?= Helper::editButtonClass($edit, "address"); ?> group-button text-sm">
                                <span>Edit</span>
                                <span class="material-symbols-rounded"><?= Helper::editButtonSymbol($edit, "address"); ?></span>
                            </button>
                        </div>
                        <div class="content">
                            <div class="group-input-box">
                                <div class="input-box">
                                    <label for="country">Country</label>
                                    <input type="text" name="address[country]" id="country" class="input-box" value="<?= $inputs["address"]["country"] ?? $order["address"]["country"]; ?>" <?= Helper::inputDisabled($edit, "address"); ?> required>
                                </div>
                                <div class="input-box">
                                    <label for="city">City</label>
                                    <input type="text" name="address[city]" id="city" class="input-box" value="<?= $inputs["address"]["city"] ?? $order["address"]["city"]; ?>" <?= Helper::inputDisabled($edit, "address"); ?> required>
                                </div>
                            </div>
                            <div class="group-input-box">
                                <div class="input-box">
                                    <label for="street">Street</label>
                                    <input type="text" name="address[street]" id="street" class="input-box" value="<?= $inputs["address"]["street"] ?? $order["address"]["street"]; ?>" <?= Helper::inputDisabled($edit, "address"); ?> required>
                                </div>
                                <div class="input-box">
                                    <label for="zipCode">Zip Code</label>
                                    <input type="text" name="address[zipCode]" id="zipCode" class="input-box" value="<?= $inputs["address"]["zipCode"] ?? $order["address"]["zipCode"]; ?>" <?= Helper::inputDisabled($edit, "address"); ?> required>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="section">
                        <div class="header">
                            <h2>
                                <span>Products</span>
                                <span class="text-gray-500 font-bold">(<?= count($order["products"]); ?>)</span>
                            </h2>
                        </div>
                        <div class="content">
                            <table>
                                <thead>
                                    <tr>
                                        <th class="text-left p-2">Product ID</th>
                                        <th class="text-left p-2">Label</th>
                                        <th class="p-2">Amount</th>
                                        <th class="p-2">Quantity</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($order["products"] as $product) : ?>
                                        <?php
                                        $totalAmount = ($totalAmount ?? 0) + $product["amount"];
                                        $totalQuantity = ($totalQuantity ?? 0) + $product["quantity"];
                                        ?>
                                        <tr class="odd:bg-gray-100 border-y border-gray-400">
                                            <td class="p-2">
                                                <a href="/dashboard/inventory?info=<?= $product["id"]; ?>" class="table-id"><?= $product["id"]; ?></a>
                                            </td>
                                            <td class="w-full p-2"><?= $product["label"]; ?></td>
                                            <td class="text-center">
                                                <span class="font-semibold">&#8369</span>
                                                <span><?= number_format((float)$product["amount"], 2, '.', ','); ?></span>
                                            </td>
                                            <td class="text-center"><?= $product["quantity"]; ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                    <tr class="odd:bg-gray-100 border-y border-gray-400">
                                        <td class="p-2 font-bold" colspan="2">Total</td>
                                        <td class="text-center text-nowrap">
                                            <span class="font-semibold">&#8369</span>
                                            <span class="font-bold"><?= number_format((float)$totalAmount, 2, '.', ','); ?></span>
                                        </td>
                                        <td class="text-center font-bold"><?= $totalQuantity; ?></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="dashboard-info-footer">
                    <button type="submit" name="updateOrder" value="<?= $id; ?>" class="button-success group-button">
                        <span>Save</span>
                        <span class="material-symbols-rounded">save</span>
                    </button>
                    <button type="button" onclick="ForceSubmitForm(this.form, this, true)" name="deleteOrder" value="<?= $id; ?>" class="button-danger group-button">
                        <span>Delete</span>
                        <span class="material-symbols-rounded">delete</span>
                    </button>
                </div>
            </form>
        </div>
    <?php endif; ?>
<?php endif; ?>
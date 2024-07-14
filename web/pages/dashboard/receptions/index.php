<?php

use Components\Sidebar;
use Components\DashboardTable;
use Utilities\Helper;

$db = Helper::getDatabase();
$query = Helper::getURLQuery();

$headers = [
    "Tracking ID" => "id",
    "Distributor" => "distributor.name",
    "Date Shipped" => "dateShipped",
    "Status" => "status.name"
];

$sortMethod = DashboardTable::getSortMethod($query, $headers);
$searchMethod = DashboardTable::getSearchMethod($query, $headers);

$sqlInfo = "";

if (isset($query["info"])) {
    $id = $query["info"];

    $sqlInfo = <<<SQL
    reception: (
        SELECT
            id,
            distributor AS distributor.id,
            distributor.person.name AS distributor.name,
            delivery.dateShipped AS dateShipped,
            delivery.description AS description,
            (
                SELECT
                    id AS statusLineId,
                    out.id AS id,
                    out.name AS name,
                    eventDatetime,
                    description
                FROM delivery->deliveryStatusLine
                WHERE time.deletedAt IS NONE
                ORDER BY eventDatetime DESC
            ) AS status,
            (
                SELECT
                    id,
                    out.label AS label,
                    quantity
                FROM ->productLine
            ) AS products
        FROM ONLY $id
        WHERE time.deletedAt IS NONE
    ),

    selections: {
        distributors: (
            SELECT
                id,
                person.name AS name
            FROM distributor
            WHERE
                time.deletedAt IS NONE AND
                person.time.deletedAt IS NONE
            ORDER BY name
        ),

        deliveryStatus: (SELECT id, name FROM deliveryStatus),
    },
    SQL;
}

$results = $db->query(<<<SQL
RETURN {
    receptions: (
        SELECT * FROM (
            SELECT
                id,
                distributor.person AS distributor.id,
                distributor.person.name AS distributor.name,
                time::format(
                    delivery.dateShipped,
                    "%h %e, %Y"
                ) AS dateShipped,
                (
                    SELECT
                        out.name AS name,
                        time.createdAt
                    FROM ONLY delivery->deliveryStatusLine
                    WHERE time.deletedAt IS NONE
                    ORDER BY time.createdAt DESC
                    LIMIT 1
                ) AS status,
                time.createdAt AS createdAt
            FROM reception
            WHERE time.deletedAt IS NONE
        )
        $searchMethod
        $sortMethod
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
                <span class="material-symbols-rounded text-4xl">person</span>
                <h1 class="text-3xl font-semibold">Receptions</h1>
                <span class="text-3xl text-gray-400 font-semibold">(<?= count($results["receptions"]) ?>)</span>
            </div>
            <a href="/dashboard/receptions/add-new" class="button-primary group-button">
                <span>Add Reception</span>
                <span class="material-symbols-rounded">add</span>
            </a>
        </div>
        <div class="dashboard-content">
            <?php
            DashboardTable::render(
                $results["receptions"],
                ["Tracking ID", "Distributor", "Date Shipped", "Status"],
                function ($reception) use ($query) {
                    $id = $reception["id"];
                    $infoQuery = Helper::getURLPathQuery(query: array_merge($query, ["info" => $id]));
                    $distributorQuery = Helper::getURLPathQuery("/dashboard/persons", array_merge($query, ["info" => $reception["distributor"]["id"]]));
                    $status = match ($reception["status"]["name"] ?? "") {
                        "" => "",
                        "Delivered" => <<<HTML
                        <span class="px-2 py-1 rounded-full bg-green-500 text-white font-semibold">{$reception["status"]["name"]}</span>
                        HTML,
                        default => <<<HTML
                        <span class="px-2 py-1 rounded-full bg-gray-400 text-white font-semibold">{$reception["status"]["name"]}</span>
                        HTML
                    };

                    return [
                        <<<HTML
                        <a href="{$infoQuery}" class="dashboard-table-id">$id</a>
                        HTML,
                        <<<HTML
                        <a href="{$distributorQuery}" class="dashboard-table-id">{$reception["distributor"]["name"]}</a>
                        HTML,
                        $reception["dateShipped"],
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
    $edit = $_SESSION["edit"] ?? [];
    $inputs = $_SESSION["inputs"] ?? [];

    $reception = $results["reception"];
    $selections = $results["selections"];
    ?>

    <?php if ($reception) : ?>
        <div class="dashboard-info">
            <form class="dashboard-info-container" action="/dashboard/receptions/handler?<?= http_build_query($query); ?>" method="POST">
                <div class="dashboard-info-header">
                    <h1><?= $reception["id"]; ?></h1>
                    <button type="button" onclick="ForceSubmitForm(this.form, this)" name="close" value="true" class="button-danger p-1 leading-[0] rounded-full">
                        <span class="material-symbols-rounded">close</span>
                    </button>
                </div>
                <div class="dashboard-info-content">
                    <div class="section">
                        <div class="header">
                            <h2>Reception Info</h2>
                            <button type="button" onclick="ForceSubmitForm(this.form, this)" name="edit" value="information" class="<?= Helper::editButtonClass($edit, "information"); ?> group-button text-sm">
                                <span>Edit</span>
                                <span class="material-symbols-rounded"><?= Helper::editButtonSymbol($edit, "information"); ?></span>
                            </button>
                        </div>
                        <div class="content">
                            <div class="group-input-box">
                                <div class="input-box">
                                    <label for="distributor">Distributor</label>
                                    <select id="distributor" name="distributor" <?= Helper::inputDisabled($edit, "information"); ?> class="h-full">
                                        <?php foreach ($selections["distributors"] as $distributor) : ?>
                                            <option value="<?= $distributor["id"]; ?>" <?= ($inputs["distributor"] ?? $reception["distributor"]["id"]) === $distributor["id"] ? "selected" : "" ?>><?= $distributor["name"]; ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="input-box">
                                    <label for="dateShipped">Date Shipped</label>
                                    <input id="dateShipped" name="dateShipped" type="date" <?= Helper::inputDisabled($edit, "information"); ?> value="<?= $inputs["dateShipped"] ?? date_create($reception["dateShipped"])->format("Y-m-d"); ?>" required>
                                </div>
                            </div>
                            <div class="input-box">
                                <label for="description">Description</label>
                                <textarea id="description" name="description" <?= Helper::inputDisabled($edit, "information"); ?> class="min-h-20 max-h-[20rem] h-20"><?= $inputs["description"] ?? $reception["description"]; ?></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="section">
                        <div class="header">
                            <h2>Products</h2>
                        </div>
                        <div class="content">
                            <table>
                                <thead>
                                    <tr>
                                        <th class="text-left p-2">Product</th>
                                        <th class="p-2">Quantity</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($reception["products"] as $product) : ?>
                                        <tr class="odd:bg-gray-100 border-y border-gray-400">
                                            <td class="w-full p-2"><?= $product["label"]; ?></td>
                                            <td class="text-center"><?= $product["quantity"]; ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="section">
                        <div class="header">
                            <h2>Tracker</h2>
                        </div>
                        <div class="content">
                            <div class="dashboard-table-tracker">
                                <?php foreach ($reception["status"] as $index => $status) : ?>
                                    <div class="data">
                                        <span class="date">
                                            <span><?= date_create($status["eventDatetime"])->format("d M"); ?></span>
                                            <span><?= date_create($status["eventDatetime"])->format("H:i") ?></span>
                                        </span>
                                        <span class="event">
                                            <span>Status: <?= $status["name"]; ?></span>
                                            <span><?= $status["description"]; ?></span>
                                        </span>
                                        <div class="flex items-center">
                                            <button type="button" onclick="ForceSubmitForm(this.form, this, true)" name="deleteDeliveryStatusLine" value="<?= $status["statusLineId"] ?>" class="button-danger p-1 leading-[0] rounded-full">
                                                <span class="material-symbols-rounded">delete</span>
                                            </button>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                                </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="section">
                        <?php if (!isset($query["addStatus"])) : ?>
                            <a href="<?= Helper::getURLPathQuery(query: Helper::arrayToggle($query, "addStatus")); ?>" class="flex justify-center items-center rounded-2xl border-2 border-dashed h-12 font-bold text-gray-400 border-gray-400 ring-gray-200 hover:bg-gray-100 hover:ring focus:bg-gray-100 focus:ring transition-colors">Add Status</a>
                        <?php else : ?>
                            <div class="header">
                                <h2>Add Status</h2>
                                <a href="<?= Helper::getURLPathQuery(query: Helper::arrayToggle($query, "addStatus")); ?>" class="button-danger group-button">
                                    <span>Cancel</span>
                                    <span class="material-symbols-rounded">close</span>
                                </a>
                            </div>
                            <div class="group-input-box">
                                <div class="input-box">
                                    <label for="addStatus[status]">Status</label>
                                    <select id="addStatus[status]" name="addStatus[status]" class="h-full" required>
                                        <option value="" disabled selected>Select Status</option>
                                        <?php foreach ($selections["deliveryStatus"] as $status) : ?>
                                            <option value="<?= $status["id"]; ?>" <?= ($inputs["addStatus"]["status"] ?? "") === $status["id"] ? "selected" : "" ?>><?= $status["name"]; ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div>
                                    <div class="input-box">
                                        <label for="addStatus[eventDatetime]">Event</label>
                                        <input type="datetime-local" id="addStatus[eventDatetime]" name="addStatus[eventDatetime]" value="<?= $inputs["addStatus"]["eventDatetime"] ?? date('Y-m-d\TH:i:s'); ?>" class="w-60">
                                    </div>
                                </div>
                            </div>
                            <div class="input-box">
                                <label for="addStatus[description]">Description</label>
                                <textarea id="addStatus[description]" name="addStatus[description]" class="min-h-20 max-h-[20rem] h-20"><?= $inputs["addStatus"]["description"] ?? ""; ?></textarea>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="dashboard-info-footer">
                    <button type="submit" name="updateReception" value="<?= $reception["id"]; ?>" class="button-success group-button">
                        <span>Save</span>
                        <span class="material-symbols-rounded">save</span>
                    </button>
                    <button type="button" onclick="ForceSubmitForm(this.form, this, true)" name="deleteReception" value="<?= $reception["id"] ?>" class="button-danger group-button">
                        <span>Delete</span>
                        <span class="material-symbols-rounded">delete</span>
                    </button>
                </div>
            </form>
        </div>
    <?php endif; ?>
<?php endif; ?>
<?php

use Components\Sidebar;
use Components\DashboardTable;
use Utilities\Helper;

$db = Helper::getDatabase();
$query = Helper::getURLQuery();

$headers = [
    "Product ID" => "id",
    "Label" => "label",
    "Description" => "description",
    "Desired Stocks" => "desiredStocks",
    "Physical Stocks" => "physicalStocks"
];

$sortMethod = DashboardTable::getSortMethod($query, $headers);
$searchMethod = DashboardTable::getSearchMethod($query, $headers);

$products = $db->query(<<<SQL
SELECT * FROM (
    SELECT
        id,
        label,
        description,
        desiredStocks,
        array::len((
            SELECT
                id,
                status.name AS status
            FROM ->stock
            WHERE
                time.deletedAt IS NONE AND
                status = stockStatus:0
        )) AS physicalStocks,
        time.createdAt AS createdAt
    FROM product
    WHERE time.deletedAt IS NONE
    $sortMethod
) $searchMethod;
SQL);
?>

<div class="flex">
    <?php Sidebar::render(); ?>
    <div class="dashboard-container">
        <div class="dashboard-header">
            <div class="flex items-center gap-2">
                <span class="material-symbols-rounded text-4xl">inventory_2</span>
                <h1 class="text-3xl font-semibold">Inventory</h1>
                <span class="text-3xl text-gray-400 font-semibold">(<?= count($products) ?>)</span>
            </div>
            <a href="/dashboard/inventory/add-new" class="button-primary group-button">
                <span>Add Product</span>
                <span class="material-symbols-rounded">add</span>
            </a>
        </div>
        <div class="dashboard-content">
            <?php
            DashboardTable::render(
                $products,
                ["Product ID", "Label", "Description", "Desired Stocks", "Physical Stocks"],
                function ($product) {
                    $id = $product["id"];
                    $infoQuery = Helper::getURLPathQuery(query: ["info" => $id]);

                    return [
                        <<<HTML
                        <a href="{$infoQuery}" class="dashboard-table-id">$id</a>
                        HTML,
                        $product["label"],
                        $product["description"],
                        $product["desiredStocks"],
                        $product["physicalStocks"]
                    ];
                },
                allowSearch: fn ($column) => match ($column) {
                    "Desired Stocks", "Physical Stocks" => false,
                    default => true
                },
                headerStyle: fn ($column) => match ($column) {
                    "Physical Stocks", "Desired Stocks" => "align-items: center;",
                    default => ""
                },
                rowStyle: fn ($column) => match ($column) {
                    "Physical Stocks", "Desired Stocks" => "text-align: center;",
                    default => ""
                }
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

    $product = $db->query(<<<SQL
    SELECT
        id,
        label,
        description,
        buyingPrice,
        sellingPrice,
        desiredStocks,
        array::len((
            SELECT
                id,
                status.name AS status
            FROM ->stock
            WHERE
                time.deletedAt IS NONE AND
                status = stockStatus:0
        )) AS physicalStocks
    FROM ONLY $id
    WHERE time.deletedAt IS NONE;
    SQL);
    ?>

    <?php if ($product) : ?>
        <div class="dashboard-info">
            <form class="dashboard-info-container" action="<?= Helper::getURLPathQuery("/dashboard/inventory/handler", $query); ?>" method="POST">
                <div class="dashboard-info-header">
                    <h1><?= $product["id"]; ?></h1>
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
                            <div class="input-box">
                                <label for="label">Label</label>
                                <input type="text" id="label" name="label" value="<?= $inputs["label"] ?? $product["label"]; ?>" <?= Helper::inputDisabled($edit, "information"); ?> required autofocus />
                            </div>
                            <div class="group-input-box">
                                <div class="input-box">
                                    <label for="buyingPrice">Buying Price</label>
                                    <input type="number" step="0.01" min="0" id="buyingPrice" name="buyingPrice" value="<?= $inputs["buyingPrice"] ?? $product["buyingPrice"]; ?>" <?= Helper::inputDisabled($edit, "information"); ?> required />
                                </div>
                                <div class="input-box">
                                    <label for="sellingPrice">Selling Price</label>
                                    <input type="number" step="0.01" min="0" id="sellingPrice" name="sellingPrice" value="<?= $inputs["sellingPrice"] ?? $product["sellingPrice"]; ?>" <?= Helper::inputDisabled($edit, "information"); ?> required />
                                </div>
                            </div>
                            <div class="group-input-box">
                                <div class="input-box">
                                    <label for="desiredStocks">Desired Stock</label>
                                    <input type="number" min="0" id="desiredStocks" name="desiredStocks" value="<?= $inputs["desiredStocks"] ?? $product["desiredStocks"]; ?>" <?= Helper::inputDisabled($edit, "information"); ?> required />
                                </div>
                                <div class="input-box">
                                    <label for="physicalStocks">Physical Stock</label>
                                    <input type="number" min="<?= $product["physicalStocks"]; ?>" id="physicalStocks" name="physicalStocks" value="<?= $inputs["physicalStocks"] ?? $product["physicalStocks"]; ?>" <?= Helper::inputDisabled($edit, "information"); ?> required />
                                </div>
                            </div>
                            <div class="input-box">
                                <label for="description">Description</label>
                                <textarea id="description" name="description" placeholder="Optional" class="min-h-20 h-20 max-h-[20rem]" <?= Helper::inputDisabled($edit, "information"); ?> required><?= $inputs["description"] ?? $product["description"] ?? ""; ?></textarea>
                            </div>
                        </div>
                    </div>
                </div>
                <div class=" dashboard-info-footer">
                    <button type="submit" name="updateProduct" value="<?= $product["id"]; ?>" class="button-success group-button">
                        <span>Save</span>
                        <span class="material-symbols-rounded">save</span>
                    </button>
                    <button type="button" onclick="ForceSubmitForm(this.form, this)" name="deleteProduct" value="<?= $product["id"] ?>" class="button-danger group-button">
                        <span>Delete</span>
                        <span class="material-symbols-rounded">delete</span>
                    </button>
                </div>
            </form>
        </div>
    <?php endif; ?>
<?php endif; ?>
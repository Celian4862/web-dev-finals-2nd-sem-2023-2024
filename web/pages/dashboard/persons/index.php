<?php

use Components\Sidebar;
use Components\DashboardTable;
use Utilities\Helper;

$db = Helper::getDatabase();
$query = Helper::getURLQuery();

$headers = [
    "Person ID" => "id",
    "Name" => "name",
    "Contact Email" => "contactEmail",
    "Contact Number" => "contactNumber"
];

$sortMethod = DashboardTable::getSortMethod($query, $headers);
$searchMethod = DashboardTable::getSearchMethod($query, $headers);

$persons = $db->query(<<<SQL
SELECT * FROM (
    SELECT
        id,
        name,
        contact.email AS contactEmail,
        contact.phone AS contactNumber,
        time.createdAt AS createdAt,
        (
            SELECT id FROM ONLY client
            WHERE
                person = \$parent.id AND
                time.deletedAt IS NONE
            LIMIT 1
        ).id AS client,
        (
            SELECT id FROM ONLY distributor
            WHERE
                person = \$parent.id AND
                time.deletedAt IS NONE
            LIMIT 1
        ).id AS distributor
    FROM person
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
                <span class="material-symbols-rounded text-4xl">group</span>
                <h1 class="text-3xl font-semibold">Person</h1>
                <span class="text-3xl text-gray-400 font-semibold">(<?= count($persons) ?>)</span>
            </div>
            <a href="/dashboard/persons/add-new" class="button-primary group-button">
                <span>Add Person</span>
                <span class="material-symbols-rounded">add</span>
            </a>
        </div>
        <div class="dashboard-content">
            <?php
            DashboardTable::render(
                $persons,
                ["Person ID", "Name", "Contact Email", "Contact Number", "Type"],
                function ($person) use ($query) {
                    $id = $person["id"];
                    $infoQuery = Helper::getURLPathQuery(query: array_merge($query, ["info" => $id]));

                    $isClient = isset($person["client"]) ? <<<HTML
                    <span class="px-2 py-1 rounded-full bg-blue-500 text-white font-semibold">Client</span>
                    HTML : "";
                    $isDistributor = isset($person["distributor"]) ? <<<HTML
                    <span class="px-2 py-1 rounded-full bg-orange-500 text-white font-semibold">Distributor</span>
                    HTML : "";

                    return [
                        <<<HTML
                        <a href="{$infoQuery}" class="dashboard-table-id">$id</a>
                        HTML,
                        $person["name"],
                        $person["contactEmail"],
                        $person["contactNumber"],
                        <<<HTML
                        <div class="flex justify-center gap-2">
                            $isClient
                            $isDistributor
                        </div>
                        HTML
                    ];
                },
                allowSort: fn ($column) => match ($column) {
                    "Type" => false,
                    default => true,
                },
                allowSearch: fn ($column) => match ($column) {
                    "Type" => false,
                    default => true,
                },
                headerStyle: fn ($column) => match ($column) {
                    "Type" => "text-align: center;",
                    default => "",
                },
                rowStyle: fn ($column) => match ($column) {
                    "Type" => "text-align: center;",
                    default => "",
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

    $person = $db->query(<<<SQL
    SELECT
        id,
        name AS name,
        contact AS contact,
        address AS address,
        (
            SELECT id
            FROM ONLY client
            WHERE
                person = $id AND
                time.deletedAt IS NONE
            LIMIT 1
        ).id AS client,
        (
            SELECT id
            FROM ONLY distributor
            WHERE
                person = $id AND
                time.deletedAt IS NONE
            LIMIT 1
        ).id AS distributor
    FROM ONLY $id
    WHERE time.deletedAt IS NONE
    FETCH address;
    SQL);
    ?>

    <?php if ($person) : ?>
        <div class="dashboard-info">
            <form class="dashboard-info-container" action="/dashboard/persons/handler?<?= http_build_query($query); ?>" method="POST">
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
                            <div class="input-box">
                                <label for="name">Name</label>
                                <input type="text" id="name" name="name" value="<?= $inputs["name"] ?? $person["name"]; ?>" <?= Helper::inputDisabled($edit, "information"); ?> required autofocus />
                            </div>
                            <div class="group-input-box">
                                <div class="input-box">
                                    <label for="contact[email]">Contact Email</label>
                                    <input type="email" id="contact[email]" name="contact[email]" placeholder="Optional" value="<?= $inputs["contact"]["email"] ?? $person["contact"]["email"] ?? ""; ?>" <?= Helper::inputDisabled($edit, "information"); ?> />
                                </div>
                                <div class="input-box">
                                    <label for="contact[phone]">Contact Number</label>
                                    <input type="tel" id="contact[phone]" name="contact[phone]" placeholder="Optional" value="<?= $inputs["contact"]["phone"] ?? $person["contact"]["phone"] ?? ""; ?>" <?= Helper::inputDisabled($edit, "information"); ?> />
                                </div>
                            </div>
                            <div class="flex justify-between">
                                <?php if (isset($person["client"])) : ?>
                                    <button type="button" onclick="ForceSubmitForm(this.form, this)" name="deleteClient" value="<?= $id ?>" class="button-danger">Unset Client</button>
                                <?php else : ?>
                                    <button type="button" onclick="ForceSubmitForm(this.form, this)" name="setClient" value="<?= $id ?>" class="button-primary">Set As Client</button>
                                <?php endif; ?>
                                <?php if (isset($person["distributor"])) : ?>
                                    <button type="button" onclick="ForceSubmitForm(this.form, this)" name="deleteDistributor" value="<?= $id ?>" class="button-danger">Unset Distributor</button>
                                <?php else : ?>
                                    <button type="button" onclick="ForceSubmitForm(this.form, this)" name="setDistributor" value="<?= $id ?>" class="button-primary">Set As Distributor</button>
                                <?php endif; ?>
                            </div>
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
                        <div class="group-input-box">
                            <div class="input-box">
                                <label for="address[country]">Country</label>
                                <input type="text" id="address[country]" name="address[country]" placeholder="Optional" value="<?= $inputs["address"]["country"] ?? $person["address"]["country"] ?? ""; ?>" <?= Helper::inputDisabled($edit, "address"); ?> />
                            </div>
                            <div class="input-box">
                                <label for="address[city]">City</label>
                                <input type="text" id="address[city]" name="address[city]" placeholder="Optional" value="<?= $inputs["address"]["city"] ?? $person["address"]["city"] ?? ""; ?>" <?= Helper::inputDisabled($edit, "address"); ?> />
                            </div>
                        </div>
                        <div class="group-input-box">
                            <div class="input-box">
                                <label for="address[street]">Street</label>
                                <input type="text" id="address[street]" name="address[street]" placeholder="Optional" value="<?= $inputs["address"]["street"] ?? $person["address"]["street"] ?? ""; ?>" <?= Helper::inputDisabled($edit, "address"); ?> />
                            </div>
                            <div class="input-box w-20 flex-grow-0">
                                <label for="address[zipCode]">Zip Code</label>
                                <input type="text" id="address[zipCode]" name="address[zipCode]" placeholder="Optional" value="<?= $inputs["address"]["zipCode"] ?? $person["address"]["zipCode"] ?? ""; ?>" <?= Helper::inputDisabled($edit, "address"); ?> />
                            </div>
                        </div>
                    </div>
                </div>
                <div class="dashboard-info-footer">
                    <button type="submit" name="updatePerson" value="<?= $id; ?>" class="button-success group-button">
                        <span>Save</span>
                        <span class="material-symbols-rounded">save</span>
                    </button>
                    <button type="button" onclick="ForceSubmitForm(this.form, this)" name="deletePerson" value="<?= $id; ?>" class="button-danger group-button">
                        <span>Delete</span>
                        <span class="material-symbols-rounded">delete</span>
                    </button>
                </div>
            </form>
        </div>
    <?php endif; ?>
<?php endif; ?>
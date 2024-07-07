<?php

session_start();

use Components\Sidebar;
use Components\DashboardTable;
use Utilities\Helper;

$db = Helper::getDatabase();
$path = Helper::getURLPath();
$query = Helper::getURLQuery();

$headers = [
    "Client ID" => "id",
    "Name" => "name",
    "Address" => "address",
    "Contact Email" => "contactEmail",
    "Contact Number" => "contactNumber"
];

$sortMethod = DashboardTable::getSortMethod($query, $headers);
$searchMethod = DashboardTable::getSearchMethod($query, $headers);

$clients = $db->query(<<<SQL
SELECT * FROM (
    SELECT
        id,
        person.name as name,
        person.contact.email as contactEmail,
        person.contact.phone as contactNumber,
        (
            IF person.address THEN
                (
                    SELECT
                        string::join(
                            ", ",
                            country,
                            city,
                            street,
                            zipCode
                        ) as address
                    FROM ONLY person.address
                )["address"]
            ELSE
                ""
            END
        ) as address,
        time.createdAt as createdAt
    FROM client
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
                <h1 class="text-3xl font-semibold">Clients</h1>
                <span class="text-3xl text-gray-400 font-semibold">(<?= count($clients) ?>)</span>
            </div>
            <a href="/dashboard/clients/add-new" class="button-primary group-button">
                <span>Add Client</span>
                <span class="material-symbols-rounded">add</span>
            </a>
        </div>
        <div class="dashboard-content">
            <?php
            DashboardTable::render(
                $clients,
                ["Client ID", "Name", "Address", "Contact Email", "Contact Number"],
                function ($clients) use ($path) {
                    $id = $clients["id"];

                    return [
                        "<a href='$path?info=$id' class='dashboard-table-id'>$id</a>",
                        $clients["name"],
                        $clients["address"],
                        $clients["contactEmail"],
                        $clients["contactNumber"]
                    ];
                },
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
        person.name as name,
        person.contact as contact,
        person.address as address
    FROM ONLY $id
    WHERE time.deletedAt IS NONE
    FETCH address;
    SQL);
    ?>

    <?php if ($person) : ?>
        <div class="dashboard-info">
            <form class="dashboard-info-container" action="/dashboard/clients/handler?<?= http_build_query($query); ?>" method="POST">
                <div class="dashboard-info-header">
                    <h1><?= $person["id"]; ?></h1>
                    <button type="submit" name="close" value="true" class="button-danger disabled p-1 leading-[0] rounded-full">
                        <span class="material-symbols-rounded">close</span>
                    </button>
                </div>
                <div class="dashboard-info-content">
                    <div class="section">
                        <div class="header">
                            <h2>Information</h2>
                            <button type="submit" name="edit" value="information" class="<?= Helper::editButtonClass($edit, "information"); ?> group-button text-sm">
                                <span>Edit</span>
                                <span class="material-symbols-rounded"><?= Helper::editButtonSymbol($edit, "information"); ?></span>
                            </button>
                        </div>
                        <div class="content">
                            <div class="input-box">
                                <label for="name">Email</label>
                                <input type="text" id="name" name="name" value="<?= $inputs["name"] ?? $person["name"]; ?>" <?= Helper::inputDisabled($edit, "information"); ?> required autofocus />
                            </div>
                            <div class="group-input-box">
                                <div class="input-box">
                                    <label for="contact[email]">Contact Email</label>
                                    <input type="email" id="contact[email]" name="contact[email]" value="<?= $inputs["contact"]["email"] ?? $person["contact"]["email"] ?? ""; ?>" <?= Helper::inputDisabled($edit, "information"); ?> required />
                                </div>
                                <div class="input-box">
                                    <label for="contact[phone]">Contact Number</label>
                                    <input type="tel" id="contact[phone]" name="contact[phone]" value="<?= $inputs["contact"]["phone"] ?? $person["contact"]["phone"] ?? ""; ?>" <?= Helper::inputDisabled($edit, "information"); ?> required />
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="section">
                        <div class="header">
                            <h2>Address</h2>
                            <button type="submit" name="edit" value="address" class="<?= Helper::editButtonClass($edit, "address"); ?> group-button text-sm">
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
                    <div class="section">
                        <div class="header">
                            <h2>Orders</h2>
                        </div>
                        <div class="content">
                            <!--
                            // TODO: Make table for client orders.
                            // * Make sure to use DashboardTable component.
                            -->
                            Empty
                        </div>
                    </div>
                </div>
                <div class="dashboard-info-footer">
                    <button type="submit" name="updateClient" value="<?= $person["id"]; ?>" class="button-success group-button">
                        <span>Save</span>
                        <span class="material-symbols-rounded">save</span>
                    </button>
                    <button type="submit" name="deleteClient" value="<?= $person["id"] ?>" class="button-danger group-button">
                        <span>Delete</span>
                        <span class="material-symbols-rounded">delete</span>
                    </button>
                </div>
            </form>
        </div>
    <?php endif; ?>
<?php endif; ?>
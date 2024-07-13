<?php

use Components\Sidebar;
use Components\DashboardTable;
use Utilities\Helper;



$db = Helper::getDatabase();
$query = Helper::getURLQuery();

$headers = [
    "Employee ID" => "id",
    "Name" => "name",
    "Address" => "address",
    "Contact Email" => "contactEmail",
    "Contact Number" => "contactNumber"
];

$sortMethod = DashboardTable::getSortMethod($query, $headers);
$searchMethod = DashboardTable::getSearchMethod($query, $headers);

$employees = $db->query(<<<SQL
SELECT * FROM (SELECT
    id,
    string::join(
        ' ',
        details.fullName.first,
        details.fullName.last
    ) AS name,
    (
        SELECT
            string::join(
                ", ",
                out.country,
                out.city,
                out.street,
                out.zipCode
            ) AS address
        FROM ONLY ->addressLine
        WHERE
            primary = true AND
            time.deletedAt IS NONE AND
            out.time.deletedAt IS NONE
        LIMIT 1
    ).address AS address, 
    details.contact.email AS contactEmail,
    details.contact.phone AS contactNumber,
    time.createdAt AS createdAt
FROM employee
WHERE time.deletedAt IS NONE
$sortMethod) $searchMethod;
SQL);
?>

<div class="flex">
    <?php Sidebar::render(); ?>
    <div class="dashboard-container">
        <div class="dashboard-header">
            <div class="flex items-center gap-2">
                <span class="material-symbols-rounded text-4xl">person</span>
                <h1 class="text-3xl font-semibold">Employees</h1>
                <span class="text-3xl text-gray-400 font-semibold">(<?= count($employees) ?>)</span>
            </div>
            <a href="/dashboard/employees/add-new" class="button-primary group-button">
                <span>Add Employee</span>
                <span class="material-symbols-rounded">add</span>
            </a>
        </div>
        <div class="dashboard-content">
            <?php
            DashboardTable::render(
                $employees,
                ["Employee ID", "Name", "Address", "Contact Email", "Contact Number"],
                function ($employee) use ($query) {
                    $id = $employee["id"];
                    $infoQuery = Helper::getURLPathQuery(query: array_merge($query, ["info" => $id]));

                    return [
                        <<<HTML
                        <a href="{$infoQuery}" class="dashboard-table-id">$id</a>
                        HTML,
                        $employee["name"],
                        $employee["address"],
                        $employee["contactEmail"],
                        $employee["contactNumber"]
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

    $employee = $db->query(<<<SQL
    SELECT
        *,
        (
            SELECT
                id,
                primary,
                out AS address
            FROM ->addressLine
            WHERE
                time.deletedAt IS NONE AND
                out.time.deletedAt IS NONE
            FETCH address
        ) AS addressLine 
    FROM ONLY $id
    WHERE time.deletedAt IS NONE
    SQL);
    ?>

    <?php if ($employee) : ?>
        <div class="dashboard-info">
            <form class="dashboard-info-container" action="/dashboard/employees/handler?<?= http_build_query($query); ?>" method="POST">
                <div class="dashboard-info-header">
                    <h1><?= $employee["id"]; ?></h1>
                    <button type="button" onclick="ForceSubmitForm(this.form, this)" name="close" value="true" class="button-danger p-1 leading-[0] rounded-full">
                        <span class="material-symbols-rounded">close</span>
                    </button>
                </div>
                <div class="dashboard-info-content">
                    <div class="section">
                        <div class="header">
                            <h2>Login Credentials</h2>
                            <button type="button" onclick="ForceSubmitForm(this.form, this)" name="edit" value="loginCredentials" class="<?= Helper::editButtonClass($edit, "loginCredentials"); ?> group-button text-sm">
                                <span>Edit</span>
                                <span class="material-symbols-rounded"><?= Helper::editButtonSymbol($edit, "loginCredentials"); ?></span>
                            </button>
                        </div>
                        <div class="content">
                            <div class="group-input-box">
                                <div class="input-box">
                                    <label for="email">Email</label>
                                    <input type="email" id="email" name="email" value="<?= $inputs["email"] ?? $employee["email"]; ?>" <?= Helper::inputDisabled($edit, "loginCredentials"); ?> required autofocus />
                                </div>
                                <div class="input-box">
                                    <label for="password">Password</label>
                                    <input type="password" id="password" name="password" value="<?= $input["password"] ?? $employee["password"] ?>" <?= Helper::inputDisabled($edit, "loginCredentials"); ?> required />
                                </div>
                            </div>
                        </div>
                    </div>
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
                                    <label for="firstName">First Name</label>
                                    <input type="text" id="firstName" name="details[fullName][first]" value="<?= $inputs["details"]["fullName"]["first"] ?? $employee["details"]["fullName"]["first"]; ?>" <?= Helper::inputDisabled($edit, "information"); ?> required autofocus />
                                </div>
                                <div class="input-box">
                                    <label for="middleName">Middle Name</label>
                                    <input type="text" id="middleName" name="details[fullName][middle]" placeholder="Optional" value="<?= $inputs["details"]["fullName"]["middle"] ?? $employee["details"]["fullName"]["middle"] ?? ""; ?>" <?= Helper::inputDisabled($edit, "information"); ?>>
                                </div>
                            </div>
                            <div class="group-input-box">
                                <div class="input-box">
                                    <label for="lastName">Last Name</label>
                                    <input type="text" id="lastName" name="details[fullName][last]" value="<?= $inputs["details"]["fullName"]["last"] ?? $employee["details"]["fullName"]["last"]; ?>" <?= Helper::inputDisabled($edit, "information"); ?> required />
                                </div>
                                <div class="input-box">
                                    <label for="suffix">Suffix</label>
                                    <input type="text" id="suffix" name="details[fullName][suffix]" placeholder="Optional" value="<?= $inputs["details"]["fullName"]["suffix"] ?? $employee["details"]["fullName"]["suffix"] ?? ""; ?>" <?= Helper::inputDisabled($edit, "information"); ?> />
                                </div>
                            </div>
                            <div class="group-input-box">
                                <div class="input-box">
                                    <label for="contactEmail">Contact Email</label>
                                    <input type="email" id="contactEmail" name="details[contact][email]" placeholder="Optional" value="<?= $inputs["details"]["contact"]["email"] ?? $employee["details"]["contact"]["email"] ?? ""; ?>" <?= Helper::inputDisabled($edit, "information"); ?> autofocus />
                                </div>
                                <div class="input-box">
                                    <label for="contactNumber">Contact Number</label>
                                    <input type="tel" id="contactNumber" name="details[contact][phone]" placeholder="Optional" value="<?= $inputs["details"]["contact"]["phone"] ?? $employee["details"]["contact"]["phone"] ?? ""; ?>" <?= Helper::inputDisabled($edit, "information"); ?> />
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php foreach ($employee["addressLine"] as $index => $addressLine) : ?>
                        <div class="section">
                            <div class="header">
                                <h2>Address <?= $index + 1 ?></h2>
                                <div class="group-button-4 text-sm">
                                    <?php if ($addressLine["primary"] === false) : ?>
                                        <button type="button" onclick="ForceSubmitForm(this.form, this)" name="setPrimaryAddress" value='<?= json_encode([$id, $addressLine["id"]]); ?>' class="button-primary group-button">
                                            <span>Set Primary</span>
                                            <span class="material-symbols-rounded">tune</span>
                                        </button>
                                    <?php endif; ?>
                                    <button type="button" onclick="ForceSubmitForm(this.form, this)" name="edit" value="addressLine-<?= $index; ?>" class="<?= Helper::editButtonClass($edit, "addressLine-$index"); ?> group-button">
                                        <span>Edit</span>
                                        <span class="material-symbols-rounded"><?= Helper::editButtonSymbol($edit, "addressLine-$index"); ?></span>
                                    </button>
                                    <button type="button" onclick="ForceSubmitForm(this.form, this)" name="deleteAddressLine" value="<?= $addressLine["id"]; ?>" class="button-danger group-button">
                                        <span>Delete</span>
                                        <span class="material-symbols-rounded">delete</span>
                                    </button>
                                </div>
                            </div>
                            <div class="content">
                                <div class="group-input-box">
                                    <div class="input-box">
                                        <label for="addressLine[<?= $index; ?>][country]">Country</label>
                                        <input type="text" id="addressLine[<?= $index; ?>][country]" name="addressLine[<?= $addressLine["address"]["id"]; ?>][country]" value="<?= $inputs["addressLine"][$addressLine["address"]["id"]]["country"] ?? $addressLine["address"]["country"]; ?>" <?= Helper::inputDisabled($edit, "addressLine-$index") ?> required autofocus />
                                    </div>
                                    <div class="input-box">
                                        <label for="addressLine[<?= $index; ?>][city]">City</label>
                                        <input type="text" id="addressLine[<?= $index; ?>][city]" name="addressLine[<?= $addressLine["address"]["id"]; ?>][city]" value="<?= $inputs["addressLine"][$addressLine["address"]["id"]]["city"] ?? $addressLine["address"]["city"]; ?>" <?= Helper::inputDisabled($edit, "addressLine-$index"); ?> required />
                                    </div>
                                </div>
                                <div class="group-input-box">
                                    <div class="input-box">
                                        <label for="addressLine[<?= $index; ?>][street]">Street</label>
                                        <input type="text" id="addressLine[<?= $index; ?>][street]" name="addressLine[<?= $addressLine["address"]["id"]; ?>][street]" value="<?= $inputs["addressLine"][$addressLine["address"]["id"]]["street"] ?? $addressLine["address"]["street"]; ?>" <?= Helper::inputDisabled($edit, "addressLine-$index"); ?> required />
                                    </div>
                                    <div class="input-box w-20 flex-grow-0">
                                        <label for="addressLine[<?= $index; ?>][zipCode]">Zip Code</label>
                                        <input type="text" id="addressLine[<?= $index; ?>][zipCode]" name="addressLine[<?= $addressLine["address"]["id"]; ?>][zipCode]" value="<?= $inputs["addressLine"][$addressLine["address"]["id"]]["zipCode"] ?? $addressLine["address"]["zipCode"]; ?>" <?= Helper::inputDisabled($edit, "addressLine-$index"); ?> required />
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                    <?php if (count($employee["addressLine"]) < 2) : ?>
                        <div class="section">
                            <?php if (!isset($query["addAddress"])) : ?>
                                <a href="<?= Helper::getURLPathQuery(query: Helper::arrayToggle($query, "addAddress")); ?>" class="flex justify-center items-center rounded-2xl border-2 border-dashed h-12 font-bold text-gray-400 border-gray-400 ring-gray-200 hover:bg-gray-100 hover:ring focus:bg-gray-100 focus:ring transition-colors">Add Address</a>
                            <?php else : ?>
                                <div class="header">
                                    <h2>Add Address</h2>
                                    <a href="<?= Helper::getURLPathQuery(query: Helper::arrayToggle($query, "addAddress")); ?>" class="button-danger group-button">
                                        <span>Cancel</span>
                                        <span class="material-symbols-rounded">close</span>
                                    </a>
                                </div>
                                <div class="content">
                                    <div class="group-input-box">
                                        <div class="input-box">
                                            <label for="addAddress[country]">Country</label>
                                            <input type="text" id="addAddress[country]" name="addAddress[country]" value="<?= $inputs["addAddress"]["country"] ?? ""; ?>" required autofocus />
                                        </div>
                                        <div class="input-box">
                                            <label for="addAddress[city]">City</label>
                                            <input type="text" id="addAddress[city]" name="addAddress[city]" value="<?= $inputs["addAddress"]["city"] ?? ""; ?>" required />
                                        </div>
                                    </div>
                                    <div class="group-input-box">
                                        <div class="input-box">
                                            <label for="addAddress[street]">Street</label>
                                            <input type="text" id="addAddress[street]" name="addAddress[street]" value="<?= $inputs["addAddress"]["street"] ?? ""; ?>" required />
                                        </div>
                                        <div class="input-box w-20 flex-grow-0">
                                            <label for="addAddress[zipCode]">Zip Code</label>
                                            <input type="text" id="addAddress[zipCode]" name="addAddress[zipCode]" value="<?= $inputs["addAddress"]["zipCode"] ?? ""; ?>" required />
                                        </div>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                </div>
                <div class="dashboard-info-footer">
                    <button type="submit" name="updateEmployee" value="<?= $employee["id"]; ?>" class="button-success group-button">
                        <span>Save</span>
                        <span class="material-symbols-rounded">save</span>
                    </button>
                    <button type="button" onclick="ForceSubmitForm(this.form, this)" name="deleteEmployee" value="<?= $employee["id"] ?>" class="button-danger group-button">
                        <span>Delete</span>
                        <span class="material-symbols-rounded">delete</span>
                    </button>
                </div>
            </form>
        </div>
    <?php endif; ?>
<?php endif; ?>
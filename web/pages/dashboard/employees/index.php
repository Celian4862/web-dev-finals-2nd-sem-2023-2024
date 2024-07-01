<?php

session_start();

use Components\Sidebar;
use Components\DashboardTable;
use Utilities\Employee;

use function Utilities\getDatabase;

$db = getDatabase();

$employees = $db->query("SELECT * FROM employee");
?>

<div class="flex">
    <?php Sidebar::render(); ?>
    <div class="dashboard-container">
        <div class="dashboard-header">
            <div class="flex items-center gap-2">
                <i class="material-symbols-rounded text-4xl">person</i>
                <h1 class="text-3xl font-semibold">Employees</h1>
                <span class="text-3xl text-gray-400 font-semibold">(<?= count($employees) ?>)</span>
            </div>
        </div>
        <div class="dashboard-content">
            <?php
            DashboardTable::render(
                $employees,
                ["Employee ID", "Full Name", "Email", "Contact"],
                function ($employee) {
                    $employee = new Employee($employee);

                    return [
                        $employee->id,
                        $employee->getFullName(),
                        $employee->email,
                        $employee->getContact()
                    ];
                },
            );
            ?>
        </div>
    </div>
</div>
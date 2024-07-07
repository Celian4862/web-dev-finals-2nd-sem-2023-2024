<?php

use Utilities\Helper;
use Components\DashboardTable;

$path = Helper::getURLPath();
$query = Helper::getURLQuery();
?>

<div class="dashboard-table">
    <table>
        <thead>
            <tr>
                <th>#</th>
                <?php foreach ($headers as $column) : ?>
                    <?php
                    $name = str_replace(' ', '_', $column);
                    $localQuery = DashboardTable::getColumnQuery($query, $headers, $column);
                    ?>

                    <th <?= (isset($headerStyle)) ? "style='{$headerStyle($column)}'" : "" ?> data-sorted="<?= $localQuery[$name] ?? 0; ?>">
                        <div class="flex flex-col gap-2">
                            <a href="<?= Helper::getURLPathQuery(query: Helper::arrayToggle($localQuery, $name, false)); ?>">
                                <span><?= $column; ?></span>
                                <span class="material-symbols-rounded font-bold">
                                    <?= ($localQuery[$name] ?? 1) == 1 ? "arrow_downward" : "arrow_upward" ?>
                                </span>
                            </a>
                            <?php if ($search) : ?>
                                <form action="<?= $path; ?>" method="GET" class="search-box">
                                    <input type="text" name="search[<?= $name; ?>]" value="<?= $_GET["search"][$name] ?? ""; ?>" placeholder="Search" class="flex-grow outline-none rounded" />
                                    <button type="submit" class="flex">
                                        <span class="material-symbols-rounded text-gray-300">search</span>
                                    </button>
                                </form>
                            <?php endif; ?>
                        </div>
                    </th>
                <?php endforeach ?>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($elements as $index => $element) : ?>
                <tr>
                    <td><?= $index + 1; ?></td>
                    <?php $row = $elementData($element) ?>
                    <?php if (isset($rowStyle)) : ?>
                        <?php foreach ($row as $dataIndex => $data) : ?>
                            <td style='<?= $rowStyle($headers[$dataIndex], $data) ?? "" ?>'><?= $data; ?></td>
                        <?php endforeach ?>
                    <?php else : ?>
                        <?php foreach ($row as $data) : ?>
                            <td><?= $data; ?></td>
                        <?php endforeach ?>
                    <?php endif; ?>
                </tr>
            <?php endforeach ?>
        </tbody>
    </table>
</div>
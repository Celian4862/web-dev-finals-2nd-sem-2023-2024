<?php

use Utilities\Helper;
use Components\DashboardTable;

$query = Helper::getURLQuery();
$pathQuery = Helper::getURLPathQuery();

$activeSort = DashboardTable::getActiveSort($headers);
?>

<div class="dashboard-table <?= $class; ?>">
    <table>
        <thead>
            <tr>
                <th>#</th>
                <?php foreach ($headers as $column) : ?>
                    <?php
                    $name = str_replace(' ', '_', $column);
                    $localQuery = DashboardTable::getColumnQuery($query, $headers, $column);
                    ?>

                    <th data-sorted="<?= $localQuery[$name] ?? 0; ?>">
                        <div <?= (isset($headerStyle)) ? "style='{$headerStyle($column)}'" : "" ?> class="flex flex-col gap-2">
                            <a <?= ($localAllowSort = !isset($allowSort) || $allowSort($column)) ? "data-allowSort='true' href='" . Helper::getURLPathQuery(query: Helper::arrayToggle($localQuery, $name, false, true)) . "'" : ""; ?>>
                                <span><?= $column; ?></span>
                                <?php if ($localAllowSort) : ?>
                                    <span class="material-symbols-rounded font-bold">
                                        <?= ($localQuery[$name] ?? 1) == 1 ? "arrow_downward" : "arrow_upward" ?>
                                    </span>
                                <?php endif; ?>
                            </a>
                            <?php if (isset($allowSearch) && $allowSearch($column)) : ?>
                                <form action="<?= Helper::getURLPathQuery(query: array_merge(array_diff_key($_GET, [$name => ""]))); ?>" method="GET">
                                    <div class="search-box">
                                        <input type="text" name="search[<?= $name; ?>]" value="<?= $_GET["search"][$name] ?? ""; ?>" placeholder="Search" class="flex-grow outline-none rounded" />
                                        <button type="submit" <?= isset($activeSort) ? "name='{$activeSort}' value='{$query[$activeSort]}'" : ""; ?> class="flex">
                                            <span class="material-symbols-rounded text-gray-300">search</span>
                                        </button>
                                    </div>
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
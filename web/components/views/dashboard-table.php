<div class="dashboard-table">
    <table>
        <tr>
            <th>#</th>
            <?php if (isset($headerStyle)) : ?>
                <?php foreach ($headers as $column) : ?>
                    <th style='<?= $headerStyle($column) ?? "" ?>'><?= $column; ?></th>
                <?php endforeach ?>
            <?php else : ?>
                <?php foreach ($headers as $column) : ?>
                    <th><?= $column; ?></th>
                <?php endforeach; ?>
            <?php endif; ?>
        </tr>
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
    </table>
</div>
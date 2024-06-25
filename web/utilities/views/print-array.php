<?php

use function Utilities\printArray;
?>

<ul class="list-disc px-4 ml-2">
    <?php foreach ($arr as $key => $val) : ?>
        <?php if (is_array($val)) : ?>
            <li>
                <span><?= $key; ?></span>
                <b> => </b>
                <span><?php printArray($val); ?></span>
            </li>
        <?php else : ?>
            <li>
                <span><?= $key; ?></span>
                <b> => </b>
                <span><?= $val; ?></span>
            </li>
        <?php endif; ?>
    <?php endforeach; ?>
</ul>
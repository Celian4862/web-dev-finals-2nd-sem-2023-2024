<?php

session_start();

use function Utilities\printArray;

use Components\Sidebar;
?>

<div class="flex">
    <?php
    Sidebar::render();
    printArray((array) $_SESSION["employee"]);
    ?>
</div>
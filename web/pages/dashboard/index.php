<?php

session_start();

use function Utilities\debug;

use Components\Sidebar;
?>

<div class="flex">
    <?php
    Sidebar::render();
    debug((array) $_SESSION["employee"]);
    ?>
</div>
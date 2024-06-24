<?php

session_start();

use function Utilities\PrintArray;

use Components\Sidebar;

?>

<div class="flex">
    <?php
    Sidebar::render();
    PrintArray((array) $_SESSION["employee"]);
    ?>
</div>
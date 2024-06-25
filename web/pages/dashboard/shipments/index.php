<?php

session_start();

use Components\Sidebar;
?>

<div class="flex">
    <?php Sidebar::render(); ?>
    <!-- Start here -->
</div>
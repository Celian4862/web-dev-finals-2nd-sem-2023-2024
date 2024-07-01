<?php

namespace Components;

class Sidebar
{
    /** Render the sidebar. */
    public static function render()
    {
        include __DIR__ . "/views/sidebar.php";
    }
}

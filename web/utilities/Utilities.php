<?php

namespace Utilities;

/** Redirects the user to the specified path. */
function Redirect(string $path): void
{
    header("Location: $path");
    exit;
}

/** Print an array in a readable format. */
function PrintArray(array $arr): void
{
    echo "<ul style='list-style-type: disc; padding: revert' class='list-disc'>";
    foreach ($arr as $key => $val) {
        if (is_array($val)) {
            echo "<li><span>" . $key . "</span><b> => </b><span>";
            PrintArray($val);
            echo "</span></li>";
        } else {
            echo "<li><span>" . $key . "</span><b> => </b><span>" . $val . "</span></li>";
        }
    }
    echo "</ul>";
}

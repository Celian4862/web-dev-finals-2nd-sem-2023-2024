<?php

/** Redirects the user to the specified path. */
function redirect(string $path): void
{
    header("Location: $path");
    exit;
}

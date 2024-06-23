<?php

session_start();

use function Utilities\PrintArray;

PrintArray((array) $_SESSION["employee"]);

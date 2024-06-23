<?php
require_once WEB_PATH . "/utilities/employee.php";
require_once WEB_PATH . "/utilities/printArray.php";

session_start();

printArray((array) $_SESSION["employee"]);

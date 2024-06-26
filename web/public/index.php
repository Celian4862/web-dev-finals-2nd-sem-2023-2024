<?php

const BASE_PATH = __DIR__ . "/..";

require BASE_PATH . "/../vendor/autoload.php";

$urlPath = parse_url($_SERVER["REQUEST_URI"], PHP_URL_PATH);
$pagePath = BASE_PATH . "/pages$urlPath";
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>N2N Solutions</title>
    <link type="text/css" rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded:wght,FILL@300,1" />
    <link type="text/css" rel="stylesheet" href="/styles.css" />
</head>

<body class="font-roboto">
    <?php
    if (file_exists($pagePath . "/index.php")) {
        include_once $pagePath . "/index.php";
    } elseif (file_exists($pagePath . ".php")) {
        include_once $pagePath . ".php";
    } else {
        http_response_code(404);
        echo "404 Not Found";
    }
    ?>
</body>

</html>
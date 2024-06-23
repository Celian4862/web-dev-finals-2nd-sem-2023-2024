<?php

use Utilities\Surreal;

$db = new Surreal();

$db->connect(
    "http://localhost:8000",
    [
        "namespace" => "n2n",
        "database" => "n2n",
    ]
);

$db->signin(
    [
        "user" => "admin",
        "pass" => "admin",
    ]
);

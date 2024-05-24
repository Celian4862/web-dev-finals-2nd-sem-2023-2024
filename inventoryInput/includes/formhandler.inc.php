<?php

if($_SERVER["REQUEST_METHOD"] == "POST") {
    $label = $_POST["label"];
    $product_description = $_POST["product_description"];
    $warranty = $_POST["warranty"];

    try {
        require_once "dbh.inc.php";

        $query = "INSERT INTO products (label, product_description, warranty) VALUES (:label, :product_description, :warranty);";
        $stmt = $pdo->prepare($query);

        $stmt->bindParam(":label", $label);
        $stmt->bindParam(":product_description", $product_description);
        $stmt->bindParam(":warranty", $warranty);

        $stmt->execute();

        $pdo = null;
        $stmt = null;

        header("Location: ../../inventory/index.php");
        die();
    } catch (PDOException $e) {
        die("Query failed: " . $e->getMessage());
    }
}
else {
    header("Location: ../inventoryInput.php");
}

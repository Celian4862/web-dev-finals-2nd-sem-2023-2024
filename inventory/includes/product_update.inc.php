<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = $_POST["id"];
    $label = $_POST["label"];
    $product_description = $_POST["product_description"];
    $warranty = $_POST["warranty"];

    try {
        require_once "dbh.inc.php";

        $query = "UPDATE products SET label = :label, product_description = :product_description, warranty = :warranty WHERE id = :id";
        $stmt = $pdo->prepare($query);

        $stmt->bindParam(":label", $label);
        $stmt->bindParam(":product_description", $product_description);
        $stmt->bindParam(":warranty", $warranty);
        $stmt->bindParam(":id", $id);

        $stmt->execute();

        $pdo = null;
        $stmt = null;

        header("Location: ../index.php");
        die();
    } catch (PDOException $e) {
        die("Query failed: " . $e->getMessage());
    }
} else {
    header("Location: ../index.php");
}

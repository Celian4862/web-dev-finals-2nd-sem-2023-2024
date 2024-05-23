<?php
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["id"])) {
    $id = $_POST["id"];

    try {
        require_once "dbh.inc.php";

        $query = "DELETE FROM products WHERE id = :id";
        $stmt = $pdo->prepare($query);

        $stmt->bindParam(":id", $id);
        $stmt->execute();

        header("Location: ../index.php");
        die();
    } catch (PDOException $e) {
        die("Query failed: " . $e->getMessage());
    }
} else {
    header("Location: ../index.php");
    die();
}
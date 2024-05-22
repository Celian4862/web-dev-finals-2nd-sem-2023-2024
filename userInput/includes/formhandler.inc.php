<?php

if($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST["email"];
    $pwd = $_POST["pwd"];

    try {
        require_once "dbh.inc.php";

        $query = "INSERT INTO users (email, pwd) VALUES (:email, :pwd);";
        $stmt = $pdo->prepare($query);

        $stmt->bindParam(":email", $email);
        $stmt->bindParam(":pwd", $pwd);

        $stmt->execute();

        $pdo = null;
        $stmt = null;

        header("Location: ../userInput.php");
        die();
    } catch (PDOException $e) {
        die("Query failed: " . $e->getMessage());
    }
}
else {
    header("Location: ../userInput.php");
}

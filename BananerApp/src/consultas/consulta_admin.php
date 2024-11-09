<?php
session_start();
include('../loader/config/connection.php'); // Ensure this path is correct

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];
    $role = $_POST['role'];

    if ($db) {
        $stmt = $db->prepare("INSERT INTO usuarios (email, password, role) VALUES (?, ?, ?)");
        $stmt->execute([$email, $password, $role]);

        if ($stmt) {
            header("Location: ../index.php");
        } else {
            echo("FATAL ERROR");
        }
    } else {
        die("Connection failed: " . $e->getMessage());
    }
}
?>
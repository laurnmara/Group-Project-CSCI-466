<?php

    include("functions-components.php");
        session_start();

    try {
            // Connecting using MySql (MariaDB)
            $dsn = "mysql:host=courses;dbname=z2048942";
            $pdo = new PDO($dsn, $username, $password);
            $pdo->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);

            if (!isset($_SESSION['user_id'])) {
                $_SESSION['user_id'] = 1; // pretend user 1 is logged in
            }
            
            if (isset($_POST['delete_btn']) && isset($_POST['product_id'])) {
                $id = $_POST['product_id'];

                $update_inv = $pdo->prepare("UPDATE ");
    
                $delete = $pdo->prepare("DELETE FROM Cart WHERE ProductID = :id");
                $delete->bindParam(':id', $id, PDO::PARAM_INT);

            if ($delete->execute()) {
        
                header("Location: cart.php.php?msg=deleted");
                exit();

            } else {
            echo "Error deleting record.";
            }
        }
    }
    catch(PDOexception $e) {
            echo "Connection to database has failed: " . $e->getMessage();
    }
?>
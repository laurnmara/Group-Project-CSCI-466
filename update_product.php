<?php

    include("functions-components.php");
    session_start();

    if (!isset($_SESSION['role']) || $_SESSION['role'] != 'customer') {
        header("Location: enter_store.php");
        exit();
    }

    $userID = $_SESSION['user_id'];

    try {
            // Connecting using MySql (MariaDB)
            $dsn = "mysql:host=courses;dbname=z2048942";
            $pdo = new PDO($dsn, $username, $password);
            $pdo->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
            
            if (isset($_POST['update_btn'])) {
                $id = $_POST['product_id']; 
                $new_qty = filter_var($_POST['quantity'], FILTER_VALIDATE_INT);

                $getCart = $pdo->prepare("SELECT CartID FROM Cart WHERE UserID = ?");
                $getCart->execute([$_SESSION['user_id']]);
                $cart = $getCart->fetch();
                $cartID = $cart['CartID'];      

                $oldQTY = $pdo->prepare("
                SELECT Quantity FROM CartProduct
                WHERE CartID = ? AND ProductID = ?
                ");
                $oldQTY->execute([$cartID, $id]);
                $fetch = $oldQTY->fetch();
                $old_qty = $fetch['Quantity'];

                $diff = $new_qty - $old_qty;

                // update inventory based on difference
                $product_qty = $pdo->prepare("
                UPDATE Product
                SET NumInStock = NumInStock - ?
                WHERE ProductID = ?
                ");
                $product_qty->execute([$diff, $id]);

                if ($new_qty == 0) {
                    // delete item
                    $stmt = $pdo->prepare("
                        DELETE FROM CartProduct
                        WHERE CartID = ? AND ProductID = ?
                    ");
                    $stmt->execute([$cartID, $id]);
                } else {
                    // update quantity
                    $stmt = $pdo->prepare("
                        UPDATE CartProduct
                        SET Quantity = ?
                        WHERE CartID = ? AND ProductID = ?
                    ");
                    $stmt->execute([$new_qty, $cartID, $id]);
                }

                header("Location: cart.php");
                exit();
        }
    }
    catch(PDOexception $e) {
            echo "Connection to database has failed: " . $e->getMessage();
    }
?>
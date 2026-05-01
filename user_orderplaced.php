<html>
    <head>
        <title>Order Placed</title>
        <link rel="stylesheet" href="style.css">
    </head>

<body>
    <!-- User Nav bar for website -->
    <nav class="navbar">
            <ul>
                <li><a href="home.php">Shop</a></li>
                <li><a href="cart.php">Cart</a></li>
                <li><a href="track_order.php">Check Order Status</a></li>
                <li><form method="POST" action="logout.php">
                <button type="submit">Switch User</button>
                </form></li>
            </ul>
        </nav>
<?php
        // Login Credentials + Functions for DB
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

            if(isset($_POST['place_order'])){
                $name = $_POST['Name'];
                $email = $_POST['Email'];
                $phone = $_POST['PhoneNum'];
                $paymentID = $_POST['CardNum'];
                $address = $_POST['Address'];
                $bill_address = $_POST['BillingAddress'];
                $cartID = $_POST['cartID'];
                $totalPaid = $_POST['totalCost'];

                //check if payment exists
                $stmt = $pdo->prepare("SELECT 1 FROM Payment WHERE PaymentID = ? LIMIT 1");
                $stmt->execute([$paymentID]);

                //if paymentID doesn't exist
                if (!$stmt->fetchColumn()) {
                    $stmt = $pdo->prepare("INSERT INTO Payment (PaymentID, UserID) VALUES (?, ?)");
                    $stmt->execute([$paymentID, $userID]);
                }

                $stmt = $pdo->prepare("UPDATE Users SET Name = ?, Email = ?, PhoneNum = ? WHERE UserID = ?");
                $stmt->execute([$name, $email, $phone, $userID]);

                $date = date("Y-m-d");
                $stmt = $pdo->prepare("INSERT INTO StoreOrder (PaymentID, CartID, UserID, 
                ShipAddr, BillAddr, PricePaid, Date, Status) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
                $stmt->execute([$paymentID, $cartID, $userID, $address, $bill_address, 
                $totalPaid, $date, "Processing"]);

                $stmt = $pdo->prepare("DELETE FROM CartProduct WHERE CartID = ?");
                $stmt->execute([$cartID]);

                $url = "track_order.php";
                echo "<h3>Your order has been placed!</h3>";
                echo "<br><h3>Click <a href='$url'>here</a> to track your order!</h3>";

            }    
        }
        catch(PDOexception $e) {
            echo "Connection to database has failed: " . $e->getMessage();
        }
?>

</body>

</html>

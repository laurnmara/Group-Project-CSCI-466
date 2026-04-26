<html>
    <head>
        <title>Default</title>
        <link rel="stylesheet" href="style.css">
    </head>

<body>
    <!-- User Nav bar for website -->
    <nav class="navbar">
            <ul>
                <li><a href="home.php">Shop</a></li>
                <li><a href="cart.php">Cart</a></li>
                <li><a href="track_order.php">Check Order Status</a></li>
                <li><a href="owner-inventory.php">Store Inventory</a></li>
            </ul>
        </nav>
<?php
        // Login Credentials + Functions for DB
        include("functions-components.php");
        session_start();

        try {
            // Connecting using MySql (MariaDB)
            $dsn = "mysql:host=courses;dbname=z2048942";
            $pdo = new PDO($dsn, $username, $password);
            $pdo->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);

            $userID = $_SESSION['user_id'] = 1; // pretend user 1 is logged in

            if(isset($_POST['place_order'])){
                $name = $_POST['Name'];
                $email = $_POST['Email'];
                $phone = $_POST['PhoneNum'];
                $paymentID = $_POST['CardNum'];
                $address = $_POST['Address'];
                $bill_address = $_POST['BillingAddress'];
                $cartID = $_POST['cartID'];
                $totalPaid = $_POST['totalCost'];

                $stmt = $pdo->prepare("UPDATE Users SET Name = ?, Email = ?, PhoneNum = ? WHERE UserID = ?");
                $stmt->execute([$name, $email, $phone, $userID]);

                $stmt = $pdo->prepare("UPDATE Payment SET PaymentID = ? WHERE UserID = ?");
                $stmt->execute([$paymentID, $userID]);

                $date = date("Y-m-d");
                $stmt = $pdo->prepare("INSERT INTO StoreOrder (PaymentID, CartID, UserID, 
                ShipAddr, BillAddr, PricePaid, Date, Status) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
                $stmt->execute([$paymentID, $cartID, $userID, $address, $bill_address, 
                $totalPaid, $date, "Processing"]);

                $stmt = $pdo->prepare("DELETE FROM Cart WHERE CartID = ?");
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

<html>
    <head>
        <title>Track Order</title>
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

            $info_stmt = $pdo->prepare(
                "SELECT * FROM StoreOrder
                JOIN Cart ON StoreOrder.UserID = Cart.UserID
                WHERE StoreOrder.UserID = ?"
            );

            $info_stmt->execute([$userID]);

            $orders = $info_stmt->fetchALL(PDO::FETCH_ASSOC);
            $sum = 0;

            if(!$orders) { echo "No Orders Found!"; die(); }

            else {

                foreach ($orders as $row){

                    echo "<h2> Your Orders: </h2><br>";
                    echo "<h3> Order #: {$row['OrderNum']}</h3><br>";
                    echo "<h3> Order Status: {$row['Status']}</h3><br>";
                    
                    if ($row['Status'] == 'Processing') {
                        echo "<h3> Tracking Number: Not Available Until Shipped. </h3><br>";
                    }
                    else {
                        echo "<h3> Tracking Number: {$row['TrackingNum']}</h3><br>";
                    }

                    echo "<h3>Your Total: \${$row['PricePaid']}</h3><br><br><br>";

                    $sum = $sum + $row['PricePaid'];

                }

                echo "<h4>Your Total for All Orders: \${$sum}</h4><br>";

            }


        }

        catch(PDOexception $e) {
            echo "Connection to database has failed: " . $e->getMessage();
        }
?>

</body>

</html>




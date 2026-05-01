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
                <button class="nav-btn" type="submit">Switch User</button>
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
                WHERE UserID = ?"
            );

            $info_stmt->execute([$userID]);

            $orders = $info_stmt->fetchALL(PDO::FETCH_ASSOC);
            $sum = 0;

            echo "<div class='page-header'>";
            echo "<h2> Your Orders </h2>";
            echo "<div class='title-underline'></div>";
            echo "</div>";

            if(!$orders) { 
                echo "<div class='cart-container'><p style='text-align:center;'>No Orders Found!</p></div>"; 
            }
            else {
                echo "<div class='cart-container'>";

                foreach ($orders as $row){
                    
                    //fetch all items matching specific order number
                    $stmt = $pdo->prepare("SELECT Product.Name, OrderProduct.Quantity 
                    FROM OrderProduct 
                    JOIN Product ON Product.ProductID = OrderProduct.ProductID 
                    WHERE OrderProduct.OrderNum = ?");
                    $stmt->execute([$row['OrderNum']]);
                    $items = $stmt->fetchAll();

                    echo "<div class='order-card'>";
                    
                    echo "<div class='order-header'>";
                        echo "<h3> Order #: {$row['OrderNum']}</h3>";
                        echo "<span class='status-badge'>{$row['Status']}</span>";
                    echo "</div>";
                    
                    if ($row['Status'] == 'Processing') {
                        echo "<p><strong>Tracking:</strong> Not Available Until Shipped.</p>";
                    }
                    else {
                        echo "<p><strong>Tracking:</strong> {$row['TrackingNum']}</p>";
                    }

                    echo "<div class='order-details-box'>";
                        echo "<strong>Items Ordered:</strong>";
                        foreach($items as $item) {
                            echo "<p class='summary-item'>" . $item['Name'] . " <span>x" . $item['Quantity'] . "</span></p>";
                        }
                        echo "<h4 class='total-display'>Order Total: <span class='total-amount'>\${$row['PricePaid']}</span></h4>";
                    echo "</div>";

                    echo "</div>"; 

                    $sum = $sum + $row['PricePaid'];
                }

                echo "<div class='cart-summary'>";
                echo "<h3>Total Spent Across All Orders: <span class='total-amount'>\${$sum}</span></h3>";
                echo "</div>";

                echo "</div>"; 
            }
        }
        catch(PDOexception $e) {
            echo "Connection to database has failed: " . $e->getMessage();
        }
?>

</body>
</html>

<html>
    <head>
        <title>Outstanding Orders</title>
        <link rel="stylesheet" href="style.css">
    </head>

<body>
        <!-- User Nav bar for website -->
        <nav class="navbar">
            <ul>
                <li><a href="owner-inventory.php">Store Inventory</a></li>
                <li><a href="owner-outstanding.php">Outstanding Orders</a></li>
                <li><a href="owner-orderfufill.php">Order Fufillment</a></li>
                <li><form method="POST" action="logout.php">
                <button class="nav-btn" type="submit">Switch User</button>
                </form></li>
            </ul>
        </nav>
<?php
        // Login Credentials + Functions for DB
        include("functions-components.php");
        session_start();

        if (!isset($_SESSION['role']) || $_SESSION['role'] != 'employee') {
            header("Location: enter_store.php");
            exit();
        }

        try {
            // Connecting using MySql (MariaDB)
            $dsn = "mysql:host=courses;dbname=z2048942";
            $pdo = new PDO($dsn, $username, $password);
            $pdo->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);

            // outstanding orders tracker page
            echo "<div class='page-header'>";
            echo "<h2>Outstanding Orders</h2>";
            echo "<div class='title-underline'></div>";
            echo "</div>";

            $order_query = $pdo->query(
                "SELECT OrderNum 'Order Number', Name, Date, Status, TrackingNum
                FROM StoreOrder 
                JOIN Users ON StoreOrder.UserID = Users.UserID
                WHERE Status = 'Processing';"
                );

            $all_orders = $order_query->fetchALL(PDO::FETCH_ASSOC);

            if (!$all_orders) { 
                echo "<div class='table-container' style='text-align:center;'>No results found!</div>"; 
            }
            else {
                echo "<div class='table-container'>";
                draw_table($all_orders);
                echo "</div>";
            }

        }
        catch(PDOexception $e) {
            echo "Connection to database has failed: " . $e->getMessage();
        }
?>

</body>
</html>

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
                <li><a href="user_orderplaced.php">Check Order Status</a></li>
                <li><a href="owner-inventory.php">Store Inventory</a></li>
                <li><a href="owner-orderfufill.php">Order Fufillment</a></li>
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

            if (!isset($_SESSION['user_id'])) {
                $_SESSION['user_id'] = 1; // pretend user 1 is logged in
            }

            echo "<h2>Owner's Order Fufillment</h2>";

            // outstanding orders tracker page
            echo "<h3>Outstanding Orders</h3>";

            $order_query = $pdo->query(
                "SELECT OrderNum 'Order Number', Name, Date, Status
                FROM StoreOrder 
                JOIN Users ON StoreOrder.UserID = Users.UserID
                WHERE Status = 'Processing';"
                );

            $all_orders = $order_query->fetchALL(PDO::FETCH_ASSOC);

            if (!$all_orders) { echo 'No results found!'; die(); }
            else {
                draw_table($all_orders);
            }

            // view order details

            echo "<h3>View and Edit Orders</h3>";

            $order_query = $pdo->query(
                "SELECT OrderNum 'Order Number', Name, Date, Status
                FROM StoreOrder 
                JOIN Users ON StoreOrder.UserID = Users.UserID;"
                );

            $all_orders = $order_query->fetchALL(PDO::FETCH_ASSOC);

            if (!$all_orders) { echo 'No results found!'; die(); }
            else {
               echo "<table border=1 cellspacing=2>";
                //print table headers
            
                    
                //print table data
                foreach($all_orders as $row) {
                    echo "<tr>";
                    echo "<td>{$row['Order Number']}</td>";
                    echo "<td>{$row['Name']}</td>";
                    echo "<td>{$row['Date']}</td>";
                    echo "<td>{$row['Status']}</td>";
                    echo "<td><a class='btn' href='owner-orderdetail.php?orderno={$row['OrderNum']}'>View Details</a></td>";
                    echo "</tr>";
                }
                echo "</table>";  
            }
           
            
        }
        catch(PDOexception $e) {
            echo "Connection to database has failed: " . $e->getMessage();
        }
?>

</body>

</html>


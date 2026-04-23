<html>
    <head>
        <title>Home</title>
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

            $products_query = $pdo->query("SELECT Name, Price, ProductID FROM Product WHERE NumInStock >= 1");
            $products_fetch = $products_query->fetchALL(PDO::FETCH_ASSOC);

            if (!$products_fetch) { echo 'No results found!'; die(); }
            else {
                echo "<table cellspacing=2>";
                foreach($products_fetch as $row) {
                    echo "<tr>";
                    echo "<td>{$row['Name']}</td>";
                    echo "<td>{$row['Price']}</td>";
                    echo "<td><a class='btn' href='productdetail.php?id={$row['ProductID']}'>Learn More</a></td>";
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


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

            $products_query = $pdo->query("SELECT Name, Price, ProductID FROM Product WHERE NumInStock >= 1");
            $products_fetch = $products_query->fetchALL(PDO::FETCH_ASSOC);

            if (!$products_fetch) { echo 'No results found!'; die(); }
            else {

                echo "<div class='page-header'>";
                    echo "<h2>Products In Stock</h2>";
                    echo "<div class='title-underline'></div>";
                echo "</div>";

                echo "<div class='product-grid'>";
                
                foreach($products_fetch as $row) {
                    echo "<div class='product-card'>";
                        echo "<div class='product-image-wrapper'>";
                            echo "<img src='uploads/" . htmlspecialchars($row['ProductID']) . ".jpg' width='200'>";
                        echo "</div>";
                    echo "<h3 class='product-name'>{$row['Name']}</h3>";
                    echo "<p class='product-price'>\${$row['Price']}</p>";
                    echo "<a class='btn' href='productdetail.php?id={$row['ProductID']}'>Learn More</a>";
                    echo "</div>";
                }
                echo "</div>";
            }
        }

        catch(PDOexception $e) {
            echo "Connection to database has failed: " . $e->getMessage();
        }
?>

</body>

</html>


<html>
    <head>
        <title>Product Detail</title>
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

            $raw_id = $_GET['id'] ?? null;

            $id = filter_var($raw_id, FILTER_VALIDATE_INT);

            if ($id === false) {
                die("Invalid ID provided.");
            }

            $product = $pdo->prepare("SELECT * FROM Product WHERE ProductID = :id");
            $product->execute(['id' => $id]);
            $result = $product->fetch(PDO::FETCH_ASSOC);
            
            if (!$result) { echo 'No results found!'; die(); }
            else {
                echo "<div class='page-header'>";
                    echo "<h2>Product Details</h2>";
                    echo "<div class='title-underline'></div>";
                echo "</div>";

                echo "<div class='product-grid'>";
                    echo "<div class='product-card'>";
                        echo "<div class='product-image-wrapper'>";
                            echo "<img src='uploads/" . htmlspecialchars($id) . ".jpg' width='200'>";
                        echo "</div>";
                        
                        echo "<h4 class='product-name'>{$result['Name']}</h4>";
                        echo "<p>{$result['Description']}</p>";
                        echo "<p class='product-price'>\${$result['Price']}</p>";
                        echo "<p>There are currently: {$result['NumInStock']} in stock.</p>";
                        
                        echo "<form method='POST' action='cart.php'>
                        <input type='hidden' name='product_id' value='{$result['ProductID']}'>
                        <label>Qty: </label>
                        <input type='number' name='quantity' value='1' min='1' style='width:50px;'>
                        <button class='btn' type='submit' name='add_to_cart'>Add to Cart</button>
                        </form>";

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

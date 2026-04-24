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
                echo "<h4>{$result['Name']}</h4>";
                echo "<p>{$result['Description']}</p>";
                echo "<p>{$result['Price']}</p>";
                echo "<p>There are currently: {$result['NumInStock']} in stock.</p>";
                
                echo "<form method='POST' action='cart.php'>
                <input type='hidden' name='product_id' value='{$result['ProductID']}'>
                <label>Qty: </label>
                <input type='number' name='quantity' value='1' min='1' style='width:50px;'>
                <button type='submit' name='add_to_cart'>Add to Cart</button>
                </form>";

            }
        }

        catch(PDOexception $e) {
            echo "Connection to database has failed: " . $e->getMessage();
        }
?>

</body>

</html>

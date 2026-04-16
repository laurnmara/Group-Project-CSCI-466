<html>
    <head>
        <title>Default</title>
    </head>

<body>
        <!-- User Nav bar for website -->
        <nav class="navbar">
            <ul>
                <li><a href="home.php">Shop</a></li>
                <li><a href="cart.php">Cart</a></li>
                <li><a href="user-orderplaced.php">Check Order Status</a></li>
            </ul>
        </nav>
<?php
        // Login Credentials + Functions for DB
        include("functions-components.php");

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
                echo "<h4>{$result['Name']}</h4>";
                echo "<p>{$result['Description']}</p>";
                echo "<p>{$result['Price']}</p>";
            }
            
        }
        catch(PDOexception $e) {
            echo "Connection to database has failed: " . $e->getMessage();
        }
?>

</body>

</html>

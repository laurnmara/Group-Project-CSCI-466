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
        session_start();

        try {
            // Connecting using MySql (MariaDB)
            $dsn = "mysql:host=courses;dbname=z2048942";
            $pdo = new PDO($dsn, $username, $password);
            $pdo->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);


            if (isset($_POST['add_to_cart'])) {
                $id = $_POST['product_id'];
                $qty = filter_var($_POST['quantity'], FILTER_VALIDATE_INT);

                if (!isset($_SESSION['cart'])) {
                    $_SESSION['cart'] = [];
                }

                // If item is already in cart, add to the existing quantity
                if (isset($_SESSION['cart'][$id])) {
                    $_SESSION['cart'][$id] += $qty;
                } else {
                // Otherwise, set it to the chosen quantity
                $_SESSION['cart'][$id] = $qty;
                }

                print_r($_SESSION['cart']);
                
            }           
            
        }
        catch(PDOexception $e) {
            echo "Connection to database has failed: " . $e->getMessage();
        }
?>

</body>

</html>


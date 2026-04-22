<html>
    <head>
        <title>Cart</title>
        <link rel="stylesheet" href="style.css">
    </head>

<body>
        <!-- User Nav bar for website -->
        <nav class="navbar">
            <ul>
                <li><a href="home.php">Shop</a></li>
                <li><a href="cart.php">Cart</a></li>
                <li><a href="user-orderplaced.php">Check Order Status</a></li>
                <li><a href="owner-inventory.php">Store Inventory</a></li>
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

            // get user cart
                $stmt = $pdo->prepare("SELECT CartID FROM Cart WHERE UserID = ?");
                $stmt->execute([$_SESSION['user_id']]);
                $cart = $stmt->fetch();
                $cartID = $cart['CartID'];
            
            if (isset($_POST['add_to_cart'])) {

                $id = $_POST['product_id']; 
                $qty = filter_var($_POST['quantity'], FILTER_VALIDATE_INT);

                // update product qty
                $product_qty = $pdo->prepare("UPDATE Product SET NumInStock = NumInStock - ? WHERE ProductID = ?");
                $product_qty->execute([$qty,$id]);

                // insert into DB
                $stmt = $pdo->prepare("
                INSERT INTO CartProduct (CartID, ProductID, Quantity)
                VALUES (?, ?, ?)
                ON DUPLICATE KEY UPDATE Quantity = Quantity + ?
                ");

                $stmt->execute([$cartID, $id, $qty, $qty]);
                
            }
            
            echo "<h2>Your Cart</h2>";

            $stmt = $pdo->prepare("
            SELECT Product.Name, Product.Price, CartProduct.Quantity
            FROM CartProduct
            JOIN Product ON CartProduct.ProductID = Product.ProductID
            WHERE CartProduct.CartID = ?
            ");

            $sum;
            $stmt->execute([$cartID]);
            $items = $stmt->fetchAll();

            foreach ($items as $item) {
            echo $item['Name'] . " - Qty: " . $item['Quantity'] . "<br>";
            $sum = $sum + ($item['Quantity'] * $item['Price']);
            echo "<form method='POST' action='delete_product.php' onsubmit='return confirm('Are you sure?');'>
            <input type='hidden' name='product_id' value='<?php echo $item['ProductID']; ?>'>
            <button type='submit' name='delete_btn'>Delete</button>
            </form>";
            }

            echo "<h3>Your Total: </h3>" . $sum;
            
        }
        catch(PDOexception $e) {
            echo "Connection to database has failed: " . $e->getMessage();
        }
?>

</body>

</html>


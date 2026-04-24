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
                <li><a href="user_orderplaced.php">Check Order Status</a></li>
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

            $userID = $_SESSION['user_id'] = 1; // pretend user 1 is logged in

            $stmt = $pdo->prepare("SELECT CartID FROM Cart WHERE UserID = ?");
            $stmt->execute([$userID]);
            $cart = $stmt->fetch();

            if ($cart) {
                $cartID = $cart['CartID'];
            } else {
                // If no cart exists, create one
                $stmt = $pdo->prepare("INSERT INTO Cart (UserID, TotalCost) VALUES (?, 0)");
                $stmt->execute([$userID]);
                $cartID = $pdo->lastInsertId();
}
            
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
            SELECT Product.Name, Product.Price, Product.ProductID, Product.NumInStock, CartProduct.Quantity
            FROM CartProduct
            JOIN Product ON CartProduct.ProductID = Product.ProductID
            WHERE CartProduct.CartID = ?
            ");

            $stmt->execute([$cartID]);
            $items = $stmt->fetchAll();

            $sum = 0;

            // display the cart
            foreach ($items as $item) {
                echo $item['Name'];
                $sum = $sum + ($item['Quantity'] * $item['Price']);
                echo "<form method='POST' action='update_product.php' onsubmit=\"return confirm('Make Changes?');\">
                <input type='hidden' name='product_id' value='{$item['ProductID']}'>
                <label>Qty: </label>
                <input type='number' name='quantity' value='{$item['Quantity']}' min='0' max='" . ($item['NumInStock'] + $item['Quantity']) . "' style='width:50px;'>
                <button type='submit' name='update_btn'>Update</button>
                </form>";
            }

            // update total cost in DB
            $stmt = $pdo->prepare("
            UPDATE Cart 
            SET TotalCost = ? 
            WHERE CartID = ? AND UserID = ?");
            $stmt->execute([$sum, $cartID, $userID]);

            $stmt = $pdo->prepare("
            SELECT TotalCost 
            FROM Cart 
            WHERE CartID = ? AND UserID = ?");
            $stmt->execute([$cartID, $userID]);
            $fetch = $stmt->fetch();
            $totalCost = $fetch['TotalCost'];

            echo "<h3>Your Total: </h3>" . $totalCost;

            echo "<form method='POST' action='user_checkout.php'>
            <button type='submit' name='checkout_btn'>Checkout</button>
            </form>";

        }
        catch(PDOexception $e) {
            echo "Connection to database has failed: " . $e->getMessage();
        }
?>

</body>

</html>


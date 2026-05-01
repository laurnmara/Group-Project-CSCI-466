<html>
    <head>
        <title>Cart</title>
        <link rel="stylesheet" href="style.css">
    </head>

<body>
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
        include("functions-components.php");
        session_start();

        if (!isset($_SESSION['role']) || $_SESSION['role'] != 'customer') {
            header("Location: enter_store.php");
            exit();
        }

        $userID = $_SESSION['user_id'];

        try {
            $dsn = "mysql:host=courses;dbname=z2048942";
            $pdo = new PDO($dsn, $username, $password);
            $pdo->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);

            $stmt = $pdo->prepare("SELECT CartID FROM Cart WHERE UserID = ?");
            $stmt->execute([$userID]);
            $cart = $stmt->fetch();

            $cartID = $cart['CartID'];
            
            if (isset($_POST['add_to_cart'])) {
                $id = $_POST['product_id']; 
                $qty = filter_var($_POST['quantity'], FILTER_VALIDATE_INT);

                $product_qty = $pdo->prepare("UPDATE Product SET NumInStock = NumInStock - ? WHERE ProductID = ?");
                $product_qty->execute([$qty,$id]);

                $stmt = $pdo->prepare("
                INSERT INTO CartProduct (CartID, ProductID, Quantity)
                VALUES (?, ?, ?)
                ON DUPLICATE KEY UPDATE Quantity = Quantity + ?
                ");
                $stmt->execute([$cartID, $id, $qty, $qty]);
            }
            
            echo "<div class='page-header'>";
                echo "<h2>Your Shopping Cart</h2>";
                echo "<div class='title-underline'></div>";
            echo "</div>";

            $stmt = $pdo->prepare("
            SELECT Product.Name, Product.Price, Product.ProductID, Product.NumInStock, CartProduct.Quantity
            FROM CartProduct
            JOIN Product ON CartProduct.ProductID = Product.ProductID
            WHERE CartProduct.CartID = ?
            ");
            $stmt->execute([$cartID]);
            $items = $stmt->fetchAll();

            $sum = 0;

            echo "<div class='cart-container'>";
    
            if (count($items) > 0) {
                foreach ($items as $item) {
                    $sum += ($item['Quantity'] * $item['Price']);
                    
                    echo "<div class='cart-item'>";
                        echo "<div class='item-info'>";
                            echo "<h4>" . htmlspecialchars($item['Name']) . "</h4>";
                            echo "<p>$" . number_format($item['Price'], 2) . "</p>";
                        echo "</div>";

                        echo "<form class='cart-update-form' method='POST' action='update_product.php' onsubmit=\"return confirm('Update quantity?');\">";
                            echo "<input type='hidden' name='product_id' value='{$item['ProductID']}'>";
                            echo "<label>Qty:</label>";
                            echo "<input type='number' class='cart-qty-input' name='quantity' value='{$item['Quantity']}' min='0' max='{$item['NumInStock']}'>";
                            echo "<button type='submit' name='update_btn' class='btn update-btn-small'>Update</button>";
                        echo "</form>";
                    echo "</div>";
                }

                echo "<div class='cart-summary'>";
                    echo "<h3>Total: <span style='color:#c23f3f;'>$" . number_format($sum, 2) . "</span></h3>";
                    echo "<form method='POST' action='user_checkout.php'>";
                    echo "<button type='submit' name='checkout_btn' class='btn checkout-btn'>Proceed to Checkout</button>";
                    echo "</form>";
                echo "</div>";
            } else {
                echo "<p style='text-align:center; padding: 40px;'>Your cart is empty. <a href='home.php'>Go Shopping</a></p>";
            }   

            echo "</div>";

            $stmt = $pdo->prepare("UPDATE Cart SET TotalCost = ? WHERE CartID = ? AND UserID = ?");
            $stmt->execute([$sum, $cartID, $userID]);

        }
        catch(PDOexception $e) {
            echo "Connection to database has failed: " . $e->getMessage();
        }
?>
</body>
</html>

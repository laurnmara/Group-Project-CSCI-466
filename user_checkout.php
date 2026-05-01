<html>
    <head>
        <title>Checkout</title>
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

            if(isset($_POST['checkout_btn'])){

                $stmt = $pdo->prepare("SELECT CartID FROM Cart WHERE UserID = ?");
                $stmt->execute([$userID]);
                $cart = $stmt->fetch();
                $cartID = $cart['CartID'];

                echo "<div class='page-header'>";
                echo "<h2>Checkout</h2>";
                echo "<div class='title-underline'></div>";
                echo "</div>";

                echo "<div class='cart-container'>";

                echo "<h3>Order Summary</h3>";
                echo "<div class='cart-summary summary-list'>";

                $stmt = $pdo->prepare("
                SELECT Product.Name, CartProduct.Quantity, Cart.TotalCost
                FROM CartProduct
                JOIN Product ON CartProduct.ProductID = Product.ProductID
                JOIN Cart ON CartProduct.CartID = Cart.CartID
                WHERE CartProduct.CartID = ?
                ");

                $stmt->execute([$cartID]);
                $items = $stmt->fetchAll();

                foreach($items as $item) {
                    echo "<p class='summary-item'>" . $item['Name'] . " <span>x" . $item['Quantity'] . "</span></p>";
                }

                echo "<h4 class='total-display'>Total: <span class='total-amount'>\${$item['TotalCost']}</span></h4>";
                echo "</div>";

                $stmt = $pdo->prepare("SELECT Users.Name, Users.Email, Users.PhoneNum, 
                Payment.PaymentID, StoreOrder.ShipAddr, StoreOrder.BillAddr 
                FROM StoreOrder
                JOIN Users ON Users.UserID = StoreOrder.UserID
                JOIN Payment ON Payment.UserID = StoreOrder.UserID
                WHERE StoreOrder.CartID = ?
                AND StoreOrder.UserID = ?");
            
                $stmt->execute([$cartID, $userID]);
                $checkout = $stmt->fetch();
                
                echo "<form method='POST' action='user_orderplaced.php'>";
                echo "<input type='hidden' name='totalCost' value='{$item['TotalCost']}'>";
                echo "<input type='hidden' name='cartID' value='{$cartID}'>";
                
                echo "<div class='checkout-grid'>";
                
                echo "<div class='checkout-section'>";
                echo "<h3>Contact information</h3>";
                echo "<div class='checkout-field'>";
                echo "<label>Name: </label>";
                echo "<input type='text' class='nice-select' name='Name' value='{$checkout['Name']}' required>";
                echo "</div>";
                echo "<div class='checkout-field'>";
                echo "<label>Email: </label>";
                echo "<input type='text' class='nice-select' name='Email' value='{$checkout['Email']}' required>";
                echo "</div>";
                echo "<div class='checkout-field'>";
                echo "<label>Phone Number: </label>";
                echo "<input type='text' class='nice-select' name='PhoneNum' value='{$checkout['PhoneNum']}' required>";
                echo "</div>";
                echo "</div>";

                echo "<div class='checkout-section'>";
                echo "<h3>Payment & Shipping</h3>";
                echo "<div class='checkout-field'>";
                echo "<h2>Payment Information: </h2>";
                echo "<label>Card Number: </label>";
                echo "<input type='text' class='nice-select' name='CardNum' value='{$checkout['PaymentID']}' required>";
                echo "</div>";
                echo "<div class='checkout-field'>";
                echo "<label>Billing Address: </label>";
                echo "<input type='text' class='nice-select' name='BillingAddress' value='{$checkout['BillAddr']}' required>";
                echo "</div>";
                echo "<div class='checkout-field'>";
                echo "<h2>Shipping address: </h2>";
                echo "<label>Address: </label>";
                echo "<input type='text' class='nice-select' name='Address' value='{$checkout['ShipAddr']}' required>";
                echo "</div>";
                echo "</div>";
                
                echo "</div>";

                echo "<div class='checkout-footer'>";
                echo "<a href='cart.php' class='back-link'>&lt; Go Back</a>";
                echo "<button type='submit' class='btn checkout-btn' name='place_order'>Place Order</button>";
                echo "</div>";
                echo "</form>";
                echo "</div>";
            }  
        }
        catch(PDOexception $e) {
            echo "Connection to database has failed: " . $e->getMessage();
        }
?>

</body>

</html>

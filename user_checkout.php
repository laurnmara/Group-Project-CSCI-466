<html>
    <head>
        <title>Default</title>
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

            if(isset($_POST['checkout_btn'])){

                $stmt = $pdo->prepare("SELECT CartID FROM Cart WHERE UserID = ?");
                $stmt->execute([$userID]);
                $cart = $stmt->fetch();
                $cartID = $cart['CartID'];

                echo "<h2>Checkout</h2><br>";

                echo "<h3>Order Summary</h3>";

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
                    echo $item['Name'] . " x" . $item['Quantity'] . "<br>";
                }

                echo "<h4>Total: \${$item['TotalCost']}</h4>";

                $stmt = $pdo->prepare("SELECT Users.Name, Users.Email, Users.PhoneNum, 
                Payment.PaymentID, StoreOrder.ShipAddr, StoreOrder.BillAddr 
                FROM StoreOrder
                JOIN Users ON Users.UserID = StoreOrder.UserID
                JOIN Payment ON Payment.UserID = StoreOrder.UserID
                WHERE StoreOrder.CartID = ?
                AND StoreOrder.UserID = ?");
            
                $stmt->execute([$cartID, $userID]);
                $checkout = $stmt->fetch();
                
                echo "<h2>Contact information: </h2>";

                echo "<form method='POST' action='user_orderplaced.php'>
                <input type='hidden' name='totalCost' value='{$item['TotalCost']}'>
                <input type='hidden' name='cartID' value='{$cartID}'>
                <label>Name: </label><br>
                <input type='text' name='Name' value='{$checkout['Name']}'><br><br>
                <label>Email: </label><br>
                <input type='text' name='Email' value='{$checkout['Email']}'><br><br>
                <label>Phone Number: </label><br>
                <input type='text' name='PhoneNum' value='{$checkout['PhoneNum']}'><br><br>
                <h2>Payment Information: </h2><br>
                <label>Card Number: </label><br>
                <input type='text' name='CardNum' value='{$checkout['PaymentID']}'><br><br>
                <label>Billing Address: </label><br>
                <input type='text' name='BillingAddress' value='{$checkout['BillAddr']}' style='width:300px'><br><br>
                <h2>Shipping address: </h2><br>
                <label>Address: </label><br>
                <input type='text' name='Address' value='{$checkout['ShipAddr']}' style='width:300px'><br><br>
                <a href='cart.php' style='padding:50px'>< Go Back</a>
                <button type='submit' name='place_order'>Place Order</button>
                </form>";
            }  
        }
        catch(PDOexception $e) {
            echo "Connection to database has failed: " . $e->getMessage();
        }
?>

</body>

</html>

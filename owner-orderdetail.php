<html>
    <head>
        <title>Order Detail</title>
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

        try {
            // Connecting using MySql (MariaDB)
            $dsn = "mysql:host=courses;dbname=z2048942";
            $pdo = new PDO($dsn, $username, $password);
            $pdo->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);

            //getting order information
            $info_stmt = $pdo->prepare(
                "SELECT * FROM StoreOrder
                JOIN Users ON StoreOrder.UserID = Users.UserID
                WHERE OrderNum = ?;"
            );
            $info_stmt->execute(array($_GET["orderno"]));
            $info_row = $info_stmt->fetch(PDO::FETCH_ASSOC);

            if(!$info_row) { echo "No Orders Found!"; die(); }
            else {

                //displaying order information
                echo "<h2>View Order # {$info_row['OrderNum']} 📦</h2>";
                echo "<h3>(Cart Number:  {$info_row['CartID']}) 🛒 </h3>";

                echo "<h3>Name: {$info_row['Name']} ||  User ID: {$info_row['UserID']}</h3>";

                echo "<h4>Contact User</h4>";
                echo "<p>Email: {$info_row['Email']} || Phone Number: {$info_row['PhoneNum']}</p>";

                echo "<h4>Order Information</h4>";
                echo "<p>Status: {$info_row['Status']}</p>";
                echo "<p>Date Ordered: {$info_row['Date']}</p>";
                echo "<p>Order Cost: {$info_row['PricePaid']} || Payment: {$info_row['PaymentID']}</p>";

                echo "<h4>Saved Addresses</h4>";
                echo "<p>Shipping Address: {$info_row['ShipAddr']}</p>";
                echo "<p>Billing Address: {$info_row['BillAddr']}</p>";
                echo "</br>";

                echo "<h3>Notes:</h3>";
                echo "<p>{$info_row['Notes']}</p>";
            }

            //submit new note -- not working yet!
            echo "</br>";
            echo "<form action='owner-orderdetail.php?orderno={$info_row['OrderNum']}&note_update={$_GET['note_update']}' method='GET'>";
                echo "Type in new note for {$info_row['Name']}: ";
                echo '<input type="text" name="note_update"/>'. '</br>';
                echo '<input type="submit" value="Update Note" class="btn" />';
            echo '</form>';

            //processing order edit
            $note_stmt = $pdo->prepare(
                "UPDATE StoreOrder
                 SET Notes = ?
                 WHERE OrderNum = ?;"
                );
            $note_stmt->execute(array($_GET['note_update'], $_GET['orderno']));
            if(!$note_stmt) { echo "Invalid Query!"; die(); }
            
        }
        catch(PDOexception $e) {
            echo "Connection to database has failed: " . $e->getMessage();
        }
?>

</body>

</html>


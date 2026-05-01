<html>
    <head>
        <title>Order Detail</title>
        <link rel="stylesheet" href="style.css">
    </head>

<body>
        <!-- User Nav bar for website -->
        <nav class="navbar">
            <ul>
                <li><a href="owner-inventory.php">Store Inventory</a></li>
                <li><a href="owner-outstanding.php">Outstanding Orders</a></li>
                <li><a href="owner-orderfufill.php">Order Fufillment</a></li>
                <li><form method="POST" action="logout.php">
                <button class="nav-btn" type="submit">Switch User</button>
                </form></li>
            </ul>
        </nav>
<?php
        // Login Credentials + Functions for DB
        include("functions-components.php");
        session_start();

        if (!isset($_SESSION['role']) || $_SESSION['role'] != 'employee') {
            header("Location: enter_store.php");
            exit();
        }

        try {
            // Connecting using MySql (MariaDB)
            $dsn = "mysql:host=courses;dbname=z2048942";
            $pdo = new PDO($dsn, $username, $password);
            $pdo->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);

            if (isset($_POST['note_update'])) {
                $note_stmt = $pdo->prepare("UPDATE StoreOrder SET Notes = ? WHERE OrderNum = ?");
                $note_stmt->execute([$_POST['note_update'], $_POST['orderno']]);

                header("Location: owner-orderdetail.php?orderno=" . $_POST['orderno']);
                exit();
            }

            //getting order information
            $info_stmt = $pdo->prepare(
                "SELECT * FROM StoreOrder
                JOIN Users ON StoreOrder.UserID = Users.UserID
                WHERE OrderNum = ?;"
            );
            $info_stmt->execute(array($_GET["orderno"]));
            $info_row = $info_stmt->fetch(PDO::FETCH_ASSOC);

            if(!$info_row) { 
                echo "<div class='page-header'><h2>No Orders Found!</h2></div>"; 
                die(); 
            }
            else {
                echo "<div class='page-header'>";
                echo "<h2>Order # {$info_row['OrderNum']} Details</h2>";
                echo "<div class='title-underline'></div>";
                echo "</div>";

                echo "<div class='cart-container'>";
                
                echo "<div class='order-header'>";
                echo "<h3>{$info_row['Name']} (ID: {$info_row['UserID']})</h3>";
                echo "<span class='status-badge'>{$info_row['Status']}</span>";
                echo "</div>";

                echo "<div class='order-details-box'>";
                echo "<h4>Contact Info</h4>";
                echo "<p><strong>Email:</strong> {$info_row['Email']}</p>";
                echo "<p><strong>Phone:</strong> {$info_row['PhoneNum']}</p>";
                echo "</div>";

                echo "<div class='order-details-box'>";
                echo "<h4>Order Info</h4>";
                echo "<p><strong>Date:</strong> {$info_row['Date']}</p>";
                echo "<p><strong>Cart #:</strong> {$info_row['CartID']}</p>";
                echo "<p><strong>Payment ID:</strong> {$info_row['PaymentID']}</p>";
                echo "<h4 class='total-display'>Order Cost: <span class='total-amount'>\${$info_row['PricePaid']}</span></h4>";
                echo "</div>";

                echo "<div class='order-details-box'>";
                echo "<h4>Addresses</h4>";
                echo "<p><strong>Shipping:</strong> {$info_row['ShipAddr']}</p>";
                echo "<p><strong>Billing:</strong> {$info_row['BillAddr']}</p>";
                echo "</div>";

                echo "<div class='order-details-box' style='background: #fff9f9; border: 1px solid #ffecec;'>";
                echo "<h3>Notes:</h3><p>" . (htmlspecialchars($info_row['Notes']) ?: "<em>No notes available.</em>") . "</p>";
                echo "</div>";

                echo "<form action='owner-orderdetail.php?orderno={$info_row['OrderNum']}' method='POST' style='margin-top: 30px;'>";
                echo "<h3>Update Notes</h3>";
                echo "<div class='checkout-field'>";
                echo "<input type='hidden' name='orderno' value='{$info_row['OrderNum']}'>";
                echo "<input type='text' class='nice-select' name='note_update' placeholder='Type in new note for {$info_row['Name']}...' required />";
                echo "</div>";
                echo "<input type='submit' value='Update Note' class='btn' style='width: 100%;' />";
                echo "</form>";

                echo "<div class='checkout-footer' style='margin-top: 20px;'>";
                echo "<a href='owner-orderfufill.php' class='back-link'>&lt; Back to Fufillment</a>";
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

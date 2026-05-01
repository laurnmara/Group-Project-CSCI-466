<html>
    <head>
        <title>Store Inventory</title>
        <link rel="stylesheet" href="style.css">
    </head>

<body>
        <!-- User Nav bar for website -->
        <nav class="navbar">
            <ul>
                <li><a href="owner-inventory.php">Store Inventory</a></li>
                <li><a href="owner-orderfufill.php">Order Fufillment</a></li>
                <li><form method="POST" action="logout.php">
                <button type="submit">Switch User</button>
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

            if (isset($_POST['update_btn'])) {
                $stmt = $pdo->prepare("UPDATE Product SET NumInStock = ? WHERE ProductID = ?;");
                $stmt->execute([$_POST['quantity'], $_POST['product_id']]);
            }

            // Get all items in inventory & quantity
            echo "<h2>Owner Inventory - All Items in Store</h2>";
            $products_query = $pdo->query("SELECT * FROM Product;");
            $products_fetch = $products_query->fetchALL(PDO::FETCH_ASSOC);

            if (!$products_fetch) { echo 'No results found!'; die(); }
            else {
                echo "<table cellspacing=2>";
                foreach($products_fetch as $row) {
                    echo "<tr>";
                    echo "<td>{$row['ProductID']}</td>";
                    echo "<td>{$row['Name']}</td>";
                    echo "<td>{$row['Description']}</td>";
                    echo "<td>{$row['Price']}</td>";          
                    echo "<td><form method='POST' action='owner-inventory.php' onsubmit=\"return confirm('Make Changes?');\">
                    <input type='hidden' name='product_id' value='{$row['ProductID']}'>
                    <label>Qty: </label>
                    <input type='number' name='quantity' value='{$row['NumInStock']}' min='0' style='width:100px;'>
                    <button type='submit' name='update_btn'>Update</button>
                    </form></td>";                   
                    echo "</tr>";
                }
                echo "</table>";
            }
            
        }
        catch(PDOexception $e) {
            echo "Connection to database has failed: " . $e->getMessage();
        }
?>

</body>

</html>


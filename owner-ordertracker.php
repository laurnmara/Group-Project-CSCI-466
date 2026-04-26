<html>
    <head>
        <title>Order Tracking</title>
        <link rel="stylesheet" href="style.css">
    </head>

<body>
        <!-- User Nav bar for website -->
        <nav class="navbar">
            <ul>
                <li><a href="owner-inventory.php">Store Inventory</a></li>
                <li><a href="owner-orderdetail.php">Order Detail</a></li>
                <li><a href="owner-orderfufill.php">Order Fufillment</a></li>
                <li><a href="owner-ordertracker.php">Order Tracker</a></li>
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

            
        }
        catch(PDOexception $e) {
            echo "Connection to database has failed: " . $e->getMessage();
        }
?>

</body>

</html>


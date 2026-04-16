<html>
    <head>
        <title>Home</title>
        <link rel="stylesheet" href="style.css">
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

        $url = "productdetail.php";

        try {
            // Connecting using MySql (MariaDB)
            $dsn = "mysql:host=courses;dbname=z2048942";
            $pdo = new PDO($dsn, $username, $password);
            $pdo->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);

            $products_query = $pdo->query("SELECT Name, Price FROM Product WHERE NumInStock >= 1");
            $products_fetch = $products_query->fetchALL(PDO::FETCH_ASSOC);

            if (!$products_fetch) { echo 'No results found!'; die(); }
            else {
            echo "<table cellspacing=2>";
                foreach($products_fetch as $key => $row) {
                    echo "<tr>";
                    foreach($row as $item) {
                    echo "<td>$item</td>";
                }
                echo "<td><a href='$url' class='btn'>Learn More</a></td>";
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


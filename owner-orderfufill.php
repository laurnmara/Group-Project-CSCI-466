<html>
    <head>
        <title>Order Fufillment</title>
        <link rel="stylesheet" href="style.css">
    </head>

<body>
        <!-- User Nav bar for website -->
        <nav class="navbar">
            <ul>
                <li><a href="home.php">Shop</a></li>
                <li><a href="cart.php">Cart</a></li>
                <li><a href="track_order.php">Check Order Status</a></li>
                <li><a href="owner-inventory.php">Store Inventory</a></li>
                <li><a href="owner-orderfufill.php">Order Fufillment</a></li>
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

            if (isset($_GET['orderno'], $_GET['order_status'])) {
                //processing order edit
                $order_stmt = $pdo->prepare(
                "UPDATE StoreOrder
                SET Status = :new_status
                WHERE OrderNum = :ordnum;"
                );

                $order_stmt->execute(array(
                ":new_status" => $_GET["order_status"], 
                ":ordnum" => $_GET["orderno"]
                ));

                if(!$order_stmt) { echo "Invalid Query!"; die(); }

                //inserting new Tracking Number if status was changed to Delivered or Shipped
                if($_GET["order_status"] != "Processing") {

                    // FIX LOGIC FOR INCREMENTING TRK NUMBER
                    // //creating new tracking num || note: tracking num in TRKXXXX format
                    $track_query = $pdo->query("SELECT MAX(TrackingNum) FROM StoreOrder;");
                    $old_tracknum = $track_query->fetchColumn(); 

                    if ($old_tracknum) { 
                        $num_part = substr($old_tracknum, 3); 
                        
                        $new_tracknum = intval($num_part) + 1;
                        $new_tracknum = 'TRK' . $new_tracknum;
                    } else { 
                        // If table is empty or all TrackingNums are NULL
                        $new_tracknum = "TRK1001"; 
                    }

                    $status_stmt = $pdo->prepare(
                        "UPDATE StoreOrder
                        SET TrackingNum = :trknum
                        WHERE OrderNum = :ordnum;"
                        );
                    $status_stmt->execute(array(
                        ":trknum" => $new_tracknum,
                        ":ordnum" => $_GET["orderno"]
                        ));
                    if(!$status_stmt) { echo "Invalid Query!"; die(); }
                }
                else { 

                    //if order status is Delivered or Shipped, remove tracking number
                    $trk_remove = $pdo->prepare(
                        "UPDATE StoreOrder
                        SET TrackingNum = null
                        WHERE OrderNum = ?"
                    );
                    $trk_remove->execute(array($_GET["orderno"]));
                    if(!$trk_remove) { echo "Tracking Number could not be removed!"; die(); }
                }

            }

            echo "<h2>Owner's Order Fufillment</h2>";

            // outstanding orders tracker page
            echo "<h3>Outstanding Orders</h3>";

            $order_query = $pdo->query(
                "SELECT OrderNum 'Order Number', Name, Date, Status, TrackingNum
                FROM StoreOrder 
                JOIN Users ON StoreOrder.UserID = Users.UserID
                WHERE Status = 'Processing';"
                );

            $all_orders = $order_query->fetchALL(PDO::FETCH_ASSOC);

            if (!$all_orders) { echo 'No results found!'; }
            else {
                draw_table($all_orders);
            }

            // view order details
            echo "<h3>View and Edit Orders</h3>";

            $order_query = $pdo->query(
                "SELECT OrderNum 'Order Number', Name, Date, Status, TrackingNum
                FROM StoreOrder 
                JOIN Users ON StoreOrder.UserID = Users.UserID;"
                );

            $all_orders = $order_query->fetchALL(PDO::FETCH_ASSOC);

            if (!$all_orders) { echo 'No results found!'; die(); }
            else {
               echo "<table border=1 cellspacing=2>";
                    
                //print table data
                foreach($all_orders as $row) {
                    echo "<tr>";
                    echo "<td>{$row['Order Number']}</td>";
                    echo "<td>{$row['Name']}</td>";
                    echo "<td>{$row['Date']}</td>";
                    echo "<td>{$row['Status']}</td>";
                    echo "<td>{$row['TrackingNum']}</td>";

                    echo "<td><a class='btn' href='owner-orderdetail.php?orderno={$row['Order Number']}'>View Details</a></td>";
                    echo "</tr>";
                }
                echo "</table>";  
                echo "</br>" . "</br>";
            }

            //edit order logic
            echo '<form action="owner-orderfufill.php" method="GET">';

                echo 'Type in the Order Number to Edit: ';
                echo '<input type="number" name="orderno" min="1" max="9999" />'. '</br>';

                echo 'Edit Order Status: ';
                echo '<select name="order_status">';
                        echo '<option value="Processing">Processing</option>';
                        echo '<option value="Shipped">Shipped</option>';
                        echo '<option value="Delivered">Delivered</option>';
                echo '</select>' . '<br>';

                echo '<input type="submit" value="Update Order" class="btn" />';
            echo '</form>';
            
        }
        catch(PDOexception $e) {
            echo "Connection to database has failed: " . $e->getMessage();
        }
?>

</body>

</html>


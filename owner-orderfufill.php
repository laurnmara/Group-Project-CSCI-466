<html>
    <head>
        <title>Order Fufillment</title>
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

            if (isset($_POST['orderno'], $_POST['order_status'])) {
                //processing order edit
                $order_stmt = $pdo->prepare(
                "UPDATE StoreOrder
                SET Status = :new_status
                WHERE OrderNum = :ordnum;"
                );

                $order_stmt->execute(array(
                ":new_status" => $_POST["order_status"], 
                ":ordnum" => $_POST["orderno"]
                ));

                if(!$order_stmt) { echo "Invalid Query!"; die(); }

                //inserting new Tracking Number if status was changed to Delivered or Shipped
                if($_POST["order_status"] != "Processing") {

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
                        ":ordnum" => $_POST["orderno"]
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
                    $trk_remove->execute(array($_POST["orderno"]));
                    if(!$trk_remove) { echo "Tracking Number could not be removed!"; die(); }
                }

            }

            echo "<div class='page-header'>";
            echo "<h2>Owner's Order Fufillment</h2>";
            echo "<div class='title-underline'></div>";
            echo "</div>";

            // view order details
            echo "<div class='table-container'>";
            echo "<h3>View and Edit Orders</h3>";

            $order_query = $pdo->query(
                "SELECT OrderNum 'Order Number', Name, Date, Status, TrackingNum
                FROM StoreOrder 
                JOIN Users ON StoreOrder.UserID = Users.UserID;"
                );

            $all_orders = $order_query->fetchALL(PDO::FETCH_ASSOC);

            if (!$all_orders) { echo 'No results found!'; die(); }
            else {
               echo "<table>";
                    echo "<thead><tr><th>Order #</th><th>Name</th><th>Date</th><th>Status</th><th>Tracking</th><th>Action</th></tr></thead>";
                //print table data
                foreach($all_orders as $row) {
                    echo "<tr>";
                    echo "<td>{$row['Order Number']}</td>";
                    echo "<td>{$row['Name']}</td>";
                    echo "<td>{$row['Date']}</td>";
                    echo "<td><span class='status-badge'>{$row['Status']}</span></td>";
                    echo "<td>" . ($row['TrackingNum'] ?? 'N/A') . "</td>";

                    echo "<td><a class='btn update-btn-small' href='owner-orderdetail.php?orderno={$row['Order Number']}'>Details</a></td>";
                    echo "</tr>";
                }
                echo "</table>";  
            }
            echo "</div>";

            //edit order logic
            echo "<div class='cart-container' style='max-width: 600px;'>";
            echo "<h3>Update Order Status</h3>";
            echo '<form action="owner-orderfufill.php" method="POST">';

                echo '<div class="checkout-field">';
                echo '<label>Order Number:</label>';
                echo '<input type="number" class="nice-select" name="orderno" min="1" max="9999" required />';
                echo '</div>';

                echo '<div class="checkout-field">';
                echo '<label>Set Status:</label>';
                echo '<div class="select-wrapper">';
                echo '<select class="nice-select" name="order_status">';
                        echo '<option value="Processing">Processing</option>';
                        echo '<option value="Shipped">Shipped</option>';
                        echo '<option value="Delivered">Delivered</option>';
                echo '</select></div></div>';

                echo '<input type="submit" value="Update Order" class="btn" style="width: 100%;" />';
            echo '</form></div>';
            
        }
        catch(PDOexception $e) {
            echo "Connection to database has failed: " . $e->getMessage();
        }
?>

</body>

</html>

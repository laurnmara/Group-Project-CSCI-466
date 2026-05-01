<html>
    <head>
        <title> Enter Store </title>
        <link rel="stylesheet" href="style.css">
    </head>
<body>

<?php
    include("functions-components.php");    
    session_start();

    if (isset($_POST['enter'])) {
        $_SESSION['user_id'] = $_POST['user_id'];
        $_SESSION['role'] = $_POST['role'];

        if ($_POST['role'] == 'employee') {
            header("Location: owner-inventory.php");
        } else {
            header("Location: home.php");
        }
        exit();
    }

    try {
        // Connecting using MySql (MariaDB)
        $dsn = "mysql:host=courses;dbname=z2048942";
        $pdo = new PDO($dsn, $username, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);

        $stmt = $pdo->query("SELECT * FROM Users;");
        $stmt->execute;

    }

    catch(PDOexception $e) {
        echo "Connection to database has failed: " . $e->getMessage();
    }

?>
<div class="login-wrapper">
    <div class="login-card">
        <div class="login-left">
            <h2>Enter Store</h2>
                <form method="POST">
                    <label>User:</label>
                    <div class="select-wrapper">
                        <select class="nice-select" name="user_id">
                            <?php
                                while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                    echo '<option value="' . $row['UserID'] . '">' . htmlspecialchars($row['Name']) . '</option>';
                                }
                            ?>
                        </select>
                    </div>
                        <br><br>

                        <label>Role:</label>
                    <div class="select-wrapper">
                        <select class="nice-select" name="role">
                            <option value="customer">Customer</option>
                            <option value="employee">Employee</option>
                        </select>
                    </div>

                        <br><br>

                        <button class='btn' type="submit" name="enter">Enter</button>
                        </form> 
            </div>
        <div class="login-right">
            <!-- You can put an <img> here or use a CSS background image -->
        </div>
    </div>
</div>

</body>
</html>
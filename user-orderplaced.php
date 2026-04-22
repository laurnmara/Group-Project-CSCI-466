<html>
    <head>
        <title>Default</title>
        <link rel="stylesheet" href="style.css">
    </head>

<body>
<?php
        // Login Credentials + Functions for DB
        include("functions-components.php");

        try {
            // Connecting using MySql (MariaDB)
            $dsn = "mysql:host=courses;dbname=z2020678";
            $pdo = new PDO($dsn, $username, $password);
            $pdo->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);

            
        }
        catch(PDOexception $e) {
            echo "Connection to database has failed: " . $e->getMessage();
        }
?>

</body>

</html>

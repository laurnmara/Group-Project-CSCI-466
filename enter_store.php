<html>
    <head>
        <title> Enter Store </title>
    </head>
<body>

<?php
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
?>

<h2>Enter Store</h2>

<form method="POST">
    <label>User:</label>
    <select name="user_id">
        <option value="1">Alice</option>
        <option value="2">Bob</option>
        <option value="3">Charlie</option>
        <option value="4">Diana</option>
        <option value="5">Ethan</option>
    </select>

    <br><br>

    <label>Role:</label>
    <select name="role">
        <option value="customer">Customer</option>
        <option value="employee">Employee</option>
    </select>

    <br><br>

    <button type="submit" name="enter">Enter</button>
</form> 

</body>
</html>
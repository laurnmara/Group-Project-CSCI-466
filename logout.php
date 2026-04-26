<?php
session_start();
session_destroy();
header("Location: enter_store.php");
exit();
?>
<?php
session_start();
session_destroy();
//echo 'You have been logged out. <a href="../index.php">Go back</a>';
//redirect to homepage
header("Location: index.php");
exit();
?>
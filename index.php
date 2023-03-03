<?php
session_start();

// Check if the user is already logged in
if (isset($_SESSION['user_id'])) {
  header('Location: dashboard.php');
  exit();
}

?>

<!DOCTYPE html>
<html>
<head>
  <title>My Website</title>
</head>
<body>

  <h1>Welcome to my website!</h1>

  <p>Please login or register to continue.</p>

  <ul>
    <li><a href="login.php">Login</a></li>
    <li><a href="register.php">Register</a></li>
  </ul>

</body>
</html>

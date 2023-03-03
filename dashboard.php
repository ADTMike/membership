<?php
session_start();

// Check if the user is not logged in
if (!isset($_SESSION['user_id'])) {
  header('Location: index.php');
  exit();
}

?>

<!DOCTYPE html>
<html>
<head>
  <title>Dashboard</title>
</head>
<body>

  <h1>Dashboard</h1>

  <p>Welcome <?php echo $_SESSION['user_id']; ?>!</p>

  <a href="logout.php">Logout</a>

</body>
</html>

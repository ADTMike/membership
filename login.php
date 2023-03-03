<?php
session_start();

// Check if the user is already logged in
if (isset($_SESSION['user_id'])) {
  header('Location: dashboard.php');
  exit();
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  // Validate form fields
  $emailOrPhone = trim($_POST['email_or_phone']);
  $password = $_POST['password'];

  $errors = array();

  if (empty($emailOrPhone)) {
    $errors["username"] = 'Please enter your email or phone number.';
  }

  if (empty($password)) {
    $errors["username"] = 'Please enter your password.';
  }

  // If there are no validation errors, authenticate the user
  if (empty($errors)) {
    // Connect to the database using PDO
    $dsn = 'mysql:host=localhost;dbname=database';
    $username = 'root';
    $password = 'pass';

    try {
      $pdo = new PDO($dsn, $username, $password);
      $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

      // Prepare the SELECT statement
      $stmt = $pdo->prepare('SELECT * FROM users WHERE email = :emailOrPhone OR phone = :emailOrPhone');

      // Bind the parameters and execute the statement
      $stmt->bindParam(':emailOrPhone', $emailOrPhone);
      $stmt->execute();

      // Check if a user with the given email or phone number exists
      $user = $stmt->fetch(PDO::FETCH_ASSOC);

      if (!$user) {
        $errors["username"] = 'Invalid email or phone number.';
      } else if (!password_verify($password, $user['password'])) {
        $errors["password"] = 'Incorrect password.';
      } else {
        // Authentication succeeded, set the user ID in the session and redirect to the dashboard
        $_SESSION['user_id'] = $user['id'];
        header('Location: dashboard.php');
        exit();
      }
    } catch (PDOException $e) {
      echo 'Connection failed: ' . $e->getMessage();
    }
  }
}

?>

<!DOCTYPE html>
<html>
<head>
  <title>Login</title>
</head>
<body>

  <h1>Login</h1>

  <p>Please enter your email or phone number and password.</p>

  <?php if (!empty($errors)): ?>
    <ul>
      <?php foreach ($errors as $error): ?>
        <li><?php echo $error; ?></li>
      <?php endforeach; ?>
    </ul>
  <?php endif; ?>

  <form method="post" action="">
    <label for="email_or_phone">Email or Phone:</label>
    <input type="text" id="email_or_phone" name="email_or_phone" value="<?php echo htmlspecialchars($emailOrPhone ?? ''); ?>">

    <br>

    <label for="password">Password:</label>
    <input type="password" id="password" name="password">

    <br>

    <button type="submit">Login</button>
  </form>

</body>
</html>

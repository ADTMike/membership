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
	$name = trim($_POST['name']);
	$email = trim($_POST['email']);
	$phone = trim($_POST['phone']);
	$password = $_POST['password'];
	$password2 = $_POST['password2'];
  
	$errors = array();
  
	if (empty($name)) {
	  $errors["name"] = 'Please enter your name.';
	} else if (!preg_match('/^[a-zA-Z ]+$/', $name)) {
	  $errors["name"] = 'Your name should contain only letters and spaces.';
	}
  
	if (empty($email)) {
	  $errors["email"] = 'Please enter your email address.';
	} else if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
	  $errors["email"] = 'Please enter a valid email address.';
	}
  
	if (empty($phone)) {
	  $errors["phone"] = 'Please enter your phone number.';
	} else if (!ctype_digit($phone) || strlen($phone) < 7 || strlen($phone) > 14) {
	  $errors["phone"] = 'Please enter a valid phone number.';
	}
  
	if (empty($password) || empty($password2)) {
	  $errors["password"] = 'Please enter your password twice.';
	} else if ($password !== $password2) {
	  $errors["password"] = 'Your passwords do not match.';
	}
  
	// If there are no validation errors, add the user to the database
	if (empty($errors)) {
	  // Connect to the database using PDO
	  $dsn = 'mysql:host=localhost;dbname=database';
	  $username = 'root';
	  $password = 'pass';
	  
	  try {
		  $pdo = new PDO($dsn, $username, $password);
		  $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
  
		  // Check if email or phone already exists
		  $stmt = $pdo->prepare("SELECT * FROM users WHERE email = :email OR phone = :phone");
		  $stmt->bindParam(':email', $email);
		  $stmt->bindParam(':phone', $phone);
		  $stmt->execute();
		  $user = $stmt->fetch();
  
		  if ($user) {
			  // Email or phone already exists, display error message and prompt user to choose a different email or phone
			  if ($user['email'] === $email) {
				  echo "Email is already in use, please choose a different email";
			  } else {
				  echo "Phone number is already in use, please choose a different phone number";
			  }
		  } else {
			  // Prepare the INSERT statement
			  $new_id = uniqid();
			  $stmt = $pdo->prepare('INSERT INTO users (id, name, email, phone, password) VALUES (:id, :name, :email, :phone, :password)');
  
			  // Bind the parameters and execute the statement
			  $stmt->bindParam(':id', $new_id);
			  $stmt->bindParam(':name', $name);
			  $stmt->bindParam(':email', $email);
			  $stmt->bindParam(':phone', $phone);
			  $stmt->bindParam(':password', password_hash($password, PASSWORD_DEFAULT));
			  $stmt->execute();
  
			  // Redirect the user to the dashboard
			  $_SESSION['user_id'] = $new_id;
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
  <title>Register</title>
</head>
<body>

  <h1>Register</h1>

  <p>Please fill out the form below to register.</p>

  <?php if (!empty($errors)): ?>
    <ul>
      <?php foreach ($errors as $error): ?>
        <li><?php echo $error; ?></li>
      <?php endforeach; ?>
    </ul>
  <?php endif; ?>

  <form method="post" action="">
  <label for="name">Name:</label>
  <input type="text" id="name" name="name" >

  <br>

  <label for="email">Email:</label>
  <input type="email" id="email" name="email" >

  <br>

  <label for="phone">Phone:</label>
  <input type="tel" id="phone" name="phone" >

  <br>

  <label for="password">Password:</label>
  <input type="password" id="password" name="password">

  <br>

  <label for="password2">Confirm Password:</label>
  <input type="password" id="password2" name="password2">

  <br>

  <input type="submit" value="Register">
</form>
</body>
</html>
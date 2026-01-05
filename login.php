<?php
session_start();

// Show errors for debugging (remove in production)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// 1. Connect to your DB
// $host = "localhost";
// $user = "root";
// $pass = "";
// $dbname = "incubation_db";

// Database connection
// $host = "sql301.infinityfree.com";
// $user = "if0_40723633";
// $pass = "Mabhelan@21";
// $dbname = "4387232_incubator_db";";

// Database connection
// $host = "sql301.infinityfree.com";
// $user = "if0_40723633";
// $pass = "Mabhelan21";
// $dbname = "if0_40723633_incubator_db";

$conn = new mysqli($host, $user, $pass, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$showAlert = false;
$alertType = '';
$alertMessage = '';
$redirectPage = '';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['email'])) {
    $email = trim($_POST["email"]);
    $password = $_POST["password"];

    $stmt = $conn->prepare("SELECT id, firstname, password FROM users WHERE email = ?");
    if (!$stmt) {
        die("Prepare failed: " . $conn->error);
    }
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows === 1) {
        $stmt->bind_result($userId, $firstname, $hashedPassword);
        $stmt->fetch();

        if (password_verify($password, $hashedPassword)) {
            $_SESSION["user_id"] = $userId;
            $_SESSION["firstname"] = $firstname;

            $showAlert = true;
            $alertType = "success";
            $alertMessage = "Welcome, $firstname! Redirecting...";
            $redirectPage = "dashboard.php";
        } else {
            $showAlert = true;
            $alertType = "error";
            $alertMessage = "Incorrect password. Please try again.";
        }
    } else {
        $showAlert = true;
        $alertType = "error";
        $alertMessage = "No user found with that email.";
    }

    $stmt->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en" class="no-js">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<title>SMMEs Virtual Incubation</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" />
<link rel="stylesheet" type="text/css" href="/css/register.css" />
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
</head>
<body id="page-top" style="overflow-x: hidden">

<!-- Navigation (same as login.html) -->
<nav class="navbar">
  <a class="navbar-brand" href="#page-top"><img src="./assets/img/preview.png" alt="Logo" /></a>
  <div class="nav-toggle" onclick="toggleMenu()">&#9776;</div>
  <ul class="nav-links" id="navMenu">
    <li><a href="home.html"><i class="fas fa-house"></i> Home</a></li>
    <li><a href="index.html#services"><i class="fas fa-handshake"></i> Our Services</a></li>
    <li><a href="index.html#digital"><i class="fas fa-info-circle"></i> About Us</a></li>
    <li><a href="index.html#contact"><i class="fas fa-envelope"></i> Contact Us</a></li>
    <li class="dropdown">
      <a href="#" class="dropbtn" onclick="toggleDropdown(event, 'dropdown1')">
        <i class="fa fa-user"></i> User
        <span class="dropdown-icon"><i class="fa fa-caret-down"></i></span>
      </a>
      <div class="dropdown-content" id="dropdown1">
        <a href="#password-reset"><i class="fas fa-key"></i> Password Reset</a>
      </div>
    </li>
  </ul>
</nav>

<div class="container" style="padding-top: 40px;">
  <div class="image-background" id="imageBackground"></div>

  <!-- Form Column -->
  <div class="form-column">
    <div class="wrapper">
      <div class="form-container">
        <div class="slide-controls">
          <input type="radio" name="slide" id="login" checked />
          <input type="radio" name="slide" id="signup" />
          <label for="login" class="slide login">Login</label>
          <label for="signup" class="slide signup">Sign Up</label>
          <div class="slider-tab"></div>
        </div>

        <div class="form-inner">
          <!-- Login Form -->
          <form class="login" id="login-form" action="login.php" method="post">
            <div class="field">
              <input type="email" name="email" placeholder="Email" required />
            </div>
            <div class="field">
              <input type="password" name="password" placeholder="Password" required />
            </div>
            <div class="field btn">
              <div class="btn-layer"></div>
              <input type="submit" value="Login" />
            </div>
            <div class="login-image" style="text-align: center; margin-top: 40px;">
              <img src="assets/img/Anim.gif" alt="Login Image" style="width: 100px" />
            </div>
          </form>

          <!-- Signup Form -->
          <form class="signup" id="signup-form" action="signup.php" method="POST">
            <div class="field"><input type="text" name="firstname" placeholder="First Name" required /></div>
            <div class="field"><input type="text" name="lastname" placeholder="Last Name" required /></div>
            <div class="field"><input type="text" name="email" placeholder="Email" required /></div>
            <div class="field"><input type="password" name="password" placeholder="Password" required /></div>
            <div class="field btn"><div class="btn-layer"></div><input type="submit" value="Sign Up" /></div>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- SweetAlert Login Feedback -->
<?php if ($showAlert): ?>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
Swal.fire({
    icon: "<?php echo $alertType; ?>",
    title: "<?php echo $alertType === 'success' ? 'Login Successful' : 'Login Failed'; ?>",
    text: "<?php echo $alertMessage; ?>",
    showConfirmButton: false,
    timer: 3000,
    timerProgressBar: true
});
<?php if ($alertType === 'success'): ?>
setTimeout(() => { window.location.href = "<?php echo $redirectPage; ?>"; }, 3000);
<?php endif; ?>
</script>
<?php endif; ?>

<!-- Add all other scripts from login.html (dropdown, toggleMenu, background images, scroll to top, etc.) -->
<script src="login-scripts.js"></script>

</body>
</html>

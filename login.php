<?php
session_start();

$host = "localhost";
$user = "root";
$pass = "";
$dbname = "incubation_db";

$conn = new mysqli($host, $user, $pass, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$showAlert = false;
$alertType = '';
$alertMessage = '';
$redirectPage = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST["email"]);
    $password = $_POST["password"];

    $stmt = $conn->prepare("SELECT id, firstname, password FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows === 1) {
        $stmt->bind_result($userId, $firstname, $hashedPassword);
        $stmt->fetch();

        if (password_verify($password, $hashedPassword)) {
            $_SESSION["user_id"] = $userId;
            $_SESSION["firstname"] = $firstname;

            // Success alert
            $showAlert = true;
            $alertType = "success";
            $alertMessage = "Welcome, $firstname! Redirecting...";
            $redirectPage = "dashboard.html";
        } else {
            // Incorrect password
            $showAlert = true;
            $alertType = "error";
            $alertMessage = "Incorrect password. Please try again.";
        }
    } else {
        // No user found
        $showAlert = true;
        $alertType = "error";
        $alertMessage = "No user found with that email.";
    }

    $stmt->close();
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Login</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" />
</head>
<body>

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
      setTimeout(() => {
        window.location.href = "<?php echo $redirectPage; ?>";
      }, 3000);
    <?php else: ?>
      setTimeout(() => {
        window.location.href = "login.html";
      }, 3000);
    <?php endif; ?>
  </script>
<?php endif; ?>

<script>
  const form = document.getElementById('login-form');

  form.addEventListener('submit', function(event) {
    event.preventDefault();

    const formData = new FormData(form);

    fetch('login.php', {
      method: 'POST',
      body: formData
    })
    .then(response => response.text())
    .then(responseText => {
      document.body.innerHTML = responseText;
    })
    .catch(error => {
      console.error('Error:', error);
    });
  });
</script>

</body>
</html>

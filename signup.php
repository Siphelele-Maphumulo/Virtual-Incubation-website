<?php
// // 1. Connect to your online DB
// $host = "sql211.infinityfree.com";
// $user = "if0_38744100";
// $pass = "Mabhelan21";
// $dbname = "if0_38744100_incubator_db";


// 1. Connect to your DB
$host = "localhost";
$user = "root";
$pass = "";
$dbname = "incubation_db";

$conn = new mysqli($host, $user, $pass, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// 2. Collect form data with basic validation
$firstname = isset($_POST['firstname']) ? trim($_POST['firstname']) : '';
$lastname = isset($_POST['lastname']) ? trim($_POST['lastname']) : '';
$email = isset($_POST['email']) ? trim($_POST['email']) : '';
$password = isset($_POST['password']) ? $_POST['password'] : '';

if ($firstname && $lastname && $email && $password) {
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    // 3. Insert into users table
    $sql = "INSERT INTO users (firstname, lastname, email, password) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssss", $firstname, $lastname, $email, $hashedPassword);

    if ($stmt->execute()) {
        echo '
        <!DOCTYPE html>
        <html>
        <head>
            <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        </head>
        <body>
            <script>
                Swal.fire({
                    icon: "success",
                    title: "Registration Successful!",
                    text: "You will be redirected to the login page shortly...",
                    showConfirmButton: false,
                    timer: 5000,
                    timerProgressBar: true
                });

                setTimeout(() => {
                    window.location.href = "login.html"; // change to your login page if needed
                }, 5000);
            </script>
        </body>
        </html>
        ';
    } else {
        echo '
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script>
            Swal.fire({
                icon: "error",
                title: "Registration Failed",
                text: "' . $stmt->error . '"
            });
        </script>
        ';
    }

    $stmt->close();
} else {
    echo '
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        Swal.fire({
            icon: "warning",
            title: "Incomplete Form",
            text: "Please fill in all fields."
        });
    </script>
    ';
}

$conn->close();
?>

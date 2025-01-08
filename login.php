<?php
session_start();
include 'connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT * FROM users WHERE username = ? AND status = 'active'");
    $stmt->bind_param('s', $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();

        if (password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_type'] = $user['user_type'];

            if (isset($_POST['remember_me'])) {
                setcookie('username', $username, time() + (86400 * 30), "/");
            }

            header('Location: homepage.php');
            exit;
        } else {
            $error = 'Invalid password.';
        }
    } else {
        $error = 'Invalid username or inactive account.';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <form method="POST">
            <h2 class="form-title">Login</h2>
            <?php if (!empty($error)) echo "<p style='color:red;'>$error</p>"; ?>
            <div class="input-group">
                <input type="text" name="username" placeholder=" " required>
                <label>Username</label>
            </div>
            <div class="input-group">
                <input type="password" name="password" placeholder=" " required>
                <label>Password</label>
            </div>
            <div class="recover">
                <input type="checkbox" name="remember_me"> Remember Me
                <a href="forgot_password.php">Forgot Password?</a>
            </div>
            <button class="btn" type="submit">Login</button>
            <div class="or">or</div>
            <button type="button" onclick="window.location.href='registration.php';">Sign Up</button>
        </form>
    </div>
</body>
</html>

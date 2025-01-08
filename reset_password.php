<?php
include 'connect.php';

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['token'])) {
    $token = $_POST['token'];
    $new_password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    // Validate token
    $stmt = $conn->prepare("SELECT id FROM users WHERE reset_token = ? AND reset_expires > NOW()");
    $stmt->bind_param('s', $token);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        // Update the password
        $stmt->bind_result($user_id);
        $stmt->fetch();

        $update_stmt = $conn->prepare("UPDATE users SET password = ?, reset_token = NULL, reset_expires = NULL WHERE id = ?");
        $update_stmt->bind_param('si', $new_password, $user_id);

        if ($update_stmt->execute()) {
            $message = "Password successfully reset. You can now log in.";
        } else {
            $message = "Error: Unable to reset password.";
        }

        $update_stmt->close();
    } else {
        $message = "Invalid or expired token.";
    }

    $stmt->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <form method="POST">
            <h2 class="form-title">Reset Password</h2>
            <?php if (!empty($message)) echo "<p style='color:red;'>$message</p>"; ?>
            <div class="input-group">
                <input type="password" name="password" placeholder=" " required>
                <label>New Password</label>
            </div>
            <input type="hidden" name="token" value="<?php echo htmlspecialchars($_GET['token']); ?>">
            <button class="btn" type="submit">Reset Password</button>
        </form>
    </div>
</body>
</html>

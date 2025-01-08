<?php
include 'connect.php';

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];

    // Check if email exists in the database
    $check_stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $check_stmt->bind_param('s', $email);
    $check_stmt->execute();
    $check_stmt->store_result();

    if ($check_stmt->num_rows > 0) {
        // Generate a unique reset token
        $token = bin2hex(random_bytes(32));
        $expires_at = date("Y-m-d H:i:s", strtotime("+1 hour"));

        // Store the reset token and its expiry time
        $update_stmt = $conn->prepare("UPDATE users SET reset_token = ?, reset_expires = ? WHERE email = ?");
        $update_stmt->bind_param('sss', $token, $expires_at, $email);
        if ($update_stmt->execute()) {
            // Send reset link to user email
            $reset_link = "http://yourwebsite.com/reset_password.php?token=$token";
            $subject = "Password Reset Request";
            $body = "Click the link below to reset your password:\n\n$reset_link";
            $headers = "From: noreply@yourwebsite.com";

            if (mail($email, $subject, $body, $headers)) {
                $message = "A password reset link has been sent to your email.";
            } else {
                $message = "Error: Unable to send email. Please try again later.";
            }
        } else {
            $message = "Error: Unable to process request.";
        }

        $update_stmt->close();
    } else {
        $message = "Error: Email address not found.";
    }

    $check_stmt->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <form method="POST">
            <h2 class="form-title">Forgot Password</h2>
            <?php if (!empty($message)) echo "<p style='color:red;'>$message</p>"; ?>
            <div class="input-group">
                <input type="email" name="email" placeholder=" " required>
                <label>Email Address</label>
            </div>
            <button class="btn" type="submit">Send Reset Link</button>
        </form>
    </div>
</body>
</html>

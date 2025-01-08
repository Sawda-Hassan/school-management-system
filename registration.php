<?php
include 'connect.php';

$message = ''; // Variable to hold success or error message

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $user_type = $_POST['user_type'];
    $status = $_POST['status'];

    // Check for duplicate username
    $check_stmt = $conn->prepare("SELECT id FROM users WHERE username = ?");
    $check_stmt->bind_param('s', $username);
    $check_stmt->execute();
    $check_stmt->store_result();

    if ($check_stmt->num_rows > 0) {
        $message = 'Error: Username already exists. Please choose a different username.';
    } else {
        // Prepare and execute the SQL statement
        $stmt = $conn->prepare("INSERT INTO users (first_name, last_name, username, password, email, phone, user_type, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        if ($stmt) {
            $stmt->bind_param('ssssssss', $first_name, $last_name, $username, $password, $email, $phone, $user_type, $status);

            if ($stmt->execute()) {
                // Redirect to homepage upon successful registration
                header('Location: homepage.php');
                exit;
            } else {
                $message = 'Error: Unable to register user. ' . $stmt->error;
            }
            $stmt->close();
        } else {
            $message = 'Error preparing statement: ' . $conn->error;
        }
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
    <title>Registration</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <?php if (!empty($message)) : ?>
        <div id="message-box">
            <p><?php echo htmlspecialchars($message); ?></p>
            <button onclick="document.getElementById('message-box').style.display='none';">OK</button>
        </div>
    <?php endif; ?>
    
    <div class="container">
        <form method="POST" enctype="multipart/form-data">
            <h2 class="form-title">Register</h2>
            <div class="input-group">
                <input type="text" name="first_name" placeholder=" " required>
                <label>First Name</label>
            </div>
            <div class="input-group">
                <input type="text" name="last_name" placeholder=" " required>
                <label>Last Name</label>
            </div>
            <div class="input-group">
                <input type="text" name="username" placeholder=" " required>
                <label>Username</label>
            </div>
            <div class="input-group">
                <input type="password" name="password" placeholder=" " required>
                <label>Password</label>
            </div>
            <div class="input-group">
                <input type="email" name="email" placeholder=" " required>
                <label>Email</label>
            </div>
            <div class="input-group">
                <input type="text" name="phone" placeholder=" " required>
                <label>Phone</label>
            </div>
            <div class="input-group">
                <label>User Type</label>
                <select name="user_type" required>
                    <option value="student">Student</option>
                    <option value="staff">Staff</option>
                    <option value="admin">Admin</option>
                </select>
            </div>
            <div class="input-group">
                <label>Status</label>
                <select name="status">
                    <option value="active">Active</option>
                    <option value="inactive">Inactive</option>
                </select>
            </div>
            <button class="btn" type="submit">Register</button>
        </form>
    </div>

    <style>
        #message-box {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background-color: white;
            border: 1px solid #ccc;
            padding: 20px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            text-align: center;
            z-index: 1000;
        }

        #message-box button {
            margin-top: 10px;
            padding: 5px 10px;
            background-color: blue;
            color: white;
            border: none;
            cursor: pointer;
        }

        #message-box button:hover {
            background-color: darkblue;
        }
    </style>
</body>
</html>

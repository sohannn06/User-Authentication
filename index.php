<?php
session_start();

// Array to store user data (for demonstration purposes)
$users = [];

if (file_exists('users.txt')) {
    $users = json_decode(file_get_contents('users.txt'), true);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['register'])) {
        // Registration process
        $username = $_POST['username'];
        $email = $_POST['email'];
        $password = password_hash($_POST['password'], PASSWORD_BCRYPT);

        $user = [
            'username' => $username,
            'email' => $email,
            'password' => $password,
            'role' => 'user', // Set a default role
        ];

        $users[] = $user;
        file_put_contents('users.txt', json_encode($user) . "\n", FILE_APPEND);

        echo "Registration successful. <a href=\"$_SERVER[PHP_SELF]\">Login</a>";
    } elseif (isset($_POST['login'])) {
        // Login process
        $email = $_POST['email'];
        $password = $_POST['password'];

        foreach ($users as $userData) {
            if ($userData['email'] === $email && password_verify($password, $userData['password'])) {
                $_SESSION['user'] = $userData;
                header('Location: dashboard.php');
                exit;
            }
        }

        // Authentication failed
        echo "Login failed. <a href=\"$_SERVER[PHP_SELF]\">Try again</a>";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>User Registration and Login</title>
</head>
<body>
    <?php if (!isset($_SESSION['user'])) : ?>
        <h2>User Registration</h2>
        <form method="post">
            Username: <input type="text" name="username" required><br><br>
            Email: <input type="email" name="email" required><br><br>
            Password: <input type="password" name="password" required><br><br>
            <input type="submit" name="register" value="Register">
        </form>

        <h2>User Login</h2>
        <form method="post">
            Email: <input type="email" name="email" required><br><br>
            Password: <input type="password" name="password" required><br><br>
            <input type="submit" name="login" value="Login">
        </form>
    <?php else : ?>
        <p>Welcome, <?php echo $_SESSION['user']['username']; ?>!</p>
        <p>Your Role: <?php echo $_SESSION['user']['role']; ?></p>
    <?php endif; ?>
</body>
</html>

<?php
session_start();
require 'vendor/autoload.php';
require_once 'user.php';
use App\user;
$dsn = 'mysql:host=localhost;dbname=users;charset=utf8';
$username = 'dimav';
$password = '8289/00/5654';
try {
    $pdo = new PDO($dsn, $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die('Database connection failed: ' . $e->getMessage());
}
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $login = $_POST['login'] ?? '';
    $password = $_POST['password'] ?? '';
    if (empty($login)) {
        $error = 'Login cannot be empty';
    } elseif (!filter_var($login, FILTER_VALIDATE_EMAIL)) {
        $error = 'Login must be a valid email';
    } elseif (empty($password)) {
        $error = 'Password cannot be empty';
    } else {
        echo "Trying to get user by login: $login\n";
        $user = user::getUserByLogin($pdo, $login);

        if ($user) {
            $userObject = user::fromArray($pdo, $user);
            echo "User data: ";
            print_r($user);
            echo "Password entered: $password\n";
            if ($userObject->verifyPassword($password)) {
                $_SESSION['user'] = $login;
                $_SESSION['login_time'] = date('Y-m-d H:i');
                setcookie('auth', sha1($login . $_SESSION['login_time']), time() + 3600);
                header('Location: index.php');
                exit();
            } else {
                $error = 'Incorrect password';
            }
        } else {
            $error = 'Login does not exist';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
<div class="container mt-5">
    <h1>Login</h1>
    <form method="POST" action="">
        <div class="form-group">
            <label for="login">Email</label>
            <input type="email" class="form-control" name="login" id="login" value="<?= htmlspecialchars($_POST['login'] ?? '') ?>" required>
        </div>
        <div class="form-group">
            <label for="password">Password</label>
            <input type="password" class="form-control" name="password" id="password" required>
        </div>
        <button type="submit" class="btn btn-primary">Login</button>
    </form>
    <?php if ($error): ?>
        <div class="alert alert-danger mt-3"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>
    <p class="mt-3"><a href="register.php">Don't have an account? Register</a></p>
</div>
</body>
</html>


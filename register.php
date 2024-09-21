<?php
session_start();
require_once 'user.php';
use App\User;
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
    $name = $_POST['name'] ?? '';
    $login = $_POST['login'] ?? '';
    $password = $_POST['password'] ?? '';
    if (empty($name)) {
        $error = 'Name cannot be empty';
    } elseif (empty($login) || !filter_var($login, FILTER_VALIDATE_EMAIL)) {
        $error = 'Login must be a valid email';
    } elseif (User::getUserByLogin($pdo, $login)) {
        $error = 'Login already exists';
    } elseif (strlen($password) <= 4) {
        $error = 'Password must be longer than 4 characters';
    } elseif (!preg_match('/[0-9]/', $password)) {
        $error = 'Password must contain at least one number';
    } elseif (!preg_match('/[A-Z]/', $password)) {
        $error = 'Password must contain at least one uppercase letter';
    } else {
        $newUser = new User($name, $login, $password);
        User::addUser($pdo, $newUser);
        $_SESSION['user'] = $login;
        $_SESSION['login_time'] = date('Y-m-d H:i');
        setcookie('auth', sha1($login . $_SESSION['login_time']), time() + 3600);
        header('Location: index.php');
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
<div class="container mt-5">
    <h1>Register</h1>
    <form method="POST" action="">
        <div class="form-group">
            <label for="name">Name</label>
            <input type="text" class="form-control" name="name" id="name" value="<?= htmlspecialchars($_POST['name'] ?? '') ?>" required>
        </div>
        <div class="form-group">
            <label for="login">Email</label>
            <input type="email" class="form-control" name="login" id="login" value="<?= htmlspecialchars($_POST['login'] ?? '') ?>" required>
        </div>
        <div class="form-group">
            <label for="password">Password</label>
            <input type="password" class="form-control" name="password" id="password" required>
        </div>
        <button type="submit" class="btn btn-primary">Register</button>
    </form>
    <?php if ($error): ?>
        <div class="alert alert-danger mt-3"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>
    <p class="mt-3"><a href="login.php">Already have an account? Login</a></p>
</div>
</body>
</html>
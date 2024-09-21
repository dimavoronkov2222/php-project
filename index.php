<?php
session_start();
require_once 'countries.php';
require_once 'functions.php';
if (isset($_GET['logout'])) {
    session_destroy();
    header('Location: ' . $_SERVER['PHP_SELF']);
    exit();
}
if (!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit();
}
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!empty($_POST['username'])) {
        $_SESSION['user'] = htmlspecialchars($_POST['username']);
    }
    if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] === UPLOAD_ERR_OK) {
        $oldAvatar = $_SESSION['avatar'] ?? null;
        $filename = uniqid() . '.' . pathinfo($_FILES['avatar']['name'], PATHINFO_EXTENSION);
        $uploadPath = __DIR__ . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR . $filename;
        if (move_uploaded_file($_FILES['avatar']['tmp_name'], $uploadPath)) {
            if ($oldAvatar && file_exists(__DIR__ . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR . $oldAvatar)) {
                unlink(__DIR__ . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR . $oldAvatar);
            }
            $_SESSION['avatar'] = $filename;
        } else {
            $errorMessage = 'Error uploading file.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
<div class="container mt-5">
    <h1 class="mb-4">Welcome, <?= htmlspecialchars($_SESSION['user']) ?>!</h1>
    <?php if (isset($_SESSION['avatar'])): ?>
        <img src="/uploads/<?= $_SESSION['avatar'] ?>" alt="User Avatar" class="img-thumbnail mb-3" style="width: 150px; height: 150px;">
    <?php else: ?>
        <p>No avatar uploaded</p>
    <?php endif; ?>
    <form method="POST" enctype="multipart/form-data" class="mb-4">
        <h2>Edit Profile</h2>
        <div class="form-group">
            <label for="username">Edit Username:</label>
            <input type="text" class="form-control" name="username" value="<?= htmlspecialchars($_SESSION['user']) ?>" required>
        </div>
        <div class="form-group">
            <label for="avatar">Upload Avatar:</label>
            <input type="file" class="form-control-file" name="avatar" accept="image/*">
        </div>
        <button type="submit" class="btn btn-primary">Save</button>
    </form>
    <a href="?logout=1" class="btn btn-danger">Logout</a>
    <h2 class="mt-5">Search for a Country</h2>
    <form method="POST" action="" class="mb-4">
        <div class="input-group">
            <input type="text" class="form-control" name="search" placeholder="Enter part of country name">
            <div class="input-group-append">
                <button type="submit" class="btn btn-outline-secondary">Search</button>
            </div>
        </div>
    </form>
    <?php if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['search'])): ?>
        <h3>Filtered Countries:</h3>
        <ul class="list-group">
            <?php
            $filteredCountries = filterCountries($_POST['search'], $countries);
            if (!empty($filteredCountries)) {
                foreach ($filteredCountries as $country) {
                    echo '<li class="list-group-item">' . htmlspecialchars($country) . '</li>';
                }
            } else {
                echo '<li class="list-group-item">No countries found.</li>';
            }
            ?>
        </ul>
    <?php endif; ?>
</div>
</body>
</html>
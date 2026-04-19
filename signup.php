<?php
declare(strict_types=1);

require_once __DIR__ . '/database.php';
require_once __DIR__ . '/UserRepository.php';
require_once __DIR__ . '/auth.php';

ensure_session_started();

if (is_logged_in()) {
    header('Location: ' . (current_user_type() === 'admin' ? 'TicketManagement.php' : 'userDashboard.php'));
    exit;
}

$name = '';
$email = '';
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim((string)($_POST['name'] ?? ''));
    $email = trim((string)($_POST['email'] ?? ''));
    $password = (string)($_POST['password'] ?? '');

    if ($name === '') {
        $errors['name'] = 'Name is required.';
    } elseif (mb_strlen($name) > 60) {
        $errors['name'] = 'Name must be 60 characters or less.';
    }

    if ($email === '') {
        $errors['email'] = 'Email is required.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = 'Please enter a valid email.';
    }

    if ($password === '') {
        $errors['password'] = 'Password is required.';
    } elseif (mb_strlen($password) < 6) {
        $errors['password'] = 'Password must be at least 6 characters.';
    }

    if (empty($errors)) {
        try {
            $userRepository = new UserRepository($pdo);
            $emailNormalized = mb_strtolower($email);

            if ($userRepository->findByEmail($emailNormalized)) {
                $errors['email'] = 'That email is already registered.';
            } else {
                $userId = $userRepository->createUser(
                    $name,
                    $emailNormalized,
                    password_hash($password, PASSWORD_DEFAULT)
                );

                login_user([
                    'id' => $userId,
                    'name' => $name,
                    'email' => $emailNormalized,
                    'user_type' => 'user',
                ]);

                header('Location: userDashboard.php');
                exit;
            }
        } catch (Throwable $e) {
            $errors['general'] = 'Sign up failed. Please try again.';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="style.css">
    <title>Tickify | Sign Up</title>
</head>
<body>
<header>
    <nav class="navbar navbar-expand-lg">
        <div class="container-fluid">
            <a class="navbar-brand" href="./index.php"><img src="./images/tickify_logo.png" class="header-logo" alt="Tickify"></a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item">
                        <a class="nav-link" href="./login.php">Login</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" aria-current="page" href="./signup.php">Sign Up</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
</header>

<main class="d-flex justify-content-center align-items-center" id="login-container">
    <div class="card p-4 shadow login-card">
        <h3 class="mb-3 text-center">Sign Up</h3>

        <?php if (isset($errors['general'])): ?>
            <div class="alert alert-danger" role="alert">
                <?= htmlspecialchars($errors['general']) ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="">
            <div class="mb-3">
                <label for="name">Name</label>
                <input type="text" id="name" name="name" class="form-control" value="<?= htmlspecialchars($name) ?>" required>
                <?php if(isset($errors['name'])) echo "<span class='error-message'>" . htmlspecialchars($errors['name']) . "</span>";?>
            </div>

            <div class="mb-3">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" class="form-control" value="<?= htmlspecialchars($email) ?>" required>
                <?php if(isset($errors['email'])) echo "<span class='error-message'>" . htmlspecialchars($errors['email']) . "</span>";?>
            </div>

            <div class="mb-3">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" class="form-control" required>
                <?php if(isset($errors['password'])) echo "<span class='error-message'>" . htmlspecialchars($errors['password']) . "</span>";?>
            </div>

            <button class="btn w-100 form-button" type="submit">Create Account</button>
        </form>
    </div>
</main>

<footer class="footer">
    <div class="container d-flex justify-content-between align-items-center">
        <p class="mb-0">&copy; 2026 Tickify</p>
        <p class="mb-0">Built by Omar, Daniel &amp; Sneh</p>
    </div>
</footer>
</body>
</html>

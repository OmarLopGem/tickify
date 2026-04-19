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

$loginEmail = '';
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $loginEmail = trim((string)($_POST['loginEmail'] ?? ''));
    $loginPassword = (string)($_POST['loginPassword'] ?? '');

    if ($loginEmail === '') {
        $errors['loginEmail'] = 'Email is required.';
    } elseif (!filter_var($loginEmail, FILTER_VALIDATE_EMAIL)) {
        $errors['loginEmail'] = 'Please enter a valid email.';
    }

    if ($loginPassword === '') {
        $errors['loginPassword'] = 'Password is required.';
    }

    if (empty($errors)) {
        try {
            $userRepository = new UserRepository($pdo);
            $user = $userRepository->findByEmail(mb_strtolower($loginEmail));

            $storedPassword = (string)($user['password'] ?? '');
            $passwordOk = $storedPassword !== '' && (
                password_verify($loginPassword, $storedPassword) ||
                hash_equals($storedPassword, $loginPassword)
            );

            if (!$user || !$passwordOk) {
                $errors['general'] = 'Invalid email or password.';
            } else {
                login_user($user);
                header('Location: ' . (current_user_type() === 'admin' ? 'TicketManagement.php' : 'userDashboard.php'));
                exit;
            }
        } catch (Throwable $e) {
            $errors['general'] = 'Login failed. Please try again.';
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
    <title>Tickify | Login</title>
</head>
<body>
    <header>
        <nav class="navbar navbar-expand-lg">
            <div class="container-fluid">
                <a class="navbar-brand" href="./index.php"><img src="./images/tickify_logo.png" class="header-logo"></a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                        <li class="nav-item">
                            <a class="nav-link active" aria-current="page" href="./index.php">Login</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="./signup.php">Sign Up</a>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>
    </header>
    <main class="d-flex justify-content-center align-items-center" id="login-container">
        <div class="card p-4 shadow login-card">
            <h3 class="mb-3 text-center">Login</h3>

            <?php if (isset($errors['general'])): ?>
                <div class="alert alert-danger" role="alert">
                    <?= htmlspecialchars($errors['general']) ?>
                </div>
            <?php endif; ?>

            <form method="POST" action="">
                <div class="mb-3">
                    <label for="loginEmail">Email</label>
                    <input type="email" id="loginEmail" name="loginEmail" class="form-control" value="<?= htmlspecialchars($loginEmail) ?>">
                    <?php if(isset($errors['loginEmail'])) echo "<span class='error-message'>" . htmlspecialchars($errors['loginEmail']) . "</span>";?>
                </div>

                <div class="mb-3">
                    <label for="loginPassword">Password</label>
                    <input type="password" id="loginPassword" name="loginPassword" class="form-control">
                    <?php if(isset($errors['loginPassword'])) echo "<span class='error-message'>" . htmlspecialchars($errors['loginPassword']) . "</span>";?>
                </div>

                <button class="btn w-100 form-button" type="submit">Login</button>
            </form>
        </div>
    </main>
    <footer class="footer">
        <div class="container d-flex justify-content-between align-items-center">
            
            <p class="mb-0">&copy; 2026 Tickify</p>
            
            <p class="mb-0">
                Built by Omar, Daniel & Sneh
            </p>

        </div>
    </footer>
</body>
</html>

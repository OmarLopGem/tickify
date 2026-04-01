<?php

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
                <a href="./index.php"><img src="./images/tickify_logo.png" class="header-logo"></a>
                <div class="d-flex" id="navbarNavAltMarkup">
                    <div class="navbar-nav">
                        <a class="nav-link active" aria-current="page" href="./index.php">Login</a>
                        <a class="nav-link" href="./signup.php">Signup</a>
                    </div>
                </div>
            </div>
        </nav>
    </header>
    <main class="d-flex justify-content-center align-items-center" id="login-container">
        <div class="card p-4 shadow login-card">
            <h3 class="mb-3 text-center">Login</h3>

            <form>
                <div class="mb-3">
                    <label for="loginEmail">Email</label>
                    <input type="email" id="loginEmail" name="loginEmail" class="form-control">
                    <span class="error-message">*</span>
                </div>

                <div class="mb-3">
                    <label for="loginPassword">Password</label>
                    <input type="password" id="loginPassword" name="loginPassword" class="form-control">
                    <span class="error-message">*</span>
                </div>

                <button class="btn w-100 form-button">Login</button>
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
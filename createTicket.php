<?php
declare(strict_types= 1);
require_once 'Ticket.php';
require_once 'TicketRepository.php';
require_once 'database.php';

session_start();

$title = "";
$description = "";
$status = "open";
$createdBy = "";
$assignedTo = null;
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'] ?? '';
    $description = $_POST['description'] ?? null;
    $priority = isset($_POST['priority']) ? (int)$_POST['priority'] : 3;
    $createdBy = $_SESSION['user_id'] ?? null;

    //validation
    if (empty($title)) {
        
    }

    $ticket = new Ticket(
        null,
        $title,
        $description,
        $status,
        $priority,
        $createdBy,
        $assignedTo
    );

    if (empty($errors)) {
        $ticketRepository = new TicketRepository($pdo);

        if ($ticketRepository->createTicket($ticket)) {
            $successMessage = 'Ticket created successfully.';
            $title = '';
            $description = '';
            $priority = 3;
            $assignedTo = '';
        } else {
            $errors['general'] = 'Error creating ticket.';
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
    <title>Tickify | Create a Ticket</title>
</head>
<body>
    <header>
        <nav class="navbar navbar-expand-lg">
            <div class="container-fluid">
                <a href="./index.php"><img src="./images/tickify_logo.png" class="header-logo"></a>
                <div class="d-flex" id="navbarNavAltMarkup">
                    <div class="navbar-nav">
                        <a class="nav-link" href="./dashboard.php">Dashboard</a>
                        <a class="nav-link active" aria-current="page" href="./createTicket.php">Create a Ticker</a>
                        <a class="nav-link" href="./dashboard.php">Logout</a>
                    </div>
                </div>
            </div>
        </nav>
    </header>
    <main class="d-flex justify-content-center align-items-center">
        

        <form method="POST" action="">
            <?php if ($successMessage !== ''): ?>
                <div class="alert alert-success">
                    <?= htmlspecialchars($successMessage) ?>
                </div>
            <?php endif; ?>

            <?php if (isset($errors['general'])): ?>
                <div class="alert alert-danger">
                    <?= htmlspecialchars($errors['general']) ?>
                </div>
            <?php endif; ?>

            <label for="title">Title</label>
            <input type="text" id="title" name="title" value=<?php echo htmlspecialchars($title); ?> required>

        </form>
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
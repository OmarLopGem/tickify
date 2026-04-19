<?php
declare(strict_types= 1);
require_once 'Ticket.php';
require_once 'TicketRepository.php';
require_once 'database.php';
require_once 'auth.php';

require_login();

$title = "";
$description = "";
$status = "open";
$priority = 3;
$assignedTo = 1;
$errors = [];
$created = ($_GET['created'] ?? '') === '1';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'] ?? '';
    $description = $_POST['description'] ?? '';
    $priority = isset($_POST['priority']) ? (int)$_POST['priority'] : 3;
    $createdBy = (int)($_SESSION['user_id'] ?? 0);

    if ($createdBy <= 0) {
        $errors['general'] = 'You must be logged in to create a ticket.';
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
        $errors = $ticket->validate();
    }

    if (empty($errors)) {
        try {
        $ticketRepository = new TicketRepository($pdo);
            $ticketRepository->createTicket($ticket);
            header('Location: createTicket.php?created=1');
            exit;
        } catch (PDOException $e) {
            die("Could not add the ticket" . $e->getMessage());
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
                <a class="navbar-brand" href="./index.php"><img src="./images/tickify_logo.png" class="header-logo"></a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                        <li class="nav-item">
                            <a class="nav-link" href="./userDashboard.php">Dashboard</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link active" aria-current="page" href="./createTicket.php">Create a Ticket</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="./logout.php">Logout</a>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>
    </header>
    <main class="container my-4">
        <div class="row justify-content-center">
            <div class="col-12 col-md-8 col-lg-6">
                <div class="card p-4 shadow">
                    <h3 class="mb-3 text-center">Create a Ticket</h3>

                    <?php if ($created): ?>
                        <div class="alert alert-success" role="alert">
                            Ticket created successfully.
                        </div>
                    <?php endif; ?>

                    <?php if (isset($errors['general'])): ?>
                        <div class="alert alert-danger" role="alert">
                            <?= htmlspecialchars($errors['general']) ?>
                        </div>
                    <?php endif; ?>

                    <form method="POST" action="">
                        <div class="mb-3">
                            <label for="title" class="form-label">Title</label>
                            <input type="text" id="title" name="title" class="form-control" value="<?= htmlspecialchars(trim($title)) ?>">
                            <?php if(isset($errors['title'])): ?>
                                <div class="error-message"><?= htmlspecialchars($errors['title']) ?></div>
                            <?php endif; ?>
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea rows="4" id="description" name="description" class="form-control"><?= htmlspecialchars(trim($description)) ?></textarea>
                            <?php if(isset($errors['description'])): ?>
                                <div class="error-message"><?= htmlspecialchars($errors['description']) ?></div>
                            <?php endif; ?>
                        </div>

                        <div class="mb-3">
                            <label for="priority" class="form-label">Priority</label>
                            <select id="priority" name="priority" class="form-select">
                                <option value="1" <?= ($priority == 1) ? 'selected' : ''; ?>>1</option>
                                <option value="2" <?= ($priority == 2) ? 'selected' : ''; ?>>2</option>
                                <option value="3" <?= ($priority == 3) ? 'selected' : ''; ?>>3</option>
                                <option value="4" <?= ($priority == 4) ? 'selected' : ''; ?>>4</option>
                                <option value="5" <?= ($priority == 5) ? 'selected' : ''; ?>>5</option>
                            </select>
                            <?php if (isset($errors['priority'])): ?>
                                <div class="error-message"><?= htmlspecialchars($errors['priority']) ?></div>
                            <?php endif; ?>
                        </div>

                        <button class="btn w-100 form-button" type="submit">Submit</button>
                    </form>
                </div>
            </div>
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

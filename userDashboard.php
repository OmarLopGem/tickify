<?php
declare(strict_types= 1);
require_once 'Ticket.php';
require_once 'TicketRepository.php';
require_once 'database.php';

session_start();

$search = $_GET['search'] ?? '';

$ticketRepository = new TicketRepository($pdo);
if ($search) {
    $ticketsArray = $ticketRepository->searchTickets($search);
} else {
    $ticketsArray = $ticketRepository->getAllTickets();
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
    <title>Tickify | Dashboard</title>
</head>
<body>
    <header>
        <nav class="navbar navbar-expand-lg">
            <div class="container-fluid">
                <a class="navbar-brand" href="./userDashboard.php"><img src="./images/tickify_logo.png" class="header-logo"></a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                        <li class="nav-item">
                            <a class="nav-link active" aria-current="page" href="./userDashboard.php">Dashboard</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="./createTicket.php">Create a Ticket</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#">Logout</a>
                        </li>
                    </ul>
                    <form class="d-flex" method="GET" action="userDashboard.php" role="search">
                        <input class="form-control me-2" type="search" name="search" placeholder="Search" value="<?= htmlspecialchars($search) ?>" aria-label="Search"/>
                        <button class="btn btn-outline-success" type="submit">Search</button>
                    </form>
                </div>
            </div>
        </nav>
    </header>
    <main class="d-flex justify-content-center align-items-center">
        <?php foreach ($ticketsArray as $ticket): ?>
            <div>
                <?= $ticket->getTitle() ?>
                <?= $ticket->getDescription() ?>
                <?= $ticket->getPriority() ?>
                <?= $ticket->getStatus() ?>
                <?= $ticket->getAssignedTo() ?>
                <a href="ticketPDF.php?id=<?= $ticket->getId() ?>" target="_blank" class="btn">Download PDF</a>
            </div>
        <?php endforeach ?>
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
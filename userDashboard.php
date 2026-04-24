<?php
declare(strict_types=1);
require_once 'Ticket.php';
require_once 'TicketRepository.php';
require_once 'database.php';
require_once 'auth.php';
require_once 'enum/TicketPriorityEnum.php';
require_once 'enum/TicketStatusEnum.php';

require_login();

$userId = $_SESSION['user_id'] ?? null;

if (!$userId) {
    die('User not logged in');
}

$search = $_GET['search'] ?? '';

$ticketRepository = new TicketRepository($pdo);

if ($search) {
    $ticketsArray = $ticketRepository->searchTicketsByUser((int) $userId, $search);
} else {
    $ticketsArray = $ticketRepository->getAllCreatedBy((int) $userId);
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI"
        crossorigin="anonymous"></script>
    <link rel="stylesheet" href="style.css">
    <title>Tickify | Dashboard</title>
</head>

<body>
    <header>
        <nav class="navbar navbar-expand-lg">
            <div class="container-fluid">
                <a class="navbar-brand" href="./userDashboard.php"><img src="./images/tickify_logo.png"
                        class="header-logo"></a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
                    data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent"
                    aria-expanded="false" aria-label="Toggle navigation">
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
                            <a class="nav-link" href="./logout.php">Logout</a>
                        </li>
                    </ul>
                    <form class="d-flex" method="GET" action="userDashboard.php" role="search">
                        <input class="form-control me-2" type="search" name="search" placeholder="Search"
                            value="<?= htmlspecialchars($search) ?>" aria-label="Search" />
                        <button class="btn btn-outline-success" type="submit">Search</button>
                    </form>
                </div>
            </div>
        </nav>
    </header>
    <main class="container my-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
                <h2 class="mb-0">My Tickets</h2>
                <small class="text-muted">Welcome,
                    <?= htmlspecialchars((string) ($_SESSION['user_name'] ?? '')) ?></small>
            </div>
            <a class="btn btn-primary" href="./createTicket.php">Create Ticket</a>
        </div>

        <?php if (empty($ticketsArray)): ?>
            <div class="alert alert-info">No tickets found.</div>
        <?php else: ?>
            <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-3">
                <?php foreach ($ticketsArray as $ticket):
                    try {
                        $statusEnum = TicketStatusEnum::from($ticket->getStatus());
                    } catch (ValueError $e) {
                        $statusEnum = TicketStatusEnum::Open;
                    }

                    try {
                        $priorityEnum = TicketPriorityEnum::from((int) $ticket->getPriority());
                    } catch (ValueError $e) {
                        $priorityEnum = TicketPriorityEnum::Medium;
                    }
                    ?>
                    <div class="col">
                        <div class="card h-100 ticket-card">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-start gap-2">
                                    <h5 class="card-title mb-1">
                                        <?= htmlspecialchars($ticket->getTitle()) ?>
                                    </h5>
                                    <span class="text-muted small">#<?= (int) $ticket->getId() ?></span>
                                </div>

                                <p class="card-text text-muted ticket-description">
                                    <?= nl2br(htmlspecialchars((string) ($ticket->getDescription() ?? ''))) ?>
                                </p>

                                <div class="d-flex flex-wrap gap-2 align-items-center">
                                    <span class="badge rounded-pill" style="background-color: <?= $statusEnum->color() ?>">
                                        <?= htmlspecialchars($statusEnum->label()) ?>
                                    </span>
                                    <span class="badge rounded-pill" style="background-color: <?= $priorityEnum->color() ?>">
                                        <?= htmlspecialchars($priorityEnum->label()) ?>
                                    </span>
                                </div>
                            </div>
                            <div class="card-footer bg-transparent border-0 d-flex justify-content-between align-items-center">
                                <small class="text-muted">
                                    <?= htmlspecialchars((string) ($ticket->getCreatedAt() ?? '')) ?>
                                </small>
                                <div class="btn-group">
                                    <a href="ticketDetail.php?id=<?= (int) $ticket->getId() ?>"
                                        class="btn btn-sm btn-outline-primary">
                                        Details
                                    </a>
                                    <a href="ticketPDF.php?id=<?= (int) $ticket->getId() ?>" target="_blank"
                                        class="btn btn-sm btn-outline-secondary">
                                        PDF
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach ?>
            </div>
        <?php endif; ?>
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
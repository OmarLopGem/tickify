<?php
declare(strict_types=1);
require_once 'Ticket.php';
require_once 'TicketRepository.php';
require_once 'database.php';
require_once 'enum/TicketPriorityEnum.php';
require_once 'enum/TicketStatusEnum.php';
require_once 'auth.php';

require_admin();

$title = "";
$description = "";
$satus = "open";
$createdBy = "";
$assignedTo = null;
$errors = [];
$tickts = [];


if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    try {
        $ticketRepository = new TicketRepository($pdo);
        $filterStatus = $_GET['status'] ?? '';
        $filterPriority = $_GET['priority'] ?? '';
        $search = trim((string)($_GET['search'] ?? ''));

        try {
            $ticketRepository = new TicketRepository($pdo);
            $tickets = $ticketRepository->getFilteredTickets($filterStatus, $filterPriority, $search);
        } catch (Exception $e) {
            $errors['general'] = 'Error fetching tickets: ' . $e->getMessage();
        }
        
    } catch (Exception $e) {
        $errors['general'] = 'Error fetching tickets: ' . $e->getMessage();
    }
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
    <title>Tickify | Ticket Management</title>
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
                            <a class="nav-link active" aria-current="page" href="./TicketManagement.php">Management</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="./logout.php">Logout</a>
                        </li>
                    </ul>
                    <form class="d-flex" method="GET" action="" role="search">
                        <input class="form-control me-2" name="search" type="search" placeholder="Search" value="<?= htmlspecialchars($search ?? '') ?>" aria-label="Search"/>
                        <button class="btn btn-outline-success" type="submit">Search</button>
                    </form>
                </div>
            </div>
        </nav>
    </header>
    <main class="container my-4">
        <?php if (!empty($errors['general'])): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($errors['general']) ?></div>
        <?php endif; ?>

        

        <?php if (empty($tickets)): ?>
            <div class="alert alert-info">No tickets found.</div>
        <?php else: ?>
            <div class="table-responsive">

                <form method="GET" class="row g-2 mb-3 align-items-end">
                    <input type="hidden" name="search" value="<?= htmlspecialchars($search) ?>">
                    <div class="col-auto">
                        <label class="form-label mb-0">Status</label>
                        <select name="status" class="form-select form-select-sm">
                            <option value="">All</option>
                            <?php foreach (TicketStatusEnum::cases() as $s): ?>
                                <option value="<?= $s->value ?>" <?= ($filterStatus === $s->value) ? 'selected' : '' ?>>
                                    <?= $s->label() ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-auto">
                        <label class="form-label mb-0">Priority</label>
                        <select name="priority" class="form-select form-select-sm">
                            <option value="">All</option>
                            <?php foreach (TicketPriorityEnum::cases() as $p): ?>
                                <option value="<?= $p->value ?>" <?= ($filterPriority === $p->value) ? 'selected' : '' ?>>
                                    <?= $p->label() ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-auto">
                        <button type="submit" class="btn btn-sm btn-primary">Filter</button>
                        <a href="./TicketManagement.php" class="btn btn-sm btn-outline-secondary">Clear</a>
                    </div>
                </form>

                <table class="table table-hover table-dark align-middle table-striped">
                    <thead class="table-dark">
                        <tr>
                            <th>#</th>
                            <th>Title</th>
                            <th>Status</th>
                            <th>Priority</th>
                            <th>Assigned To</th>
                            <th>Created</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($tickets as $ticket):
                            $status = TicketStatusEnum::from($ticket->getStatus());
                            $priority = TicketPriorityEnum::from($ticket->getPriority());
                            ?>
                            <tr>
                                <td><?= $ticket->getId() ?></td>
                                <td><?= htmlspecialchars($ticket->getTitle()) ?></td>
                                <td>
                                    <span class="badge rounded-pill" style="background-color: <?= $status->color() ?>">
                                        <?= $status->label() ?>
                                    </span>
                                </td>
                                <td>
                                    <span class="badge rounded-pill" style="background-color: <?= $priority->color() ?>">
                                        <?= $priority->label() ?>
                                    </span>
                                </td>
                                <td><?= $ticket->getAssignedTo() ?? '—' ?></td>
                                <td><?= $ticket->getCreatedAt() ?></td>
                                <td>
                                    <a href="./ticketDetail.php?id=<?= $ticket->getId() ?>" class="btn btn-sm btn-outline-secondary">
                                        View
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
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

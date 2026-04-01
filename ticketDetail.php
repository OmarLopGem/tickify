<?php
declare(strict_types=1);
require_once 'Ticket.php';
require_once 'TicketRepository.php';
require_once 'database.php';
require_once 'enum/TicketPriorityEnum.php';
require_once 'enum/TicketStatusEnum.php';

session_start();

$title = "";
$description = "";
$satus = "open";
$createdBy = "";
$assignedTo = null;
$errors = [];
$ticket = null;
$comments = [];


if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    try {
        $ticketRepository = new TicketRepository($pdo);
        $ticketId = $_GET['id'] ?? '';


        try {
            $ticketRepository = new TicketRepository($pdo);
            $aux = (int) $ticketId;
            $ticket = $ticketRepository->getById($aux);
            $comments = $ticketRepository->getCommentsByTicketId($aux);
        } catch (Exception $e) {
            $errors['general'] = 'Error fetching ticket: ' . $e->getMessage();
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
    <main class="container my-4">
        <?php if (!empty($errors['general'])): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($errors['general']) ?></div>
        <?php endif; ?>

        <?php if ($ticket): ?>
            <!-- Back button -->
            <a href="./TicketManagement.php" class="btn btn-sm btn-outline-secondary mb-3">&larr; Back to Tickets</a>

            <!-- Ticket detail card -->
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">Ticket #<?= $ticket->getId() ?></h4>
                    <small class="text-muted"><?= htmlspecialchars($ticket->getCreatedAt() ?? '') ?></small>
                </div>
                <div class="card-body">
                    <h5><?= htmlspecialchars($ticket->getTitle()) ?></h5>
                    <p class="text-muted"><?= nl2br(htmlspecialchars($ticket->getDescription() ?? '')) ?></p>

                    <hr>

                    <!-- Status & Priority update form -->
                    <form method="POST" action="./updateTicket.php" class="row g-2 align-items-end">
                        <input type="hidden" name="id" value="<?= $ticket->getId() ?>">

                        <div class="col-auto">
                            <label class="form-label mb-0">Status</label>
                            <select name="status" class="form-select form-select-sm">
                                <?php foreach (TicketStatusEnum::cases() as $s): ?>
                                    <option value="<?= $s->value ?>" <?= ($ticket->getStatus() === $s->value) ? 'selected' : '' ?>>
                                        <?= $s->label() ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="col-auto">
                            <label class="form-label mb-0">Priority</label>
                            <select name="priority" class="form-select form-select-sm">
                                <?php foreach (TicketPriorityEnum::cases() as $p): ?>
                                    <option value="<?= $p->value ?>" <?= ($ticket->getPriority() === $p->value) ? 'selected' : '' ?>>
                                        <?= $p->label() ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="col-auto">
                            <button type="submit" class="btn btn-sm btn-primary">Update</button>
                        </div>
                    </form>

                    <hr>

                    <!-- Current badges -->
                    <div class="d-flex gap-2">
                        <?php
                        $statusEnum = TicketStatusEnum::from($ticket->getStatus());
                        $priorityEnum = TicketPriorityEnum::from($ticket->getPriority());
                        ?>
                        <span class="badge rounded-pill" style="background-color: <?= $statusEnum->color() ?>">
                            <?= $statusEnum->label() ?>
                        </span>
                        <span class="badge rounded-pill" style="background-color: <?= $priorityEnum->color() ?>">
                            <?= $priorityEnum->label() ?>
                        </span>
                        <span class="text-muted ms-auto">
                            Assigned to: <?= $ticket->getAssignedTo() ?? 'Unassigned' ?>
                        </span>
                    </div>
                </div>
            </div>

            <!-- Comments section -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Comments (<?= count($comments) ?>)</h5>
                </div>
                <div class="card-body">
                    <?php if (!empty($comments)): ?>
                        <?php foreach ($comments as $comment): ?>
                            <div class="d-flex mb-3">
                                <div class="bg-light rounded p-3 w-100">
                                    <div class="d-flex justify-content-between mb-1">
                                        <strong>User #<?= $comment->getAuthorId() ?></strong>
                                        <small class="text-muted"><?= htmlspecialchars($comment->getCreatedAt() ?? '') ?></small>
                                    </div>
                                    <p class="mb-0"><?= nl2br(htmlspecialchars($comment->getBody())) ?></p>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p class="text-muted">No comments yet.</p>
                    <?php endif; ?>

                    <hr>

                    <!-- New comment form -->
                    <form method="POST" action="./addComment.php">
                        <input type="hidden" name="ticket_id" value="<?= $ticket->getId() ?>">
                        <div class="mb-2">
                            <textarea name="body" class="form-control" rows="3" placeholder="Write a comment..."
                                required></textarea>
                        </div>
                        <button type="submit" class="btn btn-sm btn-primary">Post Comment</button>
                    </form>
                </div>
            </div>

        <?php else: ?>
            <div class="alert alert-warning">Ticket not found.</div>
        <?php endif; ?>
    </main>
</body>

</html>
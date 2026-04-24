<?php
declare(strict_types=1);
require_once 'Ticket.php';
require_once 'TicketRepository.php';
require_once 'database.php';
require_once 'enum/TicketPriorityEnum.php';
require_once 'enum/TicketStatusEnum.php';
require_once 'auth.php';

ensure_session_started();

if (!is_logged_in()) {
    header('Location: index.php');
    exit;
}

$errors = [];
$ticket = null;
$comments = [];

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $ticketId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
    
    if ($ticketId > 0) {
        try {
            $ticketRepository = new TicketRepository($pdo);
            $ticket = $ticketRepository->getById($ticketId);
            $comments = $ticketRepository->getCommentsByTicketId($ticketId);
        } catch (Exception $e) {
            $errors['general'] = 'Error: ' . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
    <title>Tickify | Ticket Detail</title>
</head>
<body>
    <header>
        <nav class="navbar navbar-expand-lg">
            <div class="container-fluid">
                <a class="navbar-brand" href="./index.php"><img src="./images/tickify_logo.png" class="header-logo"></a>
                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                        <li class="nav-item">
                            <a class="nav-link" href="<?= current_user_type() === 'admin' ? './TicketManagement.php' : './userDashboard.php' ?>">Dashboard</a>
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
        <?php if ($ticket): ?>
            <a href="<?= current_user_type() === 'admin' ? './TicketManagement.php' : './userDashboard.php' ?>" class="btn btn-sm btn-outline-secondary mb-3">&larr; Back</a>

            <div class="card mb-4 shadow-sm">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">Ticket #<?= $ticket->getId() ?></h4>
                    <span class="badge bg-info text-dark"><?= htmlspecialchars($ticket->getStatus()) ?></span>
                </div>
                <div class="card-body">
                    <h5><?= htmlspecialchars($ticket->getTitle()) ?></h5>
                    <p class="text-muted"><?= nl2br(htmlspecialchars($ticket->getDescription() ?? '')) ?></p>

                    <hr>

                    <?php if (current_user_type() === 'admin'): ?>
                        <div class="admin-controls p-3 bg-light rounded border">
                            <h6>Admin Controls (Edit Status/Priority)</h6>
                            <form method="POST" action="./utilsTicketRest/updateTicket.php" class="row g-2 align-items-end">
                                <input type="hidden" name="id" value="<?= $ticket->getId() ?>">
                                <div class="col-auto">
                                    <label class="form-label small">Status</label>
                                    <select name="status" class="form-select form-select-sm">
                                        <?php foreach (TicketStatusEnum::cases() as $s): ?>
                                            <option value="<?= $s->value ?>" <?= ($ticket->getStatus() === $s->value) ? 'selected' : '' ?>><?= $s->label() ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="col-auto">
                                    <label class="form-label small">Priority</label>
                                    <select name="priority" class="form-select form-select-sm">
                                        <?php foreach (TicketPriorityEnum::cases() as $p): ?>
                                            <option value="<?= $p->value ?>" <?= ($ticket->getPriority() === $p->value) ? 'selected' : '' ?>><?= $p->label() ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="col-auto">
                                    <button type="submit" class="btn btn-sm btn-primary">Update Ticket</button>
                                </div>
                            </form>
                        </div>
                        <hr>
                    <?php endif; ?>

                    <div class="d-flex gap-2">
                        <span class="text-muted">Created: <?= htmlspecialchars($ticket->getCreatedAt() ?? '') ?></span>
                        <span class="text-muted ms-auto">Assigned to: <?= $ticket->getAssignedTo() ?? 'Unassigned' ?></span>
                    </div>
                </div>
            </div>

            <div class="card shadow-sm">
                <div class="card-header">
                    <h5 class="mb-0">Comments (<?= count($comments) ?>)</h5>
                </div>
                <div class="card-body">
                    <div class="comment-list mb-4">
                        <?php if (!empty($comments)): ?>
                            <?php foreach ($comments as $comment): ?>
                                <div class="p-2 mb-2 border-bottom">
                                    <div class="d-flex justify-content-between">
                                        <small><strong>User #<?= is_array($comment) ? $comment['author_id'] : $comment->getAuthorId() ?></strong></small>
                                        <small class="text-muted"><?= is_array($comment) ? $comment['created_at'] : $comment->getCreatedAt() ?></small>
                                    </div>
                                    <p class="mb-0"><?= nl2br(htmlspecialchars(is_array($comment) ? $comment['body'] : $comment->getBody())) ?></p>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <p class="text-muted">No comments yet.</p>
                        <?php endif; ?>
                    </div>

                    <form method="POST" action="./utilsTicketRest/addComment.php">
                        <input type="hidden" name="ticket_id" value="<?= $ticket->getId() ?>">
                        <div class="mb-2">
                            <textarea name="body" class="form-control" rows="3" placeholder="Add a comment..." required></textarea>
                        </div>
                        <button type="submit" class="btn btn-sm btn-dark">Post Comment</button>
                    </form>
                </div>
            </div>

        <?php else: ?>
            <div class="alert alert-warning">Ticket not found or access denied.</div>
        <?php endif; ?>
    </main>
</body>
</html>
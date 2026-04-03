<?php
declare(strict_types=1);
require_once '../TicketRepository.php';
require_once '../database.php';

session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $ticketId = (int) ($_POST['ticket_id'] ?? 0);
    $body = trim($_POST['body'] ?? '');
    $authorId = (int) ($_SESSION['user_id'] ?? 1);

    if ($body !== '' && $ticketId > 0 && $authorId > 0) {
        $repo = new TicketRepository($pdo);
        $repo->addComment($ticketId, $authorId, $body);
    }

    header("Location: ../ticketDetail.php?id=$ticketId");
    exit;
}
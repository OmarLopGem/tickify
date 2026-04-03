<?php
declare(strict_types=1);
require_once '../TicketRepository.php';
require_once '../database.php';

session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = (int) ($_POST['id'] ?? 0);
    $status = $_POST['status'] ?? '';
    $priority = (int) ($_POST['priority'] ?? 3);

    $repo = new TicketRepository($pdo);
    $repo->updateStatusAndPriority($id, $status, $priority);

    header("Location: ../ticketDetail.php?id=$id");
    exit;
}
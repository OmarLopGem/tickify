<?php
declare(strict_types=1);

require_once __DIR__ . '/auth.php';

ensure_session_started();

if (is_logged_in()) {
    header('Location: ' . (current_user_type() === 'admin' ? 'TicketManagement.php' : 'userDashboard.php'));
    exit;
}

header('Location: login.php');
exit;

?>

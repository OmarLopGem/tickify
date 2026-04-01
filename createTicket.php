<?php
declare(strict_types= 1);
require_once 'Ticket.php';
require_once 'TicketRepository.php';
require_once 'database.php';

session_start();

$title = "";
$description = "";
$status = "open";
$priority = 3;
$assignedTo = 1;
$errors = [];
$successMessage = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'] ?? '';
    $description = $_POST['description'] ?? '';
    $priority = isset($_POST['priority']) ? (int)$_POST['priority'] : 3;
    $createdBy = 2;

    $ticket = new Ticket(
        null,
        $title,
        $description,
        $status,
        $priority,
        $createdBy,
        $assignedTo
    );

    $errors = $ticket->validate();

    if (empty($errors)) {
        try {
            $ticketRepository = new TicketRepository($pdo);
            $ticketRepository->createTicket($ticket);
            header("location: createTicket.php");
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
                            <a class="nav-link" href="#">Logout</a>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>
    </header>
    <main class="d-flex justify-content-center align-items-center">

        <form method="POST" action="">
            <div class="input-group">
                <label for="title">Title</label>
                <input type="text" id="title" name="title" value="<?php echo htmlspecialchars(trim($title)); ?>">
                <?php if(isset($errors['title'])) echo "<span class='error-message'>{$errors['title']}</span>";?>
            </div>

            <div class="input-group">
                <label for="description">Description</label>
                <textarea rows="4" id="description" name="description"><?php echo htmlspecialchars(trim($description)); ?></textarea>
                <?php if(isset($errors['description'])) echo "<span class='error-message'>{$errors['description']}</span>";?>
            </div>

            <div class="input-group">
                <label for="priority">Priority</label>
                <select id="priority" name="priority">
                    <option value="1" <?php echo ($priority == 1) ? 'selected' : ''; ?>>1</option>
                    <option value="2" <?php echo ($priority == 2) ? 'selected' : ''; ?>>2</option>
                    <option value="3" <?php echo ($priority == 3) ? 'selected' : ''; ?>>3</option>
                    <option value="4" <?php echo ($priority == 4) ? 'selected' : ''; ?>>4</option>
                    <option value="5" <?php echo ($priority == 5) ? 'selected' : ''; ?>>5</option>
                </select>
                <?php if (isset($errors['priority'])) echo "<span class='error-message'>{$errors['priority']}</span>"; ?>
            </div>
            
            <button type="submit">Submit</button>

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
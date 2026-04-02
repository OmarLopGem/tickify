<?php
declare(strict_types=1);
require_once 'Ticket.php';
require_once 'TicketComment.php';

class TicketRepository
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function createTicket(Ticket $ticket): bool
    {
        $sql = "INSERT INTO tickets (title, description, status, priority, created_by, assigned_to)
        VALUES (:title, :description, :status, :priority, :created_by, :assigned_to)";

        $stmt = $this->pdo->prepare($sql);
 
        $success = $stmt->execute([
            ':title' => $ticket->getTitle(),
            ':description' => $ticket->getDescription(),
            ':status' => $ticket->getStatus(),
            ':priority' => $ticket->getPriority(),
            ':created_by' => $ticket->getCreatedBy(),
            ':assigned_to' => $ticket->getAssignedTo()
        ]);

        if ($success) {
            $ticket->setId((int) $this->pdo->lastInsertId());
        }

        return $success;

    }

    public function getAllTickets(): array
    {
        $sql = "SELECT * FROM tickets ORDER BY created_at DESC";
        $stmt = $this->pdo->query($sql);

        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $tickets = [];

        foreach ($rows as $row) {
            $tickets[] = $this->mapRowToTicket($row);
        }

        return $tickets;
    }

    public function getById(int $id): ?Ticket
    {
        $sql = "SELECT * FROM tickets WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':id' => $id]);

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$row) {
            return null;
        }

        return $this->mapRowToTicket($row);
    }

    public function getByStatus(string $status): ?array
    {
        $sql = "SELECT * FROM tickets WHERE status = :status ORDER BY created_at DESC";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':status' => $status]);

        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $tickets = [];

        foreach ($rows as $row) {
            $tickets[] = $this->mapRowToTicket($row);
        }

        return $tickets;
    }

    public function getAllAsignedTo(int $userId): array
    {
        $sql = "SELECT * FROM tickets WHERE assigned_to = :assigned_to ORDER BY created_at DESC";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':assigned_to' => $userId]);

        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $tickets = [];

        foreach ($rows as $row) {
            $tickets[] = $this->mapRowToTicket($row);
        }

        return $tickets;
    }

    public function getFilteredTickets(string $status = '', string $priority = ''): array
    {
        $sql = 'SELECT * FROM tickets WHERE assigned_to = 1';
        $params = [];

        if ($status !== '') {
            $sql .= ' AND status = :status';
            $params[':status'] = $status;
        }

        if ($priority !== '') {
            $sql .= ' AND priority = :priority';
            $params[':priority'] = (int) $priority;
        }

        $sql .= ' ORDER BY created_at DESC';

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);

        $tickets = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $tickets[] = new Ticket(
                $row['id'],
                $row['title'],
                $row['description'],
                $row['status'],
                (int) $row['priority'],
                (int) $row['created_by'],
                $row['assigned_to'] ? (int) $row['assigned_to'] : null,
                $row['created_at'],
                $row['updated_at']
            );
        }

        return $tickets;
    }


    public function getCommentsByTicketId(int $ticketId): array
    {
        $sql = "SELECT * FROM ticket_comments WHERE ticket_id = :ticket_id ORDER BY created_at ASC";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':ticket_id' => $ticketId]);

        $comments = array_map([TicketComment::class, 'fromRow'], $stmt->fetchAll(PDO::FETCH_ASSOC));
        return $comments;
    }

    private function mapRowToTicket(array $row): Ticket
    {
        return new Ticket(
            $row['id'],
            $row['title'],
            $row['description'],
            $row['status'],
            $row['priority'],
            $row['created_by'],
            $row['assigned_to'],
            $row['created_at'],
            $row['updated_at']
        );
    }

    public function searchTickets(string $search): array {
        $stmt = $this->pdo->prepare("SELECT * FROM tickets WHERE title LIKE :search OR description LIKE :search");
        $stmt->execute([
            'search' => "%$search%"
        ]);

        $results = [];

        while ($row = $stmt->fetch()) {
            $results[] = new Ticket(
                $row['id'],
                $row['title'],
                $row['description'],
                $row['status'],
                $row['priority'],
                $row['created_by'],
                $row['assigned_to']
            );
        }

        return $results;
    }
}

?>
<?php
declare(strict_types=1);

class TicketComment
{
    private ?int $id;
    private int $ticketId;
    private int $authorId;
    private string $body;
    private ?string $createdAt;

    public function __construct(
        ?int $id,
        int $ticketId,
        int $authorId,
        string $body,
        ?string $createdAt = null
    ) {
        $this->id = $id;
        $this->ticketId = $ticketId;
        $this->authorId = $authorId;
        $this->body = trim($body);
        $this->createdAt = $createdAt;
    }

    public static function fromRow(array $row): self
    {
        return new self(
            (int) $row['id'],
            (int) $row['ticket_id'],
            (int) $row['author_id'],
            $row['body'],
            $row['created_at'] ?? null
        );
    }

    public function getId(): ?int { return $this->id; }
    public function getTicketId(): int { return $this->ticketId; }
    public function getAuthorId(): int { return $this->authorId; }
    public function getBody(): string { return $this->body; }
    public function getCreatedAt(): ?string { return $this->createdAt; }

    public function validate(): array
    {
        $errors = [];

        if ($this->body === '') {
            $errors['body'] = 'Comment body is required.';
        }
        if ($this->ticketId <= 0) {
            $errors['ticket_id'] = 'Invalid ticket.';
        }
        if ($this->authorId <= 0) {
            $errors['author_id'] = 'Invalid author.';
        }

        return $errors;
    }
}
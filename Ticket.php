<?php
declare(strict_types= 1);
class Ticket {
    private ?int $id;
    private string $title;
    private string $description;
    private string $status;
    private int $priority;
    private int $createdBy;
    private int $assignedTo;
    private ?string $createdAt;
    private ?string $updatedAt;

    public function __construct(
        ?int $id,
        string $title,
        string $description,
        string $status,
        int $priority,
        int $createdBy,
        int $assignedTo,
        ?string $createdAt = null,
        ?string $updatedAt = null
    ) {
        $this->id = $id;
        $this->title = $title;
        $this->description = $description;
        $this->status = $status;
        $this->priority = $priority;
        $this->createdBy = $createdBy;
        $this->assignedTo = $assignedTo;
        $this->createdAt = $createdAt;
        $this->updatedAt = $updatedAt;
    }

    public function getId(): ?int {
        return $this->id;
    }

    public function getTitle(): string {
        return $this->title;
    }

    public function getDescription(): ?string {
        return $this->description;
    }

    public function getStatus(): string {
        return $this->status;
    }

    public function getPriority(): int {
        return $this->priority;
    }

    public function getCreatedBy(): ?int {
        return $this->createdBy;
    }

    public function getAssignedTo(): ?int {
        return $this->assignedTo;
    }

    public function getCreatedAt(): ?string {
        return $this->createdAt;
    }

    public function getUpdatedAt(): ?string {
        return $this->updatedAt;
    }

    public function setId(int $id): void {
        $this->id = $id;
    }

    public function validate(): array {
        $errors = [];

        if ($this->title === '') {
            $errors['title'] = 'Title is required.';
        } elseif (mb_strlen($this->title) > 50) {
            $errors['title'] = 'Title cannot exceed 50 characters.';
        }

        if ($this->description === '') {
            $errors['description'] = 'description is required.';
        } elseif (mb_strlen($this->description) > 200) {
            $errors['description'] = 'Description cannot exceed 200 characters.';
        }

        if ($this->priority < 1 || $this->priority > 5) {
            $errors['priority'] = 'Priority must be between 1 and 5.';
        }

        return $errors;
    }
}
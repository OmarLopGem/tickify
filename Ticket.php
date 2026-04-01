<?php

class Ticket {
    private ?int $id;
    private string $title;
    private ?string $description;
    private string $status;
    private int $priority;
    private ?int $createdBy;
    private ?int $assignedTo;
    private ?string $createdAt;
    private ?string $updatedAt;

    public function __construct(
        ?int $id,
        string $title,
        ?string $description = null,
        string $status = 'open',
        int $priority = 3,
        ?int $createdBy = null,
        ?int $assignedTo = null,
        ?string $createdAt = null,
        ?string $updatedAt = null
    ) {
        $this->id = $id;
        $this->title = trim($title);
        $this->description = $description !== null ? trim($description) : null;
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
        } elseif (mb_strlen($this->title) > 200) {
            $errors['title'] = 'Title cannot exceed 200 characters.';
        }

        $allowedStatuses = ['open', 'in_progress', 'on_hold', 'resolved', 'closed', 'cancelled'];
        if (!in_array($this->status, $allowedStatuses, true)) {
            $errors['status'] = 'Invalid status.';
        }

        if ($this->priority < 1 || $this->priority > 5) {
            $errors['priority'] = 'Priority must be between 1 and 5.';
        }

        if ($this->createdBy !== null && $this->createdBy <= 0) {
            $errors['created_by'] = 'Invalid creator.';
        }

        if ($this->assignedTo !== null && $this->assignedTo <= 0) {
            $errors['assigned_to'] = 'Invalid assigned user.';
        }

        return $errors;
    }
}
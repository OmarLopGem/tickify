<?php

final class TicketStatusEnum
{
    public const OPEN = 'open';
    public const IN_PROGRESS = 'in_progress';
    public const ON_HOLD = 'on_hold';
    public const RESOLVED = 'resolved';
    public const CLOSED = 'closed';
    public const CANCELLED = 'cancelled';

    public string $value;

    private function __construct(string $value)
    {
        $this->value = $value;
    }

    public static function from(string $value): self
    {
        $value = trim($value);
        if (!in_array($value, self::values(), true)) {
            throw new ValueError('Invalid TicketStatusEnum value');
        }
        return new self($value);
    }

    /** @return self[] */
    public static function cases(): array
    {
        return array_map(static fn (string $v) => new self($v), self::values());
    }

    /** @return string[] */
    private static function values(): array
    {
        return [
            self::OPEN,
            self::IN_PROGRESS,
            self::ON_HOLD,
            self::RESOLVED,
            self::CLOSED,
            self::CANCELLED,
        ];
    }

    public function label(): string
    {
        switch ($this->value) {
            case self::OPEN:
                return 'Open';
            case self::IN_PROGRESS:
                return 'In Progress';
            case self::ON_HOLD:
                return 'On Hold';
            case self::RESOLVED:
                return 'Resolved';
            case self::CLOSED:
                return 'Closed';
            case self::CANCELLED:
                return 'Cancelled';
        }

        return 'Open';
    }

    public function color(): string
    {
        switch ($this->value) {
            case self::OPEN:
                return '#3B82F6';
            case self::IN_PROGRESS:
                return '#F59E0B';
            case self::ON_HOLD:
                return '#6B7280';
            case self::RESOLVED:
                return '#10B981';
            case self::CLOSED:
                return '#1F2937';
            case self::CANCELLED:
                return '#EF4444';
        }

        return '#3B82F6';
    }
}
?>

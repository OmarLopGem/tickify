<?php
final class TicketPriorityEnum
{
    public const CRITICAL = 1;
    public const HIGH = 2;
    public const MEDIUM = 3;
    public const LOW = 4;
    public const TRIVIAL = 5;

    public int $value;

    private function __construct(int $value)
    {
        $this->value = $value;
    }

    public static function from(int $value): self
    {
        if (!in_array($value, self::values(), true)) {
            throw new ValueError('Invalid TicketPriorityEnum value');
        }
        return new self($value);
    }

    /** @return self[] */
    public static function cases(): array
    {
        return array_map(static fn (int $v) => new self($v), self::values());
    }

    /** @return int[] */
    private static function values(): array
    {
        return [
            self::CRITICAL,
            self::HIGH,
            self::MEDIUM,
            self::LOW,
            self::TRIVIAL,
        ];
    }

    public function label(): string
    {
        switch ($this->value) {
            case self::CRITICAL:
                return 'Critical';
            case self::HIGH:
                return 'High';
            case self::MEDIUM:
                return 'Medium';
            case self::LOW:
                return 'Low';
            case self::TRIVIAL:
                return 'Trivial';
        }

        return 'Medium';
    }

    public function color(): string
    {
        switch ($this->value) {
            case self::CRITICAL:
                return '#DC2626';
            case self::HIGH:
                return '#F97316';
            case self::MEDIUM:
                return '#EAB308';
            case self::LOW:
                return '#3B82F6';
            case self::TRIVIAL:
                return '#9CA3AF';
        }

        return '#EAB308';
    }
}
?>

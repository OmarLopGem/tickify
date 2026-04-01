<?php
enum TicketPriorityEnum: int
{
    case Critical = 1;
    case High = 2;
    case Medium = 3;
    case Low = 4;
    case Trivial = 5;

    public function label(): string
    {
        return match ($this) {
            self::Critical => 'Critical',
            self::High => 'High',
            self::Medium => 'Medium',
            self::Low => 'Low',
            self::Trivial => 'Trivial',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Critical => '#DC2626',
            self::High => '#F97316',    
            self::Medium => '#EAB308',
            self::Low => '#3B82F6', 
            self::Trivial => '#9CA3AF', 
        };
    }
}
?>
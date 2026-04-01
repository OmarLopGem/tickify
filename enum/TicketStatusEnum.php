<?php

enum TicketStatusEnum: string
{
    case Open = 'open';
    case InProgress = 'in_progress';
    case OnHold = 'on_hold';
    case Resolved = 'resolved';
    case Closed = 'closed';
    case Cancelled = 'cancelled';

    public function label(): string
    {
        return match ($this) {
            self::Open => 'Open',
            self::InProgress => 'In Progress',
            self::OnHold => 'On Hold',
            self::Resolved => 'Resolved',
            self::Closed => 'Closed',
            self::Cancelled => 'Cancelled',
        };
    }

        public function color(): string
    {
        return match ($this) {
            self::Open => '#3B82F6',      
            self::InProgress => '#F59E0B', 
            self::OnHold => '#6B7280',   
            self::Resolved => '#10B981',  
            self::Closed => '#1F2937',    
            self::Cancelled => '#EF4444',
        };
    }
}
?>

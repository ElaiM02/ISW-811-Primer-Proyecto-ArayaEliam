<?php

namespace App;

use App\Models\User;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

enum IdeaStatus: string
{
    case Pending = 'pending';
    case InProgress = 'in_progress';
    case Completed = 'completed';

    public function label(): string
    {
        return match ($this) {
            IdeaStatus::Pending => 'Pending',
            IdeaStatus::InProgress => 'In Progress',
            IdeaStatus::Completed => 'Completed',
        };
    }
}
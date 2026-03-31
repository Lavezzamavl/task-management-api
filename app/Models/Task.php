<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    use HasFactory;

    protected $fillable = ['title', 'due_date', 'priority', 'status'];

    protected $casts = ['due_date' => 'date'];

    public static array $statusTransitions = [
        'pending'     => 'in_progress',
        'in_progress' => 'done',
    ];

    public function canTransitionTo(string $newStatus): bool
    {
        return isset(self::$statusTransitions[$this->status])
            && self::$statusTransitions[$this->status] === $newStatus;
    }

    public function scopeFilterByStatus($query, ?string $status): mixed
    {
        if ($status) {
            $query->where('status', $status);
        }
        return $query;
    }

    public function scopeSortByPriorityAndDueDate($query): mixed
    {
        return $query
            ->orderByRaw("FIELD(priority, 'high', 'medium', 'low')")
            ->orderBy('due_date', 'asc');
    }

    public function scopeForDate($query, string $date): mixed
    {
        return $query->whereDate('due_date', $date);
    }
}